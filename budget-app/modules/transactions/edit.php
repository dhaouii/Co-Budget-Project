<?php
/**
 * Modification d'une transaction
 * Charge les données, affiche le formulaire, traite la mise à jour
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
blockGuest();
$userId = getCurrentUserId();

$pageTitle = 'Modifier une transaction';
$customCSS = 'transactions.css';

$error = '';
$transaction = NULL;

$transactionId = intval($_GET['id'] ?? 0);
if (!$transactionId) {
    header('Location: ' . BASE_URL . '/modules/transactions/list.php');
    exit;
}

// Récupère la transaction
try {
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ? AND id_utilisateur = ?');
    $stmt->execute([$transactionId, $userId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        header('Location: ' . BASE_URL . '/modules/transactions/list.php');
        exit;
    }

    // Récupère les catégories et budgets
    $catStmt = $pdo->prepare('SELECT id, nom FROM categories WHERE id_utilisateur IS NULL OR id_utilisateur = ? ORDER BY nom');
    $catStmt->execute([$userId]);
    $categories = $catStmt->fetchAll();

    $budStmt = $pdo->prepare('SELECT id, nom FROM budgets WHERE id_createur = ? ORDER BY nom');
    $budStmt->execute([$userId]);
    $budgets = $budStmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Erreur lors du chargement de la transaction.';
}

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = sanitize($_POST['montant'] ?? '');
    $type = sanitize($_POST['type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $date = sanitize($_POST['date'] ?? '');
    $categoryId = intval($_POST['categorie'] ?? 0);
    $budgetId = intval($_POST['budget'] ?? 0) ?: NULL;

    if (empty($amount) || empty($type) || empty($date)) {
        $error = 'Montant, type et date sont requis.';
    } elseif (!validateAmount($amount)) {
        $error = 'Le montant doit être un nombre positif.';
    } elseif (!validateTransactionType($type)) {
        $error = 'Type de transaction invalide.';
    } elseif (!validateDate($date)) {
        $error = 'Date invalide.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'UPDATE transactions SET montant = ?, type = ?, description = ?, date_transaction = ?, id_categorie = ?, id_budget = ?
                 WHERE id = ? AND id_utilisateur = ?'
            );

            $stmt->execute([
                floatval($amount),
                $type,
                $description,
                $date,
                $categoryId ?: NULL,
                $budgetId,
                $transactionId,
                $userId
            ]);

            header('Location: ' . BASE_URL . '/modules/transactions/list.php?success=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur lors de la modification.';
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

require_once '../../views/layouts/header.php';
?>

<h1>Modifier la transaction</h1>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST" id="transactionForm">
        <div class="form-group">
            <label for="montant">Montant *</label>
            <input
                type="number"
                id="montant"
                name="montant"
                step="0.01"
                min="0"
                class="amount-input"
                value="<?php echo htmlspecialchars($transaction['montant']); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="type">Type *</label>
            <select id="type" name="type" required>
                <option value="revenu" <?php echo $transaction['type'] === 'revenu' ? 'selected' : ''; ?>>Revenu</option>
                <option value="depense" <?php echo $transaction['type'] === 'depense' ? 'selected' : ''; ?>>Dépense</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input
                type="text"
                id="description"
                name="description"
                value="<?php echo htmlspecialchars($transaction['description'] ?? ''); ?>"
            >
        </div>

        <div class="form-group">
            <label for="date">Date *</label>
            <input
                type="date"
                id="date"
                name="date"
                value="<?php echo htmlspecialchars($transaction['date_transaction']); ?>"
                min="2020-01-01"
                max="<?php echo date('Y-m-d'); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="categorie">Catégorie</label>
            <select id="categorie" name="categorie">
                <option value="">-- Aucune catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $transaction['id_categorie'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="budget">Budget</label>
            <select id="budget" name="budget">
                <option value="">-- Aucun budget --</option>
                <?php foreach ($budgets as $bud): ?>
                    <option value="<?php echo $bud['id']; ?>" <?php echo $transaction['id_budget'] == $bud['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($bud['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: var(--spacing-base);">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/modules/transactions/list.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>
