/**
 * Admin dashboard — filter khoảng ngày + Chart.js (doanh thu) + KPI loading skeleton
 */
(function () {
	'use strict';

	var customWrap = document.getElementById('admDashCustomRange');
	var periodInputs = document.querySelectorAll('.adm-dash-filter__pills input[name="period"]');
	var kpiRows = document.getElementById('admDashKpiRows');
	var filterForm = document.querySelector('.adm-dash-filter');

	function syncCustomRange() {
		if (!customWrap || !periodInputs.length) {
			return;
		}

		var showCustom = false;
		periodInputs.forEach(function (radio) {
			if (radio.checked && radio.value === 'custom') {
				showCustom = true;
			}
		});

		customWrap.hidden = !showCustom;

		if (showCustom) {
			var fromInput = customWrap.querySelector('input[name="from"]');
			if (fromInput) {
				fromInput.focus();
			}
		}
	}

	function showKpiLoading() {
		if (kpiRows) {
			kpiRows.classList.add('is-loading');
		}
	}

	if (customWrap && periodInputs.length) {
		periodInputs.forEach(function (radio) {
			radio.addEventListener('change', function () {
				syncCustomRange();
				if (radio.value !== 'custom') {
					showKpiLoading();
				}
			});
		});
		syncCustomRange();
	}

	if (filterForm) {
		filterForm.addEventListener('submit', showKpiLoading);
		filterForm.querySelectorAll('input[name="from"], input[name="to"]').forEach(function (input) {
			input.addEventListener('change', showKpiLoading);
		});
	}

	var cfg = window.admDashboardChart;
	if (!cfg || typeof Chart === 'undefined') {
		return;
	}

	var commonOpts = {
		responsive: true,
		maintainAspectRatio: false,
		plugins: {
			legend: { display: false },
			tooltip: {
				backgroundColor: 'rgba(15, 23, 42, 0.92)',
				padding: 10,
				cornerRadius: 8,
			},
		},
		scales: {
			x: {
				grid: { display: false },
				ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 },
			},
			y: {
				beginAtZero: true,
				grid: { color: 'rgba(148, 163, 184, 0.15)' },
			},
		},
	};

	var revCtx = document.getElementById('admChartRevenue');
	if (revCtx) {
		new Chart(revCtx, {
			type: 'line',
			data: {
				labels: cfg.labels,
				datasets: [{
					label: 'Doanh thu',
					data: cfg.revenue,
					borderColor: '#2563eb',
					backgroundColor: 'rgba(37, 99, 235, 0.12)',
					fill: true,
					tension: 0.35,
					borderWidth: 2,
					pointRadius: 2,
				}],
			},
			options: Object.assign({}, commonOpts, {
				plugins: Object.assign({}, commonOpts.plugins, {
					tooltip: {
						callbacks: {
							label: function (ctx) {
								var v = ctx.parsed.y || 0;
								return ' ' + v.toLocaleString('vi-VN') + ' ₫';
							},
						},
					},
				}),
			}),
		});
	}
})();
