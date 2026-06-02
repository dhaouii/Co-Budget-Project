<?php
/**
 * Liste des transactions de l'utilisateur
 * Avec filtres par période et catégorie
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';
require_once '../../helpers/format_helper.php';

requireLogin();
$userId = getCurrentUserId();

$pageTitle = 'Transactions';
$customCSS = 'transactions.css';
$customJS = 'transactions.js';

$success = isset($_GET['success']) && $_GET['success'] == 1;
$error = '';

// Récupère les filtres
$filterType = sanitize($_GET['type'] ?? '');
$filterCategory = sanitize($_GET['category'] ?? '');
$filterMonth = sanitize($_GET['month'] ?? '');
$filterYear = sanitize($_GET['year'] ?? '');
$filterBudget = sanitize($_GET['budget'] ?? '');

// Construire la requête
$query = 'SELECT t.*, c.nom as categorie, b.nom as budget_nom FROM transactions t
          LEFT JOIN categories c ON t.id_categorie = c.id
          LEFT JOIN budgets b ON t.id_budget = b.id
          WHERE t.id_utilisateur = ?';
$params = [$userId];

// Filtre par type
if ($filterType && in_array($filterType, ['revenu', 'depense'])) {
    $query .= ' AND t.type = ?';
    $params[] = $filterType;
}

// Filtre par catégorie
if ($filterCategory) {
    $query .= ' AND t.id_categorie = ?';
    $params[] = intval($filterCategory);
}

// Filtre par mois
if ($filterMonth) {
    $query .= ' AND MONTH(t.date_transaction) = ?';
    $params[] = intval($filterMonth);
}

// Filtre par année
if ($filterYear) {
    $query .= ' AND YEAR(t.date_transaction) = ?';
    $params[] = intval($filterYear);
}

// Filtre par budget/projet
if ($filterBudget) {
    $query .= ' AND t.id_budget = ?';
    $params[] = intval($filterBudget);
}

$query .= ' ORDER BY t.date_transaction DESC LIMIT 100';

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();

    // Récupère les catégories pour le filtre
    $catStmt = $pdo->prepare('SELECT id, nom FROM categories WHERE id_utilisateur IS NULL OR id_utilisateur = ? ORDER BY nom');
    $catStmt->execute([$userId]);
    $categories = $catStmt->fetchAll();

    // Récupère les budgets/projets pour le filtre
    $budStmt = $pdo->prepare('SELECT id, nom FROM budgets WHERE id_createur = ? ORDER BY nom');
    $budStmt->execute([$userId]);
    $budgets = $budStmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Erreur lors du chargement des transactions.';
    error_log('Database error: ' . $e->getMessage());
}

require_once '../../views/layouts/header.php';
?>

<div style="margin-bottom: var(--spacing-lg);">
    <div class="flex-between" style="margin-bottom: var(--spacing-lg);">
        <h1>Transactions</h1>
        <?php if (!isGuest()): ?>
            <a href="<?php echo BASE_URL; ?>/modules/transactions/add.php" class="btn btn-primary">
                + Ajouter une transaction
            </a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ Transaction ajoutée avec succès !
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card">
        <form method="GET" style="display: flex; gap: var(--spacing-base); align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 0 1 200px;">
                <label for="type">Type</label>
                <select id="type" name="type" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="revenu" <?php echo $filterType === 'revenu' ? 'selected' : ''; ?>>Revenus</option>
                    <option value="depense" <?php echo $filterType === 'depense' ? 'selected' : ''; ?>>Dépenses</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 0 1 200px;">
                <label for="category">Catégorie</label>
                <select id="category" name="category" onchange="this.form.submit()">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $filterCategory == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 0 1 180px;">
                <label for="month">Mois</label>
                <select id="month" name="month" onchange="this.form.submit()">
                    <option value="">Tous les mois</option>
                    <?php
                    $monthsList = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                    foreach ($monthsList as $idx => $m): $monthNum = str_pad($idx + 1, 2, '0', STR_PAD_LEFT); ?>
                        <option value="<?php echo $monthNum; ?>" <?php echo $filterMonth == $monthNum ? 'selected' : ''; ?>>
                            <?php echo $m; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 0 1 150px;">
                <label for="year">Année</label>
                <select id="year" name="year" onchange="this.form.submit()">
                    <option value="">Toutes</option>
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filterYear == $y ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 0 1 200px;">
                <label for="budget">Projet (Budget)</label>
                <select id="budget" name="budget" onchange="this.form.submit()">
                    <option value="">Tous les projets</option>
                    <?php foreach ($budgets as $bud): ?>
                        <option value="<?php echo $bud['id']; ?>" <?php echo $filterBudget == $bud['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($bud['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($filterType || $filterCategory || $filterMonth || $filterYear || $filterBudget): ?>
                <a href="<?php echo BASE_URL; ?>/modules/transactions/list.php" class="btn btn-secondary btn-sm">
                    Réinitialiser les filtres
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<!-- Liste des transactions -->
<div class="card">
    <div style="padding: var(--spacing-base); margin-bottom: var(--spacing-lg); background-color: var(--color-bg); border-radius: var(--radius); font-size: 14px; color: var(--color-text-secondary);">
        📊 Total: <strong><?php echo count($transactions); ?> transaction(s)</strong>
    </div>

    <?php if (empty($transactions)): ?>
        <div style="padding: var(--spacing-lg); text-align: center;">
            <p class="text-center text-muted">
                Aucune transaction trouvée.
            </p>
            <p style="font-size: 12px; color: #999; margin-top: var(--spacing-base);">
                Utilisateur ID: <?php echo htmlspecialchars($userId); ?> |
                Type: <?php echo htmlspecialchars($filterType ?: 'tous'); ?> |
                Catégorie: <?php echo htmlspecialchars($filterCategory ?: 'toutes'); ?> |
                Mois: <?php echo htmlspecialchars($filterMonth ?: 'tous'); ?>
            </p>
            <a href="<?php echo BASE_URL; ?>/modules/transactions/add.php" class="btn btn-primary" style="margin-top: var(--spacing-base);">
                + Ajouter une transaction
            </a>
        </div>
    <?php else: ?>
        <table class="transaction-table">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border);">
                    <th style="padding: var(--spacing-base);">Description</th>
                    <th style="padding: var(--spacing-base); text-align: right;">Montant</th>
                    <th style="padding: var(--spacing-base); text-align: right;">Date</th>
                    <th style="padding: var(--spacing-base); text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction):
                    $showActions = TRUE;
                    require '../../views/partials/transaction_row.php';
                endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
