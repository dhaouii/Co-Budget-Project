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
        gap: 8px;
        padding: 12px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    ">
        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Co Budget" style="
            width: 160px;
            height: auto;
            max-height: 110px;
            object-fit: contain;
        " onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div style="
            display: none;
            width: 40px;
            height: 40px;
            background: #4338CA;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 18px;
        ">Co</div>
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
