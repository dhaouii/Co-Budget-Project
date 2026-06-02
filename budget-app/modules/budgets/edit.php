<?php
/**
 * Modification d'un budget
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
blockGuest();
$userId = getCurrentUserId();

$pageTitle = 'Modifier un budget';
$customCSS = 'budgets.css';

$budgetId = intval($_GET['id'] ?? 0);
if (!$budgetId) {
    header('Location: ' . BASE_URL . '/modules/budgets/list.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM budgets WHERE id = ? AND id_createur = ?');
    $stmt->execute([$budgetId, $userId]);
    $budget = $stmt->fetch();

    if (!$budget) {
        http_response_code(403);
        die('Accès refusé');
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/modules/budgets/list.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['nom'] ?? '');
    $limit = sanitize($_POST['montant'] ?? '');

    if (empty($name) || empty($limit)) {
        $error = 'Nom et montant sont requis.';
    } elseif (!validateAmount($limit)) {
        $error = 'Le montant doit être positif.';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE budgets SET nom = ?, montant_limite = ? WHERE id = ? AND id_createur = ?');
            $stmt->execute([$name, floatval($limit), $budgetId, $userId]);

            header('Location: ' . BASE_URL . '/modules/budgets/list.php?success=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur lors de la modification.';
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

require_once '../../views/layouts/header.php';
?>

<h1>Modifier le budget</h1>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST">
        <div class="form-group">
            <label for="nom">Nom du budget</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($budget['nom']); ?>" required>
        </div>

        <div class="form-group">
            <label for="montant">Montant limite</label>
            <input
                type="number"
                id="montant"
                name="montant"
                step="0.01"
                value="<?php echo $budget['montant_limite']; ?>"
                required
            >
        </div>

        <div style="display: flex; gap: var(--spacing-base);">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/modules/budgets/list.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>
