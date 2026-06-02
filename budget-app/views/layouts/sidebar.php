<?php
/**
 * Barre latérale de navigation - Design Figma (Blanc/Gris)
 * Sidebar minimaliste avec fond blanc et texte gris
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';

$user = getCurrentUser();
$isAdmin = $user && $user['role'] === ROLE_ADMIN;
$currentPage = basename($_SERVER['REQUEST_URI']);
?>

<aside style="
    width: 260px;
    background: linear-gradient(180deg, #1E40AF 0%, #1E3A8A 100%);
    border-right: none;
    padding: var(--spacing-xl) var(--spacing-lg);
    overflow-y: auto;
    height: calc(100vh - 60px);
    position: sticky;
    top: 60px;
">
    <nav style="display: flex; flex-direction: column; gap: var(--spacing-sm);">

        <!-- Section Principale -->
        <div style="
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            padding: var(--spacing-lg) var(--spacing-sm);
            margin-bottom: var(--spacing-sm);
            letter-spacing: 0.5px;
        ">
            NAVIGATION
        </div>

        <!-- Dashboard -->
        <a href="<?php echo BASE_URL; ?>/modules/dashboard/index.php" style="
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: var(--radius);
            color: <?php echo strpos($currentPage, 'dashboard') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 600;
            background-color: <?php echo strpos($currentPage, 'dashboard') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?php echo strpos($currentPage, 'dashboard') !== false ? '#FFFFFF' : 'transparent'; ?>;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'dashboard') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
            <span style="
                width: 20px;
                height: 20px;
                background: currentColor;
                border-radius: 4px;
                display: inline-block;
                opacity: 0.4;
            "></span>
            Tableau de Bord
        </a>

        <!-- Transactions -->
        <a href="<?php echo BASE_URL; ?>/modules/transactions/list.php" style="
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: var(--radius);
            color: <?php echo strpos($currentPage, 'transactions') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 600;
            background-color: <?php echo strpos($currentPage, 'transactions') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?php echo strpos($currentPage, 'transactions') !== false ? '#FFFFFF' : 'transparent'; ?>;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'transactions') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
            <span style="
                width: 20px;
                height: 20px;
                background: currentColor;
                border-radius: 4px;
                display: inline-block;
                opacity: 0.4;
            "></span>
            Transactions
        </a>

        <!-- Budgets -->
        <a href="<?php echo BASE_URL; ?>/modules/budgets/list.php" style="
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: var(--radius);
            color: <?php echo strpos($currentPage, 'budgets') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 600;
            background-color: <?php echo strpos($currentPage, 'budgets') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?php echo strpos($currentPage, 'budgets') !== false ? '#FFFFFF' : 'transparent'; ?>;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'budgets') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
            <span style="
                width: 20px;
                height: 20px;
                background: currentColor;
                border-radius: 4px;
                display: inline-block;
                opacity: 0.4;
            "></span>
            Budgets
        </a>

        <!-- Catégories -->
        <a href="<?php echo BASE_URL; ?>/modules/categories/list.php" style="
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: var(--radius);
            color: <?php echo strpos($currentPage, 'categories') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 600;
            background-color: <?php echo strpos($currentPage, 'categories') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?php echo strpos($currentPage, 'categories') !== false ? '#FFFFFF' : 'transparent'; ?>;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'categories') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
            <span style="
                width: 20px;
                height: 20px;
                background: currentColor;
                border-radius: 4px;
                display: inline-block;
                opacity: 0.4;
            "></span>
            Catégories
        </a>

        <!-- Section Admin -->
        <?php if ($isAdmin): ?>
            <div style="
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                color: rgba(255, 255, 255, 0.6);
                padding: var(--spacing-lg) var(--spacing-sm);
                margin-top: var(--spacing-lg);
                margin-bottom: var(--spacing-sm);
                letter-spacing: 0.5px;
            ">
                ADMINISTRATION
            </div>

            <!-- Admin Dashboard -->
            <a href="<?php echo BASE_URL; ?>/modules/admin/index.php" style="
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 10px 14px;
                border-radius: var(--radius);
                color: <?php echo strpos($currentPage, 'admin/index') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
                transition: all 0.3s;
                font-size: 15px;
                font-weight: 600;
                background-color: <?php echo strpos($currentPage, 'admin/index') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
                border-left: 3px solid <?php echo strpos($currentPage, 'admin/index') !== false ? '#FFFFFF' : 'transparent'; ?>;
            " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'admin/index') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
                <span style="
                    width: 20px;
                    height: 20px;
                    background: currentColor;
                    border-radius: 4px;
                    display: inline-block;
                    opacity: 0.4;
                "></span>
                Gestion système
            </a>

            <!-- Users Management -->
            <a href="<?php echo BASE_URL; ?>/modules/admin/users_list.php" style="
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 10px 14px;
                border-radius: var(--radius);
                color: <?php echo strpos($currentPage, 'users_list') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
                transition: all 0.3s;
                font-size: 15px;
                font-weight: 600;
                background-color: <?php echo strpos($currentPage, 'users_list') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
                border-left: 3px solid <?php echo strpos($currentPage, 'users_list') !== false ? '#FFFFFF' : 'transparent'; ?>;
            " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'users_list') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
                <span style="
                    width: 20px;
                    height: 20px;
                    background: currentColor;
                    border-radius: 4px;
                    display: inline-block;
                    opacity: 0.4;
                "></span>
                Utilisateurs
            </a>
        <?php endif; ?>

        <!-- Section Compte -->
        <div style="
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            padding: var(--spacing-lg) var(--spacing-sm);
            margin-top: var(--spacing-lg);
            margin-bottom: var(--spacing-sm);
            letter-spacing: 0.5px;
        ">
            COMPTE
        </div>

        <!-- Mon Profil -->
        <a href="<?php echo BASE_URL; ?>/modules/users/profile.php" style="
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: var(--radius);
            color: <?php echo strpos($currentPage, 'profile') !== false ? '#FFFFFF' : 'rgba(255, 255, 255, 0.85)'; ?>;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 600;
            background-color: <?php echo strpos($currentPage, 'profile') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>;
            border-left: 3px solid <?php echo strpos($currentPage, 'profile') !== false ? '#FFFFFF' : 'transparent'; ?>;
        " onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.transform='translateX(2px)'" onmouseout="this.style.backgroundColor='<?php echo strpos($currentPage, 'profile') !== false ? 'rgba(255, 255, 255, 0.2)' : 'transparent'; ?>'; this.style.transform='translateX(0)'">
            <span style="
                width: 20px;
                height: 20px;
                background: currentColor;
                border-radius: 4px;
                display: inline-block;
                opacity: 0.4;
            "></span>
            Paramètres
        </a>
    </nav>
</aside>
