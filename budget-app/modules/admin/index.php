<?php
/**
 * Tableau de bord administration
 * Statistiques globales : utilisateurs, budgets, transactions
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';

requireLogin();
requireRole(ROLE_ADMIN);

$pageTitle = 'Administration';
$customCSS = 'admin.css';

try {
    // Total utilisateurs
    $userStmt = $pdo->query('SELECT COUNT(*) as count FROM utilisateurs');
    $totalUsers = $userStmt->fetch()['count'] ?? 0;

    // Utilisateurs actifs
    $activeStmt = $pdo->query('SELECT COUNT(*) as count FROM utilisateurs WHERE statut = "actif"');
    $activeUsers = $activeStmt->fetch()['count'] ?? 0;

    // Utilisateurs inactifs (en attente)
    $inactiveStmt = $pdo->query('SELECT COUNT(*) as count FROM utilisateurs WHERE statut = "inactif"');
    $inactiveUsers = $inactiveStmt->fetch()['count'] ?? 0;

    // Total budgets
    $budgetStmt = $pdo->query('SELECT COUNT(*) as count FROM budgets');
    $totalBudgets = $budgetStmt->fetch()['count'] ?? 0;

    // Total transactions
    $transStmt = $pdo->query('SELECT COUNT(*) as count FROM transactions');
    $totalTransactions = $transStmt->fetch()['count'] ?? 0;

    // Transactions du jour
    $todayStmt = $pdo->query('SELECT COUNT(*) as count FROM transactions WHERE DATE(date_transaction) = CURDATE()');
    $todayTransactions = $todayStmt->fetch()['count'] ?? 0;

} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
}

require_once '../../views/layouts/header.php';
?>

<h1>Tableau de bord administrateur</h1>

<div class="admin-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-label">Utilisateurs totaux</div>
        <div class="admin-stat-value"><?php echo $totalUsers; ?></div>
        <p style="font-size: 12px; color: var(--color-text-secondary); margin: var(--spacing-sm) 0 0 0;">
            <?php echo $activeUsers; ?> actifs, <?php echo $inactiveUsers; ?> en attente
        </p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-label">Budgets actifs</div>
        <div class="admin-stat-value"><?php echo $totalBudgets; ?></div>
        <p style="font-size: 12px; color: var(--color-text-secondary); margin: var(--spacing-sm) 0 0 0;">
            Budgets créés dans le système
        </p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-label">Transactions</div>
        <div class="admin-stat-value"><?php echo $totalTransactions; ?></div>
        <p style="font-size: 12px; color: var(--color-text-secondary); margin: var(--spacing-sm) 0 0 0;">
            <?php echo $todayTransactions; ?> aujourd'hui
        </p>
    </div>
</div>

<div class="card">
    <h2 style="margin-top: 0;">Actions administration</h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-base);">
        <a href="<?php echo BASE_URL; ?>/modules/admin/users_list.php" class="card" style="
            text-decoration: none;
            color: inherit;
            padding: var(--spacing-lg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            transition: all 0.2s;
            cursor: pointer;
        " onmouseover="this.style.boxShadow='var(--shadow)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
            <div style="
                width: 48px;
                height: 48px;
                border-radius: 8px;
                background-color: rgba(45, 212, 191, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--color-accent);
                margin-bottom: var(--spacing-base);
            ">
                <i data-lucide="users" style="width: 24px; height: 24px;"></i>
            </div>
            <h3 style="margin: 0 0 var(--spacing-sm) 0; font-size: 16px;">Gestion des utilisateurs</h3>
            <p style="margin: 0; font-size: 13px; color: var(--color-text-secondary);">
                Valider, suspendre ou modifier les comptes
            </p>
        </a>

        <a href="<?php echo BASE_URL; ?>/modules/admin/users_list.php?filter=inactive" class="card" style="
            text-decoration: none;
            color: inherit;
            padding: var(--spacing-lg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            transition: all 0.2s;
            cursor: pointer;
        " onmouseover="this.style.boxShadow='var(--shadow)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
            <div style="
                width: 48px;
                height: 48px;
                border-radius: 8px;
                background-color: rgba(239, 68, 68, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--color-danger);
                margin-bottom: var(--spacing-base);
            ">
                <i data-lucide="alert-circle" style="width: 24px; height: 24px;"></i>
            </div>
            <h3 style="margin: 0 0 var(--spacing-sm) 0; font-size: 16px;">Comptes en attente</h3>
            <p style="margin: 0; font-size: 13px; color: var(--color-text-secondary);">
                <?php echo $inactiveUsers; ?> compte(s) à valider
            </p>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
