<?php
/**
 * En-tête HTML commun
 * Inclus : meta tags, CSS, navigation top bar
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

    <!-- CSS Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>

    <!-- CSS Global -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/global.css">

    <!-- CSS Spécifiques (à charger après) -->
    <?php if (isset($customCSS)): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo $customCSS; ?>">
    <?php endif; ?>

    <!-- Font Inter et DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Barre de navigation supérieure - Premium Design -->
    <nav style="
        background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding: 14px var(--spacing-lg);
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 8px rgba(30, 64, 175, 0.2);
    ">
        <div class="container flex-between">
            <!-- Logo / Titre -->
            <div>
                <a href="<?php echo BASE_URL; ?>/modules/dashboard/index.php" style="
                    font-size: 24px;
                    font-weight: 700;
                    font-family: var(--font-heading);
                    letter-spacing: -0.5px;
                    color: #FFFFFF;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                ">
                    <span style="
                        width: 40px;
                        height: 40px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: 700;
                        font-size: 16px;
                        backdrop-filter: blur(10px);
                    ">Co</span>
                    <?php echo APP_NAME; ?>
                </a>
            </div>

            <!-- Menu utilisateur - Premium -->
            <div class="flex-center gap-lg">
                <?php if ($user): ?>
                    <div style="font-size: 14px; color: rgba(255, 255, 255, 0.85);">
                        👋 Bienvenue <strong style="color: #FFFFFF;"><?php echo htmlspecialchars($user['prenom']); ?></strong>
                    </div>

                    <div style="width: 1px; height: 20px; background-color: rgba(255, 255, 255, 0.2);"></div>

                    <a href="<?php echo BASE_URL; ?>/modules/users/profile.php" title="Mon profil" style="
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.2);
                        color: white;
                        font-weight: 600;
                        font-size: 15px;
                        transition: var(--transition);
                        backdrop-filter: blur(10px);
                    " onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='scale(1.05)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='scale(1)'">
                        <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
                    </a>

                    <a href="<?php echo BASE_URL; ?>/modules/auth/logout.php" class="btn btn-secondary btn-sm">
                        Quitter
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/modules/auth/login.php" class="btn btn-primary">
                        Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Conteneur principal -->
    <div style="display: flex; min-height: calc(100vh - 60px);">
        <?php if ($user): ?>
            <!-- Sidebar navigateur -->
            <?php require_once __DIR__ . '/sidebar.php'; ?>
        <?php endif; ?>

        <!-- Contenu principal -->
        <main style="
            flex: 1;
            padding: var(--spacing-lg);
            overflow-y: auto;
        ">
