<?php
/**
 * Sidebar - Premium Fintech Design
 * Avec card balance en haut, navigation propre
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/format_helper.php';

$user = getCurrentUser();
$isAdmin = $user && $user['role'] === ROLE_ADMIN;
$currentPage = basename($_SERVER['REQUEST_URI']);
$currentPath = $_SERVER['REQUEST_URI'];

// Calcul du solde pour la card balance
$currentBalance = 0;
if ($user && !empty($user['id'])) {
    try {
        $userId = $user['id'];
        $stmt = $pdo->prepare(
            'SELECT
                COALESCE(SUM(CASE WHEN type = "revenu" THEN montant ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN type = "depense" THEN montant ELSE 0 END), 0) AS balance
             FROM transactions
             WHERE id_utilisateur = ? OR id_budget IN (
                 SELECT id FROM budgets WHERE id_createur = ?
                 UNION
                 SELECT id_budget FROM budget_membres WHERE id_utilisateur = ?
             )'
        );
        $stmt->execute([$userId, $userId, $userId]);
        $currentBalance = (float) $stmt->fetch()['balance'];
    } catch (PDOException $e) {
        $currentBalance = 0;
    }
}

function isActive($paths, $currentPath) {
    if (!is_array($paths)) $paths = [$paths];
    foreach ($paths as $path) {
        if (strpos($currentPath, $path) !== false) return true;
    }
    return false;
}
?>

<aside style="
    background: linear-gradient(180deg, #4338CA 0%, #3730A3 100%);
    padding: 24px 20px;
    overflow-y: auto;
    height: 100vh;
    position: sticky;
    top: 0;
    display: flex;
    flex-direction: column;
    gap: 24px;
    box-sizing: border-box;
">
    <!-- Logo / App name -->
    <div style="
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 20px 16px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    ">
        <!-- Logo SVG inline - grand -->
        <svg width="110" height="110" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#6366F1;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#8B5CF6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#A78BFA;stop-opacity:1" />
                </linearGradient>
            </defs>
            <!-- Cerveau / Arbre stylisé -->
            <g fill="url(#grad1)" stroke="url(#grad1)" stroke-width="1.5">
                <!-- Tronc -->
                <rect x="27" y="42" width="6" height="12" rx="2"/>
                <!-- Branches en cercles -->
                <circle cx="30" cy="20" r="5"/>
                <circle cx="20" cy="25" r="4"/>
                <circle cx="40" cy="25" r="4"/>
                <circle cx="18" cy="35" r="4"/>
                <circle cx="42" cy="35" r="4"/>
                <circle cx="25" cy="42" r="3"/>
                <circle cx="35" cy="42" r="3"/>
                <!-- Connexions -->
                <line x1="30" y1="20" x2="20" y2="25" stroke-width="2"/>
                <line x1="30" y1="20" x2="40" y2="25" stroke-width="2"/>
                <line x1="20" y1="25" x2="18" y2="35" stroke-width="2"/>
                <line x1="40" y1="25" x2="42" y2="35" stroke-width="2"/>
                <line x1="18" y1="35" x2="25" y2="42" stroke-width="2"/>
                <line x1="42" y1="35" x2="35" y2="42" stroke-width="2"/>
            </g>
        </svg>

        <!-- Texte Co Budget -->
        <div style="
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.8px;
        ">
            Co Budget
        </div>
    </div>

    <!-- Balance Card -->
    <div style="
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 20px;
        padding: 24px 20px;
        text-align: center;
    ">
        <div style="
            font-size: 28px;
            font-weight: 800;
            color: white;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        ">
            <?php echo number_format($currentBalance, 2, ',', ' '); ?>
        </div>
        <div style="
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        ">
            Solde actuel · TND
        </div>
    </div>

    <!-- Navigation -->
    <nav style="display: flex; flex-direction: column; gap: 4px; flex: 1;">

        <?php
        $isDashboard = isActive('dashboard', $currentPath);
        $isTransactions = isActive('transactions', $currentPath);
        $isBudgets = isActive('budgets', $currentPath);
        $isCategories = isActive('categories', $currentPath);
        $isAdminPage = isActive(['admin/index', 'users_list'], $currentPath);
        $isUsers = isActive('users_list', $currentPath);
        $isProfile = isActive('profile', $currentPath);

        $menuItems = [
            ['icon' => 'layout-dashboard', 'label' => 'Tableau de Bord', 'url' => BASE_URL . '/modules/dashboard/index.php', 'active' => $isDashboard],
            ['icon' => 'receipt', 'label' => 'Transactions', 'url' => BASE_URL . '/modules/transactions/list.php', 'active' => $isTransactions],
            ['icon' => 'wallet', 'label' => 'Budgets', 'url' => BASE_URL . '/modules/budgets/list.php', 'active' => $isBudgets],
            ['icon' => 'tag', 'label' => 'Catégories', 'url' => BASE_URL . '/modules/categories/list.php', 'active' => $isCategories],
        ];

        foreach ($menuItems as $item):
            $activeStyle = $item['active']
                ? 'background: rgba(255, 255, 255, 0.18); color: white;'
                : 'background: transparent; color: rgba(255, 255, 255, 0.75);';
        ?>
            <a href="<?php echo $item['url']; ?>" style="
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 14px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                transition: all 200ms ease;
                text-decoration: none;
                <?php echo $activeStyle; ?>
            " onmouseover="if(!this.dataset.active){this.style.background='rgba(255, 255, 255, 0.08)'; this.style.color='white';}" onmouseout="if(!this.dataset.active){this.style.background='transparent'; this.style.color='rgba(255, 255, 255, 0.75)';}" data-active="<?php echo $item['active'] ? '1' : ''; ?>">
                <i data-lucide="<?php echo $item['icon']; ?>" style="width: 18px; height: 18px;"></i>
                <?php echo $item['label']; ?>
            </a>
        <?php endforeach; ?>

        <?php if ($isAdmin): ?>
            <div style="
                margin-top: 20px;
                margin-bottom: 8px;
                padding: 0 14px;
                font-size: 11px;
                font-weight: 700;
                color: rgba(255, 255, 255, 0.5);
                text-transform: uppercase;
                letter-spacing: 1px;
            ">
                Admin
            </div>

            <?php
            $adminItems = [
                ['icon' => 'settings', 'label' => 'Gestion système', 'url' => BASE_URL . '/modules/admin/index.php', 'active' => $isAdminPage && !$isUsers],
                ['icon' => 'users', 'label' => 'Utilisateurs', 'url' => BASE_URL . '/modules/admin/users_list.php', 'active' => $isUsers],
            ];

            foreach ($adminItems as $item):
                $activeStyle = $item['active']
                    ? 'background: rgba(255, 255, 255, 0.18); color: white;'
                    : 'background: transparent; color: rgba(255, 255, 255, 0.75);';
            ?>
                <a href="<?php echo $item['url']; ?>" style="
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    padding: 12px 14px;
                    border-radius: 12px;
                    font-size: 14px;
                    font-weight: 600;
                    transition: all 200ms ease;
                    text-decoration: none;
                    <?php echo $activeStyle; ?>
                " onmouseover="if(!this.dataset.active){this.style.background='rgba(255, 255, 255, 0.08)'; this.style.color='white';}" onmouseout="if(!this.dataset.active){this.style.background='transparent'; this.style.color='rgba(255, 255, 255, 0.75)';}" data-active="<?php echo $item['active'] ? '1' : ''; ?>">
                    <i data-lucide="<?php echo $item['icon']; ?>" style="width: 18px; height: 18px;"></i>
                    <?php echo $item['label']; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Profile en bas -->
        <div style="margin-top: auto; padding-top: 20px;">
            <a href="<?php echo BASE_URL; ?>/modules/users/profile.php" style="
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 14px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                transition: all 200ms ease;
                text-decoration: none;
                background: <?php echo $isProfile ? 'rgba(255, 255, 255, 0.18)' : 'transparent'; ?>;
                color: <?php echo $isProfile ? 'white' : 'rgba(255, 255, 255, 0.75)'; ?>;
            " onmouseover="if(!this.dataset.active){this.style.background='rgba(255, 255, 255, 0.08)'; this.style.color='white';}" onmouseout="if(!this.dataset.active){this.style.background='transparent'; this.style.color='rgba(255, 255, 255, 0.75)';}" data-active="<?php echo $isProfile ? '1' : ''; ?>">
                <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                Mon Profil
            </a>
        </div>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
