<?php
/**
 * En-tête HTML - Premium Fintech Design
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';

initSession();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?><?php echo APP_NAME; ?></title>

    <!-- CSS Global -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/global.css?v=<?php echo time(); ?>">

    <!-- CSS Spécifiques -->
    <?php if (isset($customCSS)): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo $customCSS; ?>?v=<?php echo time(); ?>">
    <?php endif; ?>

    <!-- Force no cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        function initLucide() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            } else {
                setTimeout(initLucide, 100);
            }
        }
        document.addEventListener('DOMContentLoaded', initLucide);
        window.addEventListener('load', initLucide);

        // Modal personnalisée pour confirmer la suppression
        function showCustomConfirm(message, onConfirm) {
            // Supprime toute modal existante
            const existing = document.querySelector('.custom-confirm-backdrop');
            if (existing) existing.remove();

            const backdrop = document.createElement('div');
            backdrop.className = 'custom-confirm-backdrop';
            backdrop.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(17,24,39,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;backdrop-filter:blur(4px);';

            const modal = document.createElement('div');
            modal.style.cssText = 'background:white;border-radius:20px;padding:32px;max-width:420px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(17,24,39,0.2);animation:popIn 0.2s ease;';

            modal.innerHTML = `
                <div style="width:64px;height:64px;background:#FEE2E2;border-radius:18px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </div>
                <h3 style="margin:0 0 12px;font-size:20px;font-weight:700;color:#111827;">Confirmer la suppression</h3>
                <p style="margin:0 0 24px;font-size:14px;color:#6B7280;line-height:1.5;">${message}</p>
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button class="cc-cancel" style="padding:12px 24px;border:1px solid #E5E7EB;background:white;border-radius:12px;font-weight:600;cursor:pointer;color:#374151;font-size:14px;">Annuler</button>
                    <button class="cc-confirm" style="padding:12px 24px;background:linear-gradient(135deg,#EF4444,#DC2626);color:white;border:none;border-radius:12px;font-weight:600;cursor:pointer;font-size:14px;box-shadow:0 4px 12px rgba(239,68,68,0.3);">Supprimer</button>
                </div>
            `;

            backdrop.appendChild(modal);
            document.body.appendChild(backdrop);

            modal.querySelector('.cc-cancel').onclick = () => backdrop.remove();
            modal.querySelector('.cc-confirm').onclick = () => { backdrop.remove(); onConfirm(); };
            backdrop.onclick = (e) => { if (e.target === backdrop) backdrop.remove(); };
        }

        // Remplace la fonction confirmDeleteAndNavigate
        window.confirmDeleteAndNavigate = function(element, message) {
            showCustomConfirm(message || 'Êtes-vous sûr ?', () => {
                window.location.href = element.getAttribute('href');
            });
            return false;
        };

        // Remplace confirmDelete
        window.confirmDelete = function(message, callback) {
            showCustomConfirm(message || 'Êtes-vous sûr ?', () => {
                if (callback) callback();
            });
            return false;
        };

        // Ajoute l'animation
        const style = document.createElement('style');
        style.textContent = '@keyframes popIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}';
        document.head.appendChild(style);
    </script>
</head>
<body>
    <!-- Conteneur principal -->
    <div style="display: grid; grid-template-columns: <?php echo $user ? '260px 1fr' : '1fr'; ?>; min-height: 100vh; background-color: var(--color-bg);">
        <?php if ($user): ?>
            <!-- Sidebar -->
            <?php require_once __DIR__ . '/sidebar.php'; ?>
        <?php endif; ?>

        <!-- Zone principale -->
        <div style="display: flex; flex-direction: column; min-height: 100vh; min-width: 0; overflow-x: hidden;">

            <!-- Header propre clair -->
            <header style="
                background-color: var(--color-surface);
                border-bottom: 1px solid var(--color-border-light);
                padding: 16px var(--spacing-xl);
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 100;
            ">
                <!-- Titre de page -->
                <div>
                    <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: var(--color-text-primary);">
                        <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : APP_NAME; ?>
                    </h2>
                </div>

                <!-- Menu utilisateur -->
                <?php if ($user): ?>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
                                <?php echo htmlspecialchars($user['prenom']); ?> <?php echo htmlspecialchars($user['nom']); ?>
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-secondary);">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </div>

                        <a href="<?php echo BASE_URL; ?>/modules/users/profile.php" title="Mon profil" style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 42px;
                            height: 42px;
                            border-radius: 12px;
                            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
                            color: white;
                            font-weight: 700;
                            font-size: 16px;
                            transition: var(--transition);
                            box-shadow: var(--shadow-indigo);
                        " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
                        </a>

                        <a href="<?php echo BASE_URL; ?>/modules/auth/logout.php" class="btn btn-secondary btn-sm" title="Déconnexion">
                            Quitter
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/modules/auth/login.php" class="btn btn-primary">
                        Se connecter
                    </a>
                <?php endif; ?>
            </header>

            <!-- Contenu principal -->
            <main style="
                flex: 1;
                padding: var(--spacing-xl);
                overflow-y: auto;
                width: 100%;
                box-sizing: border-box;
            ">
