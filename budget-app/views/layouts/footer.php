<?php
/**
 * Pied de page commun
 * Ferme les conteneurs ouverts dans header.php
 */
?>
        </main>
    </div>

    <!-- Footer -->
    <footer style="
        background-color: var(--color-surface);
        border-top: 1px solid var(--color-border);
        padding: var(--spacing-lg);
        text-align: center;
        font-size: 12px;
        color: var(--color-text-secondary);
        margin-top: auto;
    ">
        <div class="container">
            <p style="margin: 0;">
                &copy; 2025 <?php echo APP_NAME; ?>. Tous droits réservés.
            </p>
        </div>
    </footer>

    <!-- JS Global -->
    <script src="<?php echo BASE_URL; ?>/assets/js/global.js?v=<?php echo time(); ?>"></script>

    <!-- JS Spécifiques (à charger après) -->
    <?php if (isset($customJS)): ?>
        <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo $customJS; ?>?v=<?php echo time(); ?>"></script>
    <?php endif; ?>

    <!-- Event delegation pour TOUS les boutons de suppression -->
    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-delete-btn');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const url = btn.getAttribute('data-url');
            const message = btn.getAttribute('data-message') || 'Êtes-vous sûr ?';

            // Crée backdrop
            const backdrop = document.createElement('div');
            backdrop.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(17,24,39,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;backdrop-filter:blur(4px);';

            const modal = document.createElement('div');
            modal.style.cssText = 'background:white;border-radius:20px;padding:32px;max-width:420px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(17,24,39,0.2);';

            modal.innerHTML = `
                <div style="width:64px;height:64px;background:#FEE2E2;border-radius:18px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </div>
                <h3 style="margin:0 0 12px;font-size:20px;font-weight:700;color:#111827;">Confirmer la suppression</h3>
                <p style="margin:0 0 24px;font-size:14px;color:#6B7280;line-height:1.5;">${message}</p>
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button class="cc-cancel-btn" style="padding:12px 24px;border:1px solid #E5E7EB;background:white;border-radius:12px;font-weight:600;cursor:pointer;color:#374151;font-size:14px;">Annuler</button>
                    <button class="cc-confirm-btn" style="padding:12px 24px;background:linear-gradient(135deg,#EF4444,#DC2626);color:white;border:none;border-radius:12px;font-weight:600;cursor:pointer;font-size:14px;box-shadow:0 4px 12px rgba(239,68,68,0.3);">Supprimer</button>
                </div>
            `;

            backdrop.appendChild(modal);
            document.body.appendChild(backdrop);

            modal.querySelector('.cc-cancel-btn').addEventListener('click', function() { backdrop.remove(); });
            modal.querySelector('.cc-confirm-btn').addEventListener('click', function() { window.location.href = url; });
            backdrop.addEventListener('click', function(ev) { if (ev.target === backdrop) backdrop.remove(); });
        }, true);  // Use capture phase to intercept early
    </script>

    <!-- Override final pour confirmer les suppressions (priorité absolue) -->
    <script>
        // FORCE l'utilisation de notre modal customisée
        window.confirmDeleteAndNavigate = function(element, message) {
            const href = element.getAttribute('href');

            // Crée backdrop
            const backdrop = document.createElement('div');
            backdrop.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(17,24,39,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;backdrop-filter:blur(4px);';

            const modal = document.createElement('div');
            modal.style.cssText = 'background:white;border-radius:20px;padding:32px;max-width:420px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(17,24,39,0.2);';

            modal.innerHTML = `
                <div style="width:64px;height:64px;background:#FEE2E2;border-radius:18px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </div>
                <h3 style="margin:0 0 12px;font-size:20px;font-weight:700;color:#111827;">Confirmer la suppression</h3>
                <p style="margin:0 0 24px;font-size:14px;color:#6B7280;line-height:1.5;">${message || 'Êtes-vous sûr ?'}</p>
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button id="ccCancel" style="padding:12px 24px;border:1px solid #E5E7EB;background:white;border-radius:12px;font-weight:600;cursor:pointer;color:#374151;font-size:14px;">Annuler</button>
                    <button id="ccConfirm" style="padding:12px 24px;background:linear-gradient(135deg,#EF4444,#DC2626);color:white;border:none;border-radius:12px;font-weight:600;cursor:pointer;font-size:14px;box-shadow:0 4px 12px rgba(239,68,68,0.3);">Supprimer</button>
                </div>
            `;

            backdrop.appendChild(modal);
            document.body.appendChild(backdrop);

            document.getElementById('ccCancel').addEventListener('click', function() { backdrop.remove(); });
            document.getElementById('ccConfirm').addEventListener('click', function() { window.location.href = href; });
            backdrop.addEventListener('click', function(e) { if (e.target === backdrop) backdrop.remove(); });

            return false;
        };

        window.confirmDelete = function(message, callback) {
            const backdrop = document.createElement('div');
            backdrop.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(17,24,39,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;backdrop-filter:blur(4px);';

            const modal = document.createElement('div');
            modal.style.cssText = 'background:white;border-radius:20px;padding:32px;max-width:420px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(17,24,39,0.2);';

            modal.innerHTML = `
                <div style="width:64px;height:64px;background:#FEE2E2;border-radius:18px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </div>
                <h3 style="margin:0 0 12px;font-size:20px;font-weight:700;color:#111827;">Confirmer</h3>
                <p style="margin:0 0 24px;font-size:14px;color:#6B7280;line-height:1.5;">${message || 'Êtes-vous sûr ?'}</p>
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button id="ccCancel2" style="padding:12px 24px;border:1px solid #E5E7EB;background:white;border-radius:12px;font-weight:600;cursor:pointer;color:#374151;font-size:14px;">Annuler</button>
                    <button id="ccConfirm2" style="padding:12px 24px;background:linear-gradient(135deg,#EF4444,#DC2626);color:white;border:none;border-radius:12px;font-weight:600;cursor:pointer;font-size:14px;box-shadow:0 4px 12px rgba(239,68,68,0.3);">Confirmer</button>
                </div>
            `;

            backdrop.appendChild(modal);
            document.body.appendChild(backdrop);

            document.getElementById('ccCancel2').addEventListener('click', function() { backdrop.remove(); });
            document.getElementById('ccConfirm2').addEventListener('click', function() { backdrop.remove(); if (callback) callback(); });
            backdrop.addEventListener('click', function(e) { if (e.target === backdrop) backdrop.remove(); });

            return false;
        };
    </script>
</body>
</html>
