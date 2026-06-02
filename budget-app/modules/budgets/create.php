<?php
/**
 * Création d'un budget (individuel ou partagé)
 * Formulaire + traitement POST
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
blockGuest();
$userId = getCurrentUserId();

$pageTitle = 'Créer un budget';
$customCSS = 'budgets.css';
$customJS = 'budgets.js';

$error = '';

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['nom'] ?? '');
    $limit = sanitize($_POST['montant'] ?? '');
    $currency = sanitize($_POST['devise'] ?? 'TND');
    $period = sanitize($_POST['periode'] ?? 'mensuel');
    $dateStart = sanitize($_POST['date_debut'] ?? '');
    $dateEnd = sanitize($_POST['date_fin'] ?? '');
    $isShared = isset($_POST['est_partage']) ? TRUE : FALSE;

    // Validation
    if (empty($name) || empty($limit) || empty($dateStart)) {
        $error = 'Nom, montant et date de début sont requis.';
    } elseif (!validateAmount($limit)) {
        $error = 'Le montant doit être positif.';
    } elseif (!validateDate($dateStart)) {
        $error = 'Date de début invalide.';
    } elseif ($dateEnd && !validateDate($dateEnd)) {
        $error = 'Date de fin invalide.';
    } elseif ($period === 'personnalise' && empty($dateEnd)) {
        $error = 'La date de fin est requise pour une période personnalisée.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO budgets (nom, montant_limite, devise, periode, date_debut, date_fin, est_partage, id_createur)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $name,
                floatval($limit),
                $currency,
                $period,
                $dateStart,
                $dateEnd ?: NULL,
                $isShared ? 1 : 0,
                $userId
            ]);

            $budgetId = $pdo->lastInsertId();

            // Ajoute le créateur comme propriétaire
            $memberStmt = $pdo->prepare(
                'INSERT INTO budget_membres (id_budget, id_utilisateur, role_membre) VALUES (?, ?, ?)'
            );
            $memberStmt->execute([$budgetId, $userId, 'proprietaire']);

            header('Location: ' . BASE_URL . '/modules/budgets/list.php?success=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur lors de la création du budget.';
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

require_once '../../views/layouts/header.php';
?>

<h1>Créer un budget</h1>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <form method="POST" id="budgetForm">
        <div class="form-group">
            <label for="nom">Nom du budget *</label>
            <input type="text" id="nom" name="nom" placeholder="Ex: Budget mensuel" required>
        </div>

        <div class="form-group">
            <label for="montant">Montant limite *</label>
            <div style="display: flex; gap: var(--spacing-base);">
                <input
                    type="number"
                    id="montant"
                    name="montant"
                    step="0.01"
                    min="0"
                    class="amount-input"
                    placeholder="0.00"
                    style="flex: 1;"
                    required
                >
                <select id="devise" name="devise" style="width: auto;">
                    <option value="TND">TND</option>
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="periode">Période *</label>
            <select id="periode" name="periode" onchange="toggleDateEnd()">
                <option value="mensuel">Mensuel</option>
                <option value="hebdomadaire">Hebdomadaire</option>
                <option value="personnalise">Personnalisé</option>
            </select>
        </div>

        <div class="form-group">
            <label for="date_debut">Date de début *</label>
            <input type="date" id="date_debut" name="date_debut" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group" id="dateEndGroup" style="display: none;">
            <label for="date_fin">Date de fin</label>
            <input type="date" id="date_fin" name="date_fin">
        </div>

        <div class="form-group form-group checkbox">
            <input type="checkbox" id="est_partage" name="est_partage" onchange="toggleSharedOptions()">
            <label for="est_partage" style="margin: 0; font-weight: normal;">Budget partagé</label>
        </div>

        <div id="sharedOptions" style="display: none; background-color: var(--color-bg); padding: var(--spacing-base); border-radius: var(--radius); margin-bottom: var(--spacing-lg);">
            <p style="font-size: 13px; color: var(--color-text-secondary); margin-bottom: var(--spacing-base);">
                Vous pourrez inviter des membres après la création du budget.
            </p>
        </div>

        <div style="display: flex; gap: var(--spacing-base);">
            <button type="submit" class="btn btn-primary">Créer le budget</button>
            <a href="<?php echo BASE_URL; ?>/modules/budgets/list.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
    function toggleDateEnd() {
        const periode = document.getElementById('periode').value;
        const dateEndGroup = document.getElementById('dateEndGroup');
        dateEndGroup.style.display = periode === 'personnalise' ? 'flex' : 'none';

        if (periode === 'personnalise') {
            document.getElementById('date_fin').required = true;
        } else {
            document.getElementById('date_fin').required = false;
        }
    }

    function toggleSharedOptions() {
        const isShared = document.getElementById('est_partage').checked;
        const sharedOptions = document.getElementById('sharedOptions');
        sharedOptions.style.display = isShared ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleDateEnd();
        toggleSharedOptions();
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
