(function () {
	'use strict';

	var cfg = window.__SUPPORT_ADMIN_CHAT__ || {};
	var messagesEl = document.getElementById('support-admin-messages');
	var form = document.getElementById('support-admin-form');
	var input = document.getElementById('support-admin-input');
	var statusEl = document.getElementById('support-admin-status');
	var takeBtn = document.getElementById('support-admin-take');
	var endBtn = document.getElementById('support-admin-end');
	var returnAiBtn = document.getElementById('support-admin-return-ai');

	var lastMessageId = cfg.lastMessageId || 0;
	var pollTimer = null;

	function escapeHtml(str) {
		var d = document.createElement('div');
		d.textContent = str == null ? '' : String(str);
		return d.innerHTML;
	}

	function appendMsg(m) {
		if (!m || !m.content) return;
		if (m.id && document.querySelector('[data-msg-id="' + m.id + '"]')) return;

		var sender = m.sender_type || 'ai';
		var el = document.createElement('div');
		el.className = 'support-admin-msg support-admin-msg--' + sender;
		if (m.id) el.setAttribute('data-msg-id', m.id);
		el.innerHTML = '<div class="support-admin-msg__bubble">' + escapeHtml(m.content).replace(/\n/g, '<br>') + '</div>'
			+ '<div class="support-admin-msg__meta small text-muted">' + escapeHtml(sender) + '</div>';
		messagesEl.appendChild(el);
		messagesEl.scrollTop = messagesEl.scrollHeight;
		if (m.id && m.id > lastMessageId) lastMessageId = m.id;
	}

	function pollOnce() {
		if (!cfg.pollUrl || !cfg.conversationId) return;
		var url = cfg.pollUrl + '?conversation_id=' + cfg.conversationId + '&after_id=' + lastMessageId;
		fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (!data || !data.ok) return;
				if (data.conversation && data.conversation.status && statusEl) {
					statusEl.textContent = data.conversation.status;
					cfg.status = data.conversation.status;
				}
				if (Array.isArray(data.messages)) {
					data.messages.forEach(appendMsg);
				}
			})
			.catch(function () {});
	}

	function startPoll() {
		stopPoll();
		pollTimer = window.setInterval(pollOnce, cfg.pollIntervalMs || 1500);
	}

	function stopPoll() {
		if (pollTimer) { window.clearInterval(pollTimer); pollTimer = null; }
	}

	function postJson(url, body) {
		return fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: body,
		}).then(function (r) { return r.json(); });
	}

	function takeConversation(done) {
		return fetch(cfg.takeUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data && data.ok) {
					if (statusEl) statusEl.textContent = data.status;
					cfg.status = data.status;
					if (takeBtn) takeBtn.disabled = true;
				}
				if (typeof done === 'function') {
					done(data);
				}
				return data;
			});
	}

	function sendStaffMessage(text) {
		return postJson(cfg.sendUrl, 'conversation_id=' + encodeURIComponent(cfg.conversationId) + '&message=' + encodeURIComponent(text))
			.then(function (data) {
				if (data && data.ok) {
					appendMsg({ id: data.message_id, sender_type: 'staff', content: data.content, created: data.created });
					if (statusEl) statusEl.textContent = 'staff_joined';
					cfg.status = 'staff_joined';
					if (takeBtn) takeBtn.disabled = true;
					if (input) input.value = '';
				} else {
					alert((data && data.message) ? data.message : 'Không gửi được tin nhắn.');
				}
				return data;
			});
	}

	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var text = input ? input.value.trim() : '';
			if (!text) return;

			var needsTake = cfg.status === 'waiting_staff' || cfg.status === 'ai_active' || cfg.status === 'handed_off' || cfg.status === 'open';
			if (needsTake && takeBtn && !takeBtn.disabled) {
				takeConversation(function (takeData) {
					if (takeData && takeData.ok) {
						sendStaffMessage(text);
					} else {
						alert((takeData && takeData.message) ? takeData.message : 'Không nhận được hội thoại.');
					}
				});
				return;
			}

			sendStaffMessage(text);
		});
	}

	if (takeBtn) {
		takeBtn.addEventListener('click', function () {
			takeConversation(function (data) {
				if (data && data.ok) {
					pollOnce();
				} else {
					alert((data && data.message) ? data.message : 'Không nhận được hội thoại.');
				}
			});
		});
	}

	if (endBtn) {
		endBtn.addEventListener('click', function () {
			if (!confirm('Kết thúc cuộc trò chuyện?')) return;
			fetch(cfg.endUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					if (data && data.ok) {
						if (statusEl) statusEl.textContent = data.status;
						pollOnce();
					}
				});
		});
	}

	if (returnAiBtn) {
		returnAiBtn.addEventListener('click', function () {
			if (!confirm('Trả lại AI xử lý?')) return;
			fetch(cfg.returnAiUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					if (data && data.ok) {
						if (statusEl) statusEl.textContent = data.status;
						cfg.status = data.status;
						takeBtn.disabled = false;
						pollOnce();
					}
				});
		});
	}

	startPoll();
})();
