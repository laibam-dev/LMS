// DEFAULT CHART.JS SETTINGS
// Use this for all charts in admin dashboard

function renderBarChart(elementId, labels, data, labelText) {
    const ctx = document.getElementById(elementId);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: labelText,
                data: data,
                backgroundColor: [
                    '#4f46e5',
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function renderPieChart(elementId, labels, data) {
    const ctx = document.getElementById(elementId);
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#4f46e5',
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b'
                ]
            }]
        },
        options: { responsive: true }
    });
}
