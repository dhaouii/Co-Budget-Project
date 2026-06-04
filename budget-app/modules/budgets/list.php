<?php
/**
 * Liste des budgets de l'utilisateur (individuels + partagés)
 * Affiche la consommation et les alertes
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/format_helper.php';

requireLogin();
$userId = getCurrentUserId();

$pageTitle = 'Budgets';
$customCSS = 'budgets.css';
$customJS = 'budgets.js';

try {
    // Récupère tous les budgets de l'utilisateur (propres + partagés)
    $query = 'SELECT DISTINCT b.* FROM budgets b
              LEFT JOIN budget_membres bm ON b.id = bm.id_budget
              WHERE b.id_createur = ? OR bm.id_utilisateur = ?
              ORDER BY b.date_creation DESC';

    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId, $userId]);
    $budgets = $stmt->fetchAll();

    // Pour chaque budget, calcule la consommation
    $budgetsWithStats = [];
    foreach ($budgets as $budget) {
        $transStmt = $pdo->prepare(
            'SELECT SUM(montant) as total FROM transactions
             WHERE id_budget = ? AND type = "depense"'
        );
        $transStmt->execute([$budget['id']]);
        $spending = $transStmt->fetch()['total'] ?? 0;

        $budget['spending'] = $spending;
        $budget['percentage'] = $budget['montant_limite'] > 0
            ? round(($spending / $budget['montant_limite']) * 100)
            : 0;

        $budgetsWithStats[] = $budget;
    }

} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $budgetsWithStats = [];
}

require_once '../../views/layouts/header.php';
?>

<div style="margin-bottom: var(--spacing-lg);">
    <div class="flex-between" style="margin-bottom: var(--spacing-lg);">
        <h1>Budgets</h1>
        <?php if (!isGuest()): ?>
            <a href="<?php echo BASE_URL; ?>/modules/budgets/create.php" class="btn btn-primary">
                + Créer un budget
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($budgetsWithStats)): ?>
    <div class="card" style="text-align: center; padding: var(--spacing-xl);">
        <p class="text-muted">Aucun budget pour le moment.</p>
        <?php if (!isGuest()): ?>
            <a href="<?php echo BASE_URL; ?>/modules/budgets/create.php" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
                Créer votre premier budget
            </a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="budget-list">
        <?php foreach ($budgetsWithStats as $budget):
            $percentageClass = getPercentageClass($budget['spending'], $budget['montant_limite']);
            $isOwner = $budget['id_createur'] === $userId;
            $progressPercentage = min($budget['percentage'], 100);
        ?>
            <div class="budget-card">
                <div class="budget-header">
                    <div>
                        <h3 class="budget-title"><?php echo htmlspecialchars($budget['nom']); ?></h3>
                        <span class="budget-period"><?php echo ucfirst($budget['periode']); ?></span>
                    </div>
                    <?php if ($isOwner && !isGuest()): ?>
                        <div style="display: flex; gap: var(--spacing-sm);">
                            <a href="<?php echo BASE_URL; ?>/modules/budgets/edit.php?id=<?php echo $budget['id']; ?>" class="btn btn-secondary btn-sm" title="Modifier" style="display: inline-flex; align-items: center; gap: 6px;">
                                <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-danger btn-sm js-delete-btn" data-url="<?php echo BASE_URL; ?>/modules/budgets/delete.php?id=<?php echo $budget['id']; ?>" data-message="Êtes-vous sûr de vouloir supprimer ce budget ?" title="Supprimer" style="display: inline-flex; align-items: center; gap: 6px;">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Supprimer
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Alerte si dépassement -->
                <?php if ($budget['percentage'] >= 80): ?>
                    <div style="
                        background-color: rgba(239, 68, 68, 0.1);
                        border: 1px solid #FCA5A5;
                        border-radius: var(--radius);
                        padding: var(--spacing-sm);
                        margin-bottom: var(--spacing-base);
                        color: #7F1D1D;
                        font-size: 13px;
                        display: flex;
                        align-items: center;
                        gap: var(--spacing-sm);
                    ">
                        <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                        Attention ! Budget à <?php echo $budget['percentage']; ?>%
                    </div>
                <?php endif; ?>

                <!-- Barre de progression -->
                <div class="budget-progress-bar">
                    <div
                        class="budget-progress-fill <?php echo $percentageClass; ?>"
                        style="width: <?php echo $progressPercentage; ?>%;"
                    ></div>
                </div>

                <!-- Statistiques -->
                <div class="budget-stats">
                    <div class="budget-stat">
                        <span class="budget-stat-label">Dépensé</span>
                        <span class="budget-stat-value">
                            <?php echo formatMoney($budget['spending'], $budget['devise']); ?>
                        </span>
                    </div>
                    <div class="budget-stat">
                        <span class="budget-stat-label">Limite</span>
                        <span class="budget-stat-value">
                            <?php echo formatMoney($budget['montant_limite'], $budget['devise']); ?>
                        </span>
                    </div>
                    <div class="budget-stat">
                        <span class="budget-stat-label">Restant</span>
                        <span class="budget-stat-value" style="color: <?php echo $budget['spending'] > $budget['montant_limite'] ? '#EF4444' : '#10B981'; ?>">
                            <?php echo formatMoney(max(0, $budget['montant_limite'] - $budget['spending']), $budget['devise']); ?>
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="budget-actions">
                    <a href="<?php echo BASE_URL; ?>/modules/transactions/list.php?budget=<?php echo $budget['id']; ?>" class="btn btn-secondary" style="flex: 1;">
                        <i data-lucide="list" style="width: 14px; height: 14px; margin-right: 4px;"></i>
                        Transactions
                    </a>
                    <?php if ($budget['est_partage'] && $isOwner): ?>
                        <a href="<?php echo BASE_URL; ?>/modules/budgets/members.php?id=<?php echo $budget['id']; ?>" class="btn btn-secondary">
                            <i data-lucide="users" style="width: 14px; height: 14px;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
