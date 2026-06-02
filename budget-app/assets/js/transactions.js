/**
 * JavaScript spécifique au module Transactions
 * Filtres dynamiques, confirmation suppression, validations
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialise les actions
    initDeleteButtons();
    initFilterButtons();
});

// Ajoute une confirmation sur les boutons suppression
function initDeleteButtons() {
    const deleteLinks = document.querySelectorAll('a[href*="/delete.php"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')) {
                e.preventDefault();
            }
        });
    });
}

// Filtre dynamique par type (sans rechargement)
function initFilterButtons() {
    const typeFilter = document.getElementById('type');
    if (typeFilter) {
        typeFilter.addEventListener('change', () => {
            // Optionnel : appliquer un filtre dynamique côté client
            // Pour l'instant, on laisse le formulaire se soumettre
        });
    }
}

// Valide le montant avant soumission
function validateTransactionForm() {
    const montant = document.getElementById('montant');
    const type = document.getElementById('type');

    if (!montant || !montant.value || parseFloat(montant.value) <= 0) {
        alert('Le montant doit être positif');
        return false;
    }

    if (!type || !type.value) {
        alert('Veuillez sélectionner un type');
        return false;
    }

    return true;
}

// Formate un montant en temps réel
function formatAmountInput(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('blur', () => {
            if (input.value) {
                const num = parseFloat(input.value);
                if (!isNaN(num)) {
                    input.value = num.toFixed(2);
                }
            }
        });
    }
}
