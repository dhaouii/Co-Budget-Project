<?php
/**
 * Tableau de bord principal
 * Affiche : 4 stat-cards, 2 graphiques, 5 dernières transactions
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/format_helper.php';

requireLogin();
$userId = getCurrentUserId();

$pageTitle = 'Tableau de bord';
$customCSS = 'dashboard.css';
$customJS = 'charts.js';

// Filtres période
$filterYear = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$filterMonth = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$filterBudget = isset($_GET['budget']) && $_GET['budget'] !== '' ? intval($_GET['budget']) : null;

// Construire les conditions WHERE pour les filtres
$dateConditions = '';
$dateParams = [];
if ($filterYear) {
    $dateConditions .= ' AND YEAR(date_transaction) = ?';
    $dateParams[] = $filterYear;
}
if ($filterMonth) {
    $dateConditions .= ' AND MONTH(date_transaction) = ?';
    $dateParams[] = $filterMonth;
}
if ($filterBudget) {
    $dateConditions .= ' AND id_budget = ?';
    $dateParams[] = $filterBudget;
}

// Récupère les budgets pour le filtre (propres + partagés)
try {
    $budgetsListStmt = $pdo->prepare(
        'SELECT DISTINCT b.id, b.nom FROM budgets b
         LEFT JOIN budget_membres bm ON bm.id_budget = b.id
         WHERE b.id_createur = ? OR bm.id_utilisateur = ?
         ORDER BY b.nom'
    );
    $budgetsListStmt->execute([$userId, $userId]);
    $userBudgets = $budgetsListStmt->fetchAll();
} catch (PDOException $e) {
    $userBudgets = [];
}

// Condition pour inclure transactions partagées
$sharedCondition = ' (id_utilisateur = ? OR id_budget IN (
    SELECT id FROM budgets WHERE id_createur = ?
    UNION
    SELECT id_budget FROM budget_membres WHERE id_utilisateur = ?
))';
$sharedParams = [$userId, $userId, $userId];

try {
    // Total revenus
    $incomeStmt = $pdo->prepare('SELECT SUM(montant) as total FROM transactions WHERE ' . $sharedCondition . ' AND type = "revenu"' . $dateConditions);
    $incomeStmt->execute(array_merge($sharedParams, $dateParams));
    $totalIncome = $incomeStmt->fetch()['total'] ?? 0;

    // Total dépenses
    $expenseStmt = $pdo->prepare('SELECT SUM(montant) as total FROM transactions WHERE ' . $sharedCondition . ' AND type = "depense"' . $dateConditions);
    $expenseStmt->execute(array_merge($sharedParams, $dateParams));
    $totalExpense = $expenseStmt->fetch()['total'] ?? 0;

    // Solde
    $balance = $totalIncome - $totalExpense;

    // Nombre de budgets
    $budgetQuery = 'SELECT COUNT(*) as count FROM budgets b
                    WHERE b.id_createur = ? OR EXISTS (
                        SELECT 1 FROM budget_membres bm WHERE bm.id_budget = b.id AND bm.id_utilisateur = ?
                    )';
    $budgetStmt = $pdo->prepare($budgetQuery);
    $budgetStmt->execute([$userId, $userId]);
    $budgetCount = $budgetStmt->fetch()['count'] ?? 0;

    // 5 dernières transactions (incluant transactions des budgets partagés)
    $transStmt = $pdo->prepare(
        'SELECT t.*, c.nom as categorie, u.prenom as user_prenom
         FROM transactions t
         LEFT JOIN categories c ON t.id_categorie = c.id
         LEFT JOIN utilisateurs u ON t.id_utilisateur = u.id
         WHERE (
             t.id_utilisateur = ?
             OR t.id_budget IN (
                 SELECT id FROM budgets WHERE id_createur = ?
                 UNION
                 SELECT id_budget FROM budget_membres WHERE id_utilisateur = ?
             )
         )
         ORDER BY t.date_transaction DESC
         LIMIT 5'
    );
    $transStmt->execute([$userId, $userId, $userId]);
    $recentTransactions = $transStmt->fetchAll();
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $totalIncome = 0;
    $totalExpense = 0;
    $balance = 0;
    $budgetCount = 0;
    $recentTransactions = [];
}

require_once '../../views/layouts/header.php';
?>

<?php if (isGuest()): ?>
    <div style="
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border: 1px solid #F59E0B;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: 16px;
    ">
        <i data-lucide="eye" style="width: 24px; height: 24px; color: #92400E;"></i>
        <div style="flex: 1;">
            <strong style="color: #92400E; display: block; margin-bottom: 4px;">Mode Visiteur - Lecture seule</strong>
            <p style="margin: 0; color: #78350F; font-size: 14px;">
                Vous explorez l'application en mode démo. Pour ajouter/modifier des données,
                <a href="<?php echo BASE_URL; ?>/modules/auth/register.php" style="color: #92400E; font-weight: 600; text-decoration: underline;">créez un compte gratuitement</a>.
            </p>
        </div>
    </div>
<?php endif; ?>

<div class="dashboard-header">
    <h1>Bienvenue, <?php echo htmlspecialchars(getCurrentUser()['prenom']); ?> !</h1>
    <p style="color: var(--color-text-secondary); margin: 0 0 var(--spacing-lg) 0;">Voici un aperçu de votre situation financière</p>

    <!-- Filtres période -->
    <div class="card" style="padding: var(--spacing-base);">
        <form method="GET" style="display: flex; gap: var(--spacing-base); align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 0 1 180px;">
                <label for="year" style="font-size: 13px;">Année</label>
                <select id="year" name="year" onchange="this.form.submit()">
                    <option value="">Toutes les années</option>
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filterYear == $y ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 0 1 180px;">
                <label for="month" style="font-size: 13px;">Mois</label>
                <select id="month" name="month" onchange="this.form.submit()">
                    <option value="">Tous les mois</option>
                    <?php
                    $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                    foreach ($months as $idx => $m): $monthNum = $idx + 1; ?>
                        <option value="<?php echo $monthNum; ?>" <?php echo $filterMonth == $monthNum ? 'selected' : ''; ?>>
                            <?php echo $m; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 0 1 220px;">
                <label for="budget" style="font-size: 13px;">Projet (Budget)</label>
                <select id="budget" name="budget" onchange="this.form.submit()">
                    <option value="">Tous les projets</option>
                    <?php foreach ($userBudgets as $bud): ?>
                        <option value="<?php echo $bud['id']; ?>" <?php echo $filterBudget == $bud['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($bud['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($filterYear || $filterMonth || $filterBudget): ?>
                <a href="<?php echo BASE_URL; ?>/modules/dashboard/index.php" class="btn btn-secondary btn-sm">
                    Réinitialiser
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Cartes statistiques -->
<div class="dashboard-grid">
    <?php
    $label = 'Solde';
    $value = formatNumber($balance);
    $icon = 'wallet';
    $variation = 0;
    $subtitle = 'Disponible';
    $hideCurrency = false;
    require '../../views/partials/stat_card.php';
    ?>

    <?php
    $label = 'Revenus';
    $value = formatNumber($totalIncome);
    $icon = 'arrow-down-left';
    $variation = 0;
    $currency = 'TND';
    $subtitle = '';
    $hideCurrency = false;
    require '../../views/partials/stat_card.php';
    ?>

    <?php
    $label = 'Dépenses';
    $value = formatNumber($totalExpense);
    $icon = 'arrow-up-right';
    $variation = 0;
    $subtitle = '';
    $hideCurrency = false;
    require '../../views/partials/stat_card.php';
    ?>

    <?php
    $label = 'Budgets actifs';
    $value = $budgetCount;
    $icon = 'target';
    $variation = 0;
    $hideCurrency = true;
    $subtitle = '';
    require '../../views/partials/stat_card.php';
    ?>
</div>

<!-- Graphiques -->
<div class="dashboard-charts" id="chartsContainer"
     data-year="<?php echo $filterYear ?? ''; ?>"
     data-month="<?php echo $filterMonth ?? ''; ?>"
     data-budget="<?php echo $filterBudget ?? ''; ?>">
    <!-- Donut Dépenses -->
    <div class="chart-container">
        <div class="chart-title">Dépenses par catégorie</div>
        <canvas id="pieChartContainer" style="height: 100%;"></canvas>
    </div>

    <!-- Donut Revenus -->
    <div class="chart-container">
        <div class="chart-title">Revenus par catégorie</div>
        <canvas id="incomeChartContainer" style="height: 100%;"></canvas>
    </div>
</div>

<!-- Graphique Évolution mensuelle (pleine largeur) -->
<div class="chart-container" style="margin-top: var(--spacing-lg);">
    <div class="chart-title">Évolution mensuelle</div>
    <canvas id="lineChartContainer" style="height: 100%;"></canvas>
</div>

<!-- Dernières transactions -->
<div class="recent-transactions">
    <h3>Transactions récentes</h3>

    <?php if (empty($recentTransactions)): ?>
        <div class="no-data">
            <p>Aucune transaction enregistrée.</p>
            <a href="<?php echo BASE_URL; ?>/modules/transactions/add.php" class="btn btn-primary" style="margin-top: var(--spacing-base);">
                Ajouter une transaction
            </a>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border);">
                    <th style="padding: var(--spacing-base); text-align: left; font-weight: 600;">Description</th>
                    <th style="padding: var(--spacing-base); text-align: right; font-weight: 600;">Montant</th>
                    <th style="padding: var(--spacing-base); text-align: right; font-weight: 600;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentTransactions as $transaction):
                    $showActions = FALSE;
                    require_once '../../views/partials/transaction_row.php';
                endforeach; ?>
            </tbody>
        </table>

        <a href="<?php echo BASE_URL; ?>/modules/transactions/list.php" style="
            display: block;
            margin-top: var(--spacing-base);
            text-align: center;
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 500;
        ">
            Voir toutes les transactions →
        </a>
    <?php endif; ?>
</div>

<!-- Charge Chart.js depuis CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<?php require_once '../../views/layouts/footer.php'; ?>
