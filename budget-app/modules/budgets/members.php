<?php
/**
 * Gestion des membres d'un budget partagé
 * Ajout/suppression de membres
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
blockGuest();
$userId = getCurrentUserId();

$pageTitle = 'Gestion des membres';
$customCSS = 'budgets.css';

$budgetId = intval($_GET['id'] ?? 0);
if (!$budgetId) {
    header('Location: ' . BASE_URL . '/modules/budgets/list.php');
    exit;
}

try {
    // Vérifie que l'utilisateur est propriétaire
    $stmt = $pdo->prepare('SELECT * FROM budgets WHERE id = ? AND id_createur = ?');
    $stmt->execute([$budgetId, $userId]);
    $budget = $stmt->fetch();

    if (!$budget) {
        http_response_code(403);
        die('Accès refusé');
    }

    // Récupère les membres
    $memberStmt = $pdo->prepare(
        'SELECT bm.*, u.prenom, u.nom, u.email FROM budget_membres bm
         JOIN utilisateurs u ON bm.id_utilisateur = u.id
         WHERE bm.id_budget = ? ORDER BY u.prenom'
    );
    $memberStmt->execute([$budgetId]);
    $members = $memberStmt->fetchAll();
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $members = [];
}

$error = '';
$success = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');
    $email = sanitize($_POST['email'] ?? '');

    if ($action === 'add' && !empty($email)) {
        if (!validateEmail($email)) {
            $error = 'Email invalide.';
        } else {
            try {
                // Cherche l'utilisateur par email
                $userStmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND statut = ?');
                $userStmt->execute([$email, 'actif']);
                $targetUser = $userStmt->fetch();

                if (!$targetUser) {
                    $error = 'Utilisateur non trouvé ou inactif.';
                } else {
                    $targetUserId = $targetUser['id'];

                    // Vérifie qu'il n'est pas déjà membre
                    $checkStmt = $pdo->prepare('SELECT id FROM budget_membres WHERE id_budget = ? AND id_utilisateur = ?');
                    $checkStmt->execute([$budgetId, $targetUserId]);

                    if ($checkStmt->fetch()) {
                        $error = 'Cet utilisateur est déjà membre.';
                    } else {
                        // Ajoute le membre
                        $addStmt = $pdo->prepare(
                            'INSERT INTO budget_membres (id_budget, id_utilisateur, role_membre) VALUES (?, ?, ?)'
                        );
                        $addStmt->execute([$budgetId, $targetUserId, 'membre']);
                        $success = 'Membre ajouté avec succès !';

                        // Reload members
                        $memberStmt = $pdo->prepare(
                            'SELECT bm.*, u.prenom, u.nom, u.email FROM budget_membres bm
                             JOIN utilisateurs u ON bm.id_utilisateur = u.id
                             WHERE bm.id_budget = ? ORDER BY u.prenom'
                        );
                        $memberStmt->execute([$budgetId]);
                        $members = $memberStmt->fetchAll();
                    }
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de l\'ajout du membre.';
                error_log('Database error: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'remove') {
        $memberId = intval($_POST['member_id'] ?? 0);

        if ($memberId) {
            try {
                // Vérifie qu'on ne supprime pas le propriétaire
                $checkStmt = $pdo->prepare(
                    'SELECT role_membre FROM budget_membres WHERE id = ? AND id_budget = ?'
                );
                $checkStmt->execute([$memberId, $budgetId]);
                $memberRecord = $checkStmt->fetch();

                if ($memberRecord && $memberRecord['role_membre'] === 'proprietaire') {
                    $error = 'Impossible de supprimer le propriétaire.';
                } else {
                    $delStmt = $pdo->prepare('DELETE FROM budget_membres WHERE id = ? AND id_budget = ?');
                    $delStmt->execute([$memberId, $budgetId]);
                    $success = 'Membre supprimé.';

                    // Reload
                    $memberStmt = $pdo->prepare(
                        'SELECT bm.*, u.prenom, u.nom, u.email FROM budget_membres bm
                         JOIN utilisateurs u ON bm.id_utilisateur = u.id
                         WHERE bm.id_budget = ? ORDER BY u.prenom'
                    );
                    $memberStmt->execute([$budgetId]);
                    $members = $memberStmt->fetchAll();
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de la suppression.';
                error_log('Database error: ' . $e->getMessage());
            }
        }
    }
}

require_once '../../views/layouts/header.php';
?>

<h1>Membres du budget: <?php echo htmlspecialchars($budget['nom']); ?></h1>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <?php $message = $success; $type = 'success'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div class="card" style="max-width: 600px; margin-bottom: var(--spacing-lg);">
    <h3 style="margin-top: 0;">Ajouter un membre</h3>

    <form method="POST">
        <input type="hidden" name="action" value="add">

        <div class="form-group">
            <label for="email">Email du membre</label>
            <input type="email" id="email" name="email" placeholder="membre@example.com" required>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter le membre</button>
    </form>
</div>

<!-- Liste des membres -->
<div class="card">
    <h3 style="margin-top: 0;">Membres actuels (<?php echo count($members); ?>)</h3>

    <?php if (empty($members)): ?>
        <p class="text-muted">Aucun membre pour le moment.</p>
    <?php else: ?>
        <ul class="members-list">
            <?php foreach ($members as $member): ?>
                <li>
                    <div>
                        <strong><?php echo htmlspecialchars($member['prenom'] . ' ' . $member['nom']); ?></strong>
                        <p style="font-size: 12px; margin: 4px 0 0 0; color: var(--color-text-secondary);">
                            <?php echo htmlspecialchars($member['email']); ?>
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: var(--spacing-base);">
                        <span class="member-role badge badge-primary">
                            <?php echo ucfirst($member['role_membre']); ?>
                        </span>

                        <?php if ($member['role_membre'] !== 'proprietaire'): ?>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                <button type="button" class="btn btn-danger btn-sm" onclick="if(typeof confirmDelete==='function'){confirmDelete('Voulez-vous vraiment retirer ce membre ?', () => this.form.submit())} else if(window.confirm('Voulez-vous vraiment retirer ce membre ?')) this.form.submit()">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<a href="<?php echo BASE_URL; ?>/modules/budgets/list.php" class="btn btn-secondary" style="margin-top: var(--spacing-lg);">
    Retour aux budgets
</a>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
