/**
 * Graphiques du tableau de bord
 * Utilise Chart.js - pie chart et line chart
 */

// URL de l'endpoint API
const API_URL = '/modules/dashboard/stats_api.php';

// Charge les données et initialise les graphiques
async function initializeDashboard() {
    try {
        // Récupère les filtres depuis le conteneur
        const container = document.getElementById('chartsContainer');
        const params = new URLSearchParams();
        if (container) {
            const year = container.getAttribute('data-year');
            const month = container.getAttribute('data-month');
            const budget = container.getAttribute('data-budget');
            if (year) params.append('year', year);
            if (month) params.append('month', month);
            if (budget) params.append('budget', budget);
        }

        // Cache buster pour éviter le cache du navigateur
        params.append('_t', Date.now());

        const url = API_URL + '?' + params.toString();
        const response = await fetch(url, {
            cache: 'no-store',
            headers: { 'Cache-Control': 'no-cache' }
        });
        if (!response.ok) throw new Error('Erreur lors du chargement des données');

        const data = await response.json();

        // Affiche les cartes statistiques
        updateStatCards(data);

        // Initialise les graphiques (ou affiche message si vide)
        if (data.categories && data.categories.length > 0) {
            renderPieChart(data.categories);
        } else {
            showEmptyChart('pieChartContainer', 'Aucune dépense pour cette période');
        }

        if (data.income_categories && data.income_categories.length > 0) {
            renderIncomeChart(data.income_categories);
        } else {
            showEmptyChart('incomeChartContainer', 'Aucun revenu pour cette période');
        }

        if (data.evolution && data.evolution.length > 0) {
            renderLineChart(data.evolution);
        } else {
            showEmptyChart('lineChartContainer', 'Aucune donnée pour cette période');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Impossible de charger les données du tableau de bord');
    }
}

// Met à jour les cartes statistiques
function updateStatCards(data) {
    // À implémenter si les cartes sont dynamiques côté JS
    // Sinon, elles sont statiques en HTML
}

// Plugin pour afficher le texte au centre du donut
const centerTextPlugin = {
    id: 'centerText',
    afterDraw: function(chart) {
        if (chart.config.options.centerText) {
            const { ctx, chartArea: { left, right, top, bottom } } = chart;
            const centerX = (left + right) / 2;
            const centerY = (top + bottom) / 2;
            const text = chart.config.options.centerText.text;
            const subText = chart.config.options.centerText.subText;
            const color = chart.config.options.centerText.color || '#1D1D1F';

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Texte principal
            ctx.font = 'bold 20px Inter, sans-serif';
            ctx.fillStyle = color;
            ctx.fillText(text, centerX, centerY - 8);

            // Sous-texte (montant total)
            if (subText) {
                ctx.font = '13px Inter, sans-serif';
                ctx.fillStyle = '#6B7280';
                ctx.fillText(subText, centerX, centerY + 14);
            }

            ctx.restore();
        }
    }
};

// Graphique en camembert - Répartition par catégorie
function renderPieChart(categories) {
    const chartContainer = document.getElementById('pieChartContainer');
    if (!chartContainer) return;

    // Détruit l'instance existante pour éviter les conflits
    const existingChart = Chart.getChart(chartContainer);
    if (existingChart) existingChart.destroy();

    const ctx = chartContainer.getContext('2d');
    if (!ctx) {
        // Canvas pas trouvé ou pas de context
        return;
    }

    // Calcule le total des dépenses
    const total = categories.reduce((sum, c) => sum + c.value, 0);

    // Utilise les couleurs des catégories
    const colors = categories.map(c => c.color || '#9CA3AF');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories.map(c => c.label),
            datasets: [{
                data: categories.map(c => c.value),
                backgroundColor: colors,
                borderColor: '#FFFFFF',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            layout: {
                padding: 10
            },
            centerText: {
                text: 'Dépenses',
                subText: total.toLocaleString('fr-FR') + ' TND',
                color: '#EF4444'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    maxHeight: 50,
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        padding: 8,
                        color: '#6B7280',
                        boxWidth: 10,
                        boxHeight: 10
                    }
                }
            }
        },
        plugins: [centerTextPlugin]
    });
}

// Graphique en camembert - Revenus par catégorie
function renderIncomeChart(categories) {
    const chartContainer = document.getElementById('incomeChartContainer');
    if (!chartContainer) return;

    // Détruit l'instance existante pour éviter les conflits
    const existingChart = Chart.getChart(chartContainer);
    if (existingChart) existingChart.destroy();

    const ctx = chartContainer.getContext('2d');
    if (!ctx) return;

    // Calcule le total des revenus
    const total = categories.reduce((sum, c) => sum + c.value, 0);

    // Utilise les couleurs des catégories
    const colors = categories.map(c => c.color || '#9CA3AF');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories.map(c => c.label),
            datasets: [{
                data: categories.map(c => c.value),
                backgroundColor: colors,
                borderColor: '#FFFFFF',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            layout: {
                padding: 10
            },
            centerText: {
                text: 'Revenus',
                subText: total.toLocaleString('fr-FR') + ' TND',
                color: '#10B981'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    maxHeight: 50,
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        padding: 8,
                        color: '#6B7280',
                        boxWidth: 10,
                        boxHeight: 10
                    }
                }
            }
        },
        plugins: [centerTextPlugin]
    });
}

// Graphique en barres - Évolution mensuelle
function renderLineChart(evolution) {
    const chartContainer = document.getElementById('lineChartContainer');
    if (!chartContainer) return;

    // Détruit l'instance existante pour éviter les conflits
    const existingChart = Chart.getChart(chartContainer);
    if (existingChart) existingChart.destroy();

    const ctx = chartContainer.getContext('2d');
    if (!ctx) {
        return;
    }

    // Filtre les mois qui ont des données (income > 0 ou expense > 0)
    const filteredEvolution = evolution.filter(e => e.income > 0 || e.expense > 0);
    const dataToUse = filteredEvolution.length > 0 ? filteredEvolution : evolution;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataToUse.map(e => formatMonthLabel(e.label)),
            datasets: [
                {
                    label: 'Revenus',
                    data: dataToUse.map(e => e.income),
                    backgroundColor: '#4338CA',
                    borderColor: '#4338CA',
                    borderWidth: 0,
                    borderRadius: 12,
                    barPercentage: 0.55,
                    categoryPercentage: 0.65
                },
                {
                    label: 'Dépenses',
                    data: dataToUse.map(e => e.expense),
                    backgroundColor: '#FF8A8A',
                    borderColor: '#FF8A8A',
                    borderWidth: 0,
                    borderRadius: 12,
                    barPercentage: 0.55,
                    categoryPercentage: 0.65
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 13
                        },
                        usePointStyle: true,
                        padding: 15,
                        color: '#6B7280'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            return context.dataset.label + ': ' + value.toLocaleString('fr-FR') + ' TND';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        color: '#9CA3AF',
                        callback: function(value) {
                            return value.toLocaleString('fr-FR');
                        }
                    },
                    grid: {
                        color: '#E5E7EB'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        color: '#9CA3AF'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Formate un label de mois (YYYY-MM → "juil 2025")
function formatMonthLabel(monthString) {
    const months = ['jan', 'fév', 'mar', 'avr', 'mai', 'jun', 'jul', 'aoû', 'sep', 'oct', 'nov', 'déc'];
    const [year, month] = monthString.split('-');
    const monthIndex = parseInt(month) - 1;
    return `${months[monthIndex]} ${year.slice(2)}`;
}

// Affiche un message d'erreur
function showErrorMessage(message) {
    const container = document.getElementById('chartsContainer');
    if (container) {
        container.innerHTML = `<div class="alert alert-danger">${message}</div>`;
    }
}

// Affiche un message vide pour un canvas
function showEmptyChart(canvasId, message) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    // Détruit l'ancien chart si existant
    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();

    // Affiche le message dans le canvas
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#9CA3AF';
    ctx.font = '14px Inter, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(message, canvas.width / 2, canvas.height / 2);
}

// Initialise au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    initializeDashboard();
});
