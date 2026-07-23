(function () {
	'use strict';

	function initProductImageManager(root) {
		if (!root || root.getAttribute('data-adm-img-init') === '1') {
			return;
		}
		root.setAttribute('data-adm-img-init', '1');

		var uploadBase = root.getAttribute('data-upload-base') || '';
		var mainInput = root.querySelector('#product_image_main');
		var listInput = root.querySelector('#product_image_list');
		var removeContainer = root.querySelector('#product_images_remove_container');
		var zoneMain = root.querySelector('[data-zone="main"]');
		var zoneGallery = root.querySelector('[data-zone="gallery"]');

		if (!mainInput || !listInput || !zoneMain || !zoneGallery) {
			return;
		}

		var removed = {};
		var dragFilename = null;

		function escapeFilename(filename) {
			if (window.CSS && typeof window.CSS.escape === 'function') {
				return window.CSS.escape(filename);
			}
			return String(filename).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
		}

		function parseList() {
			try {
				var parsed = JSON.parse(listInput.value || '[]');
				return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
			} catch (e) {
				return [];
			}
		}

		function syncHidden() {
			var mainCard = zoneMain.querySelector('.adm-img-card[data-filename]');
			mainInput.value = mainCard ? mainCard.getAttribute('data-filename') : '';

			var gallery = [];
			zoneGallery.querySelectorAll('.adm-img-card[data-filename]').forEach(function (card) {
				gallery.push(card.getAttribute('data-filename'));
			});
			listInput.value = JSON.stringify(gallery);

			removeContainer.innerHTML = '';
			Object.keys(removed).forEach(function (filename) {
				var input = document.createElement('input');
				input.type = 'hidden';
				input.name = 'product_images_remove[]';
				input.value = filename;
				removeContainer.appendChild(input);
			});
		}

		function markRemoved(filename) {
			if (!filename) {
				return;
			}
			removed[filename] = true;
		}

		function createCard(filename, zone) {
			var card = document.createElement('div');
			card.className = 'adm-img-card';
			card.draggable = true;
			card.setAttribute('data-filename', filename);

			var img = document.createElement('img');
			img.src = uploadBase + filename;
			img.alt = '';
			card.appendChild(img);

			if (zone === 'main') {
				var badge = document.createElement('span');
				badge.className = 'adm-img-card__badge';
				badge.textContent = 'Chính';
				card.appendChild(badge);
			}

			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'adm-img-card__delete';
			btn.setAttribute('aria-label', 'Xóa ảnh');
			btn.innerHTML = '&times;';
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				if (!confirm('Xóa ảnh này khỏi sản phẩm?')) {
					return;
				}
				markRemoved(filename);
				card.remove();
				updateEmptyStates();
				syncHidden();
			});
			card.appendChild(btn);

			card.addEventListener('dragstart', function (e) {
				dragFilename = filename;
				card.classList.add('is-dragging');
				e.dataTransfer.effectAllowed = 'move';
				e.dataTransfer.setData('text/plain', filename);
			});

			card.addEventListener('dragend', function () {
				card.classList.remove('is-dragging');
				dragFilename = null;
				zoneMain.classList.remove('is-dragover');
				zoneGallery.classList.remove('is-dragover');
			});

			return card;
		}

		function updateEmptyStates() {
			var mainEmpty = zoneMain.querySelector('.adm-img-zone__empty');
			var hasMain = zoneMain.querySelector('.adm-img-card[data-filename]');
			if (!hasMain && !mainEmpty) {
				mainEmpty = document.createElement('div');
				mainEmpty.className = 'adm-img-zone__empty';
				mainEmpty.textContent = 'Kéo thả ảnh vào đây làm ảnh chính';
				zoneMain.appendChild(mainEmpty);
			} else if (hasMain && mainEmpty) {
				mainEmpty.remove();
			}

			var galleryEmpty = zoneGallery.querySelector('.adm-img-zone__empty');
			var hasGallery = zoneGallery.querySelector('.adm-img-card[data-filename]');
			if (!hasGallery && !galleryEmpty) {
				galleryEmpty = document.createElement('div');
				galleryEmpty.className = 'adm-img-zone__empty';
				galleryEmpty.textContent = 'Kéo thả ảnh kèm theo vào đây';
				zoneGallery.appendChild(galleryEmpty);
			} else if (hasGallery && galleryEmpty) {
				galleryEmpty.remove();
			}
		}

		function setupDropZone(zoneEl, zoneName) {
			zoneEl.addEventListener('dragover', function (e) {
				e.preventDefault();
				zoneEl.classList.add('is-dragover');
				e.dataTransfer.dropEffect = 'move';
			});

			zoneEl.addEventListener('dragleave', function (e) {
				if (!zoneEl.contains(e.relatedTarget)) {
					zoneEl.classList.remove('is-dragover');
				}
			});

			zoneEl.addEventListener('drop', function (e) {
				e.preventDefault();
				zoneEl.classList.remove('is-dragover');

				var filename = dragFilename || e.dataTransfer.getData('text/plain');
				if (!filename) {
					return;
				}

				var sourceCard = root.querySelector('.adm-img-card[data-filename="' + escapeFilename(filename) + '"]');
				if (!sourceCard) {
					return;
				}

				if (zoneName === 'main') {
					var currentMain = zoneMain.querySelector('.adm-img-card[data-filename]');
					if (currentMain && currentMain !== sourceCard) {
						currentMain.querySelector('.adm-img-card__badge') && currentMain.querySelector('.adm-img-card__badge').remove();
						zoneGallery.insertBefore(currentMain, zoneGallery.firstChild);
					}
					sourceCard.querySelector('.adm-img-card__badge') && sourceCard.querySelector('.adm-img-card__badge').remove();
					if (!sourceCard.querySelector('.adm-img-card__badge')) {
						var badge = document.createElement('span');
						badge.className = 'adm-img-card__badge';
						badge.textContent = 'Chính';
						sourceCard.appendChild(badge);
					}
					zoneMain.appendChild(sourceCard);
				} else {
					sourceCard.querySelector('.adm-img-card__badge') && sourceCard.querySelector('.adm-img-card__badge').remove();
					var after = document.elementFromPoint(e.clientX, e.clientY);
					var targetCard = after && after.closest ? after.closest('.adm-img-card[data-filename]') : null;
					if (targetCard && zoneGallery.contains(targetCard) && targetCard !== sourceCard) {
						zoneGallery.insertBefore(sourceCard, targetCard);
					} else {
						zoneGallery.appendChild(sourceCard);
					}
				}

				updateEmptyStates();
				syncHidden();
			});
		}

		function renderInitial() {
			zoneMain.innerHTML = '';
			zoneGallery.innerHTML = '';

			var mainFile = mainInput.value;
			var list = parseList();

			if (mainFile) {
				zoneMain.appendChild(createCard(mainFile, 'main'));
			}

			list.forEach(function (filename) {
				if (filename === mainFile) {
					return;
				}
				zoneGallery.appendChild(createCard(filename, 'gallery'));
			});

			updateEmptyStates();
			syncHidden();
		}

		setupDropZone(zoneMain, 'main');
		setupDropZone(zoneGallery, 'gallery');
		renderInitial();

		root.closest('form').addEventListener('submit', syncHidden);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-adm-product-images]').forEach(initProductImageManager);
	});
})();
