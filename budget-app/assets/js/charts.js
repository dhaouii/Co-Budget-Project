/**
 * Graphiques du tableau de bord
 * Utilise Chart.js - pie chart et line chart
 */

// URL de l'endpoint API
const API_URL = '/modules/dashboard/stats_api.php';

// Charge les données et initialise les graphiques
async function initializeDashboard() {
    try {
        const response = await fetch(API_URL);
        if (!response.ok) throw new Error('Erreur lors du chargement des données');

        const data = await response.json();

        // Affiche les cartes statistiques
        updateStatCards(data);

        // Initialise les graphiques
        if (data.categories && data.categories.length > 0) {
            renderPieChart(data.categories);
        }

        if (data.evolution && data.evolution.length > 0) {
            renderLineChart(data.evolution);
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

// Graphique en camembert - Répartition par catégorie
function renderPieChart(categories) {
    const chartContainer = document.getElementById('pieChartContainer');
    if (!chartContainer) return;

    const ctx = chartContainer.getContext('2d');
    if (!ctx) {
        // Canvas pas trouvé ou pas de context
        return;
    }

    // Couleurs personnalisées
    const colors = [
        '#2DD4BF', '#0F9B8E', '#1B6B5D', '#F59E0B', '#EF4444',
        '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#F97316'
    ];

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
            layout: {
                padding: {
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        padding: 12,
                        color: '#6B7280',
                        boxWidth: 12
                    }
                }
            }
        }
    });
}

// Graphique en barres - Évolution mensuelle
function renderLineChart(evolution) {
    const chartContainer = document.getElementById('lineChartContainer');
    if (!chartContainer) return;

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
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10B981',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Dépenses',
                    data: dataToUse.map(e => e.expense),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: '#EF4444',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.6
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

// Initialise au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    initializeDashboard();
});
