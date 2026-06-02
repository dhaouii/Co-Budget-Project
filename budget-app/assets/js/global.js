/**
 * Fonctions JavaScript partagées
 * Utilitaires globaux, gestion des alertes, confirmation suppression
 */

// Confirme une action avant suppression avec une belle modal
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer ?', callback = null) {
    // Crée le backdrop
    const backdrop = document.createElement('div');
    backdrop.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(2px);
    `;

    // Crée la modal
    const modal = document.createElement('div');
    modal.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
        max-width: 400px;
        text-align: center;
        animation: modalSlide 0.3s ease;
    `;

    modal.innerHTML = `
        <h3 style="
            margin: 0 0 16px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1D1D1F;
        ">⚠️ Confirmation</h3>
        <p style="
            margin: 0 0 24px 0;
            font-size: 14px;
            color: #86868B;
            line-height: 1.5;
        ">${message}</p>
        <div style="
            display: flex;
            gap: 12px;
            justify-content: center;
        ">
            <button id="cancelBtn" style="
                padding: 10px 24px;
                border: 1px solid #D2D2D7;
                background: white;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                color: #1D1D1F;
            ">Annuler</button>
            <button id="confirmBtn" style="
                padding: 10px 24px;
                background: linear-gradient(135deg, #FF3B30 0%, #FF1744 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                box-shadow: 0 2px 8px rgba(255, 59, 48, 0.3);
            ">Supprimer</button>
        </div>
    `;

    backdrop.appendChild(modal);
    document.body.appendChild(backdrop);

    const cleanup = () => backdrop.remove();

    document.getElementById('cancelBtn').addEventListener('click', () => {
        cleanup();
    });

    document.getElementById('confirmBtn').addEventListener('click', () => {
        cleanup();
        if (callback) callback();
    });

    backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) cleanup();
    });

    return false;
}

// Confirme avant de naviguer vers un lien
function confirmDeleteAndNavigate(element, message = 'Êtes-vous sûr de vouloir supprimer ?') {
    const href = element.getAttribute('href');

    // Crée le backdrop
    const backdrop = document.createElement('div');
    backdrop.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(2px);
    `;

    const modal = document.createElement('div');
    modal.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
        max-width: 400px;
        text-align: center;
    `;

    modal.innerHTML = `
        <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600; color: #1D1D1F;">⚠️ Confirmation</h3>
        <p style="margin: 0 0 24px 0; font-size: 14px; color: #86868B; line-height: 1.5;">${message}</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="cancelBtn" style="padding: 10px 24px; border: 1px solid #D2D2D7; background: white; border-radius: 8px; font-weight: 600; cursor: pointer; color: #1D1D1F;">Annuler</button>
            <button id="confirmBtn" style="padding: 10px 24px; background: linear-gradient(135deg, #FF3B30 0%, #FF1744 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 8px rgba(255, 59, 48, 0.3);">Supprimer</button>
        </div>
    `;

    backdrop.appendChild(modal);
    document.body.appendChild(backdrop);

    document.getElementById('cancelBtn').addEventListener('click', () => backdrop.remove());
    document.getElementById('confirmBtn').addEventListener('click', () => window.location.href = href);
    backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) backdrop.remove();
    });

    return false;
}

// Affiche une notification temporaire
function showNotification(message, type = 'success', duration = 3000) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: 400px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    `;

    const bgColor = type === 'success' ? '#ECFDF5' : type === 'error' ? '#FEE2E2' : '#EFF6FF';
    const textColor = type === 'success' ? '#065F46' : type === 'error' ? '#7F1D1D' : '#1E40AF';
    const borderColor = type === 'success' ? '#86EFAC' : type === 'error' ? '#FCA5A5' : '#BFDBFE';

    notification.style.backgroundColor = bgColor;
    notification.style.color = textColor;
    notification.style.border = `1px solid ${borderColor}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

// Valide un formulaire (checks requis et types)
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    let isValid = true;
    const inputs = form.querySelectorAll('[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#EF4444';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }

        // Validation email
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                input.style.borderColor = '#EF4444';
                isValid = false;
            }
        }

        // Validation montant
        if (input.classList.contains('amount-input') && input.value) {
            if (isNaN(input.value) || parseFloat(input.value) <= 0) {
                input.style.borderColor = '#EF4444';
                isValid = false;
            }
        }
    });

    return isValid;
}

// Formate un montant en EUR
function formatMoney(amount, currency = 'TND') {
    const num = parseFloat(amount);
    if (isNaN(num)) return '0.00 ' + currency;
    return num.toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' ' + currency;
}

// Fetch avec gestion d'erreur
async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('Erreur lors de la requête', 'error');
        return null;
    }
}

// Formate une date en français
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Ajoute des styles d'animation au document
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loading {
        animation: pulse 1s infinite;
    }
`;
document.head.appendChild(style);
