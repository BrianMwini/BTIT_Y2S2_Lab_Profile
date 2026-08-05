/**
 * =====================================================================
 * MPVS — Front-end behaviour
 * Sidebar toggle, Chart.js charts, verify loading state, password
 * visibility toggles, destructive-action confirmations.
 * =====================================================================
 */
(function () {
    'use strict';

    /* ---------------- Sidebar (mobile) ---------------- */
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
    }

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('show');
        });
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    /* ---------------- Flash alerts auto-dismiss ---------------- */
    document.querySelectorAll('.flash-container .alert').forEach(function (alert) {
        setTimeout(function () {
            const close = alert.querySelector('.btn-close');
            if (close) close.click();
        }, 6000);
    });

    /* ---------------- Password visibility ---------------- */
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.getAttribute('data-toggle-password'));
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    });

    /* ---------------- Form loading states (verify search + add transaction) ---------------- */
    document.querySelectorAll('form').forEach(function (form) {
        const btn = form.querySelector('button[type="submit"] .btn-loading');
        if (!btn) return; // only forms that expose .btn-normal / .btn-loading spans
        const submitBtn = btn.closest('button[type="submit"]');
        form.addEventListener('submit', function () {
            if (!submitBtn) return;
            const normal = submitBtn.querySelector('.btn-normal');
            if (normal) normal.classList.add('d-none');
            btn.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    });

    // Keep M-Pesa code fields uppercased + alphanumeric as the user types.
    document.querySelectorAll('.code-input').forEach(function (input) {
        input.addEventListener('input', function () {
            input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    });

    /* ---------------- Destructive-action confirmations ---------------- */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const question = form.getAttribute('data-confirm');
            if (!window.confirm(question)) {
                e.preventDefault();
            }
        });
    });

    /* ---------------- Chart.js ---------------- */
    function chartColor(hex, alpha) {
        // Convert #rrggbb to rgba() — handy for gradient fills.
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
    }

    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = 'Segoe UI, system-ui, sans-serif';
        Chart.defaults.color = '#64748b';

        /* Dashboard 7-day revenue area chart */
        const revenueCanvas = document.getElementById('revenueChart');
        if (revenueCanvas) {
            const labels = JSON.parse(revenueCanvas.dataset.labels || '[]');
            const revenue = JSON.parse(revenueCanvas.dataset.revenue || '[]');
            const verified = JSON.parse(revenueCanvas.dataset.verified || '[]');
            new Chart(revenueCanvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Verified payments',
                            data: verified,
                            borderColor: '#16a34a',
                            backgroundColor: chartColor('#16a34a', 0.15),
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            borderWidth: 2
                        },
                        {
                            label: 'Revenue (' + (window.MPVS_CURRENCY || 'KES') + ')',
                            data: revenue,
                            borderColor: '#0f7a4d',
                            backgroundColor: chartColor('#0f7a4d', 0.1),
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            borderWidth: 2,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        /* Reports daily trend bar chart */
        const trendCanvas = document.getElementById('trendChart');
        if (trendCanvas) {
            const labels = JSON.parse(trendCanvas.dataset.labels || '[]');
            const verified = JSON.parse(trendCanvas.dataset.verified || '[]');
            const failed = JSON.parse(trendCanvas.dataset.failed || '[]');
            new Chart(trendCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Verified',
                            data: verified,
                            backgroundColor: chartColor('#16a34a', 0.85),
                            borderRadius: 6
                        },
                        {
                            label: 'Failed',
                            data: failed,
                            backgroundColor: chartColor('#dc2626', 0.85),
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        /* Reports status distribution doughnut */
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            const verified = parseInt(statusCanvas.dataset.verified || '0', 10);
            const failed = parseInt(statusCanvas.dataset.failed || '0', 10);
            const pending = parseInt(statusCanvas.dataset.pending || '0', 10);
            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Verified', 'Failed', 'Pending'],
                    datasets: [{
                        data: [verified, failed, pending],
                        backgroundColor: ['#16a34a', '#dc2626', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.label + ': ' + ctx.parsed; } } }
                    }
                }
            });
        }
    }
})();
