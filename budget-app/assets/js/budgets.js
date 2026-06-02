/**
 * JavaScript spécifique au module Budgets
 * Affichage/masquage options partagé, validations
 */

function toggleSharedOptions() {
    const isShared = document.getElementById('est_partage');
    const sharedOptions = document.getElementById('sharedOptions');

    if (isShared) {
        sharedOptions.style.display = isShared.checked ? 'block' : 'none';
    }
}

function toggleDateEnd() {
    const periode = document.getElementById('periode');
    const dateEndGroup = document.getElementById('dateEndGroup');

    if (periode && dateEndGroup) {
        dateEndGroup.style.display = periode.value === 'personnalise' ? 'flex' : 'none';

        const dateEnd = document.getElementById('date_fin');
        if (dateEnd) {
            dateEnd.required = periode.value === 'personnalise';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    toggleSharedOptions();
    toggleDateEnd();

    const estPartageCheckbox = document.getElementById('est_partage');
    const periodeSelect = document.getElementById('periode');

    if (estPartageCheckbox) {
        estPartageCheckbox.addEventListener('change', toggleSharedOptions);
    }

    if (periodeSelect) {
        periodeSelect.addEventListener('change', toggleDateEnd);
    }
});
