import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

function formatRupiah(value) {
    const n = Number(value) || 0;
    return `Rp${new Intl.NumberFormat('id-ID').format(n)}`;
}

function parseChartData(el) {
    if (!el?.dataset?.chart) {
        return null;
    }

    try {
        return JSON.parse(el.dataset.chart);
    } catch {
        return null;
    }
}

function salesGradient(ctx, chartArea) {
    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    gradient.addColorStop(0, 'rgba(245, 158, 11, 0.35)');
    gradient.addColorStop(1, 'rgba(245, 158, 11, 0.02)');
    return gradient;
}

function initSalesTrendChart() {
    const canvas = document.getElementById('sales-trend-chart');
    const data = parseChartData(canvas);
    if (!canvas || !data?.labels?.length) {
        return;
    }

    const ctx = canvas.getContext('2d');
    let fillGradient = 'rgba(245, 158, 11, 0.15)';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Penjualan',
                    data: data.values,
                    borderColor: '#ea7d0a',
                    backgroundColor: (context) => {
                        const chart = context.chart;
                        const { chartArea } = chart;
                        if (!chartArea) {
                            return fillGradient;
                        }
                        if (!context.dataset._gradient) {
                            context.dataset._gradient = salesGradient(chart.ctx, chartArea);
                        }
                        return context.dataset._gradient;
                    },
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#ea7d0a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.38,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Poppins', size: 12, weight: '600' },
                    bodyFont: { family: 'Poppins', size: 13 },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => ` ${formatRupiah(ctx.parsed.y)}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'Poppins', size: 11 },
                        maxRotation: 0,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.15)' },
                    border: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'Poppins', size: 11 },
                        callback: (v) => {
                            const n = Number(v);
                            if (n >= 1_000_000) {
                                return `${(n / 1_000_000).toFixed(1)}jt`;
                            }
                            if (n >= 1_000) {
                                return `${(n / 1_000).toFixed(0)}rb`;
                            }
                            return n;
                        },
                    },
                },
            },
        },
    });
}

function initCostCompositionChart() {
    const canvas = document.getElementById('cost-composition-chart');
    const data = parseChartData(canvas);
    if (!canvas || !data?.values?.length || data.values.every((v) => v === 0)) {
        return;
    }

    new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [
                {
                    data: data.values,
                    backgroundColor: ['#0ea5e9', '#8b5cf6', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#64748b',
                        font: { family: 'Poppins', size: 11, weight: '600' },
                        padding: 14,
                        usePointStyle: true,
                        pointStyle: 'circle',
                    },
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    bodyFont: { family: 'Poppins', size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: (ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                            return ` ${ctx.label}: ${formatRupiah(ctx.parsed)} (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
}

export function initDashboardCharts() {
    initSalesTrendChart();
    initCostCompositionChart();
}
