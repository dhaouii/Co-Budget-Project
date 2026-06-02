<?php
/**
 * Liste de tous les utilisateurs pour l'administration
 * Validation, suspension, changement de rôle
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
requireRole(ROLE_ADMIN);

$pageTitle = 'Gestion des utilisateurs';
$customCSS = 'admin.css';

$filter = sanitize($_GET['filter'] ?? '');
$error = '';
$success = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');
    $userId = intval($_POST['user_id'] ?? 0);

    if ($action === 'activate' && $userId) {
        try {
            $stmt = $pdo->prepare('UPDATE utilisateurs SET statut = ? WHERE id = ?');
            $stmt->execute(['actif', $userId]);
            $success = 'Utilisateur activé avec succès.';
        } catch (PDOException $e) {
            $error = 'Erreur lors de l\'activation.';
        }
    } elseif ($action === 'suspend' && $userId) {
        try {
            $stmt = $pdo->prepare('UPDATE utilisateurs SET statut = ? WHERE id = ?');
            $stmt->execute(['suspendu', $userId]);
            $success = 'Utilisateur suspendu.';
        } catch (PDOException $e) {
            $error = 'Erreur lors de la suspension.';
        }
    } elseif ($action === 'change_role' && $userId) {
        $newRole = sanitize($_POST['role'] ?? '');
        if (validateRole($newRole)) {
            try {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
                $stmt->execute([$newRole, $userId]);
                $success = 'Rôle modifié avec succès.';
            } catch (PDOException $e) {
                $error = 'Erreur lors de la modification du rôle.';
            }
        }
    }
}

// Récupère les utilisateurs
try {
    $query = 'SELECT * FROM utilisateurs WHERE 1=1';
    $params = [];

    if ($filter === 'inactive') {
        $query .= ' AND statut = ?';
        $params[] = 'inactif';
    } elseif ($filter === 'suspended') {
        $query .= ' AND statut = ?';
        $params[] = 'suspendu';
    }

    $query .= ' ORDER BY date_creation DESC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $users = [];
}

require_once '../../views/layouts/header.php';
?>

<div style="margin-bottom: var(--spacing-lg);">
    <h1>Gestion des utilisateurs</h1>

    <!-- Filtres -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div style="display: flex; gap: var(--spacing-base); flex-wrap: wrap;">
            <a href="<?php echo BASE_URL; ?>/modules/admin/users_list.php" class="btn <?php echo !$filter ? 'btn-primary' : 'btn-secondary'; ?>">
                Tous les utilisateurs (<?php echo count($users); ?>)
            </a>

            <?php
            $inactiveCount = count(array_filter($users, fn($u) => $u['statut'] === 'inactif'));
            ?>
            <a href="<?php echo BASE_URL; ?>/modules/admin/users_list.php?filter=inactive" class="btn <?php echo $filter === 'inactive' ? 'btn-primary' : 'btn-secondary'; ?>">
                En attente (<?php echo $inactiveCount; ?>)
            </a>

            <?php
            $suspendedCount = count(array_filter($users, fn($u) => $u['statut'] === 'suspendu'));
            ?>
            <a href="<?php echo BASE_URL; ?>/modules/admin/users_list.php?filter=suspended" class="btn <?php echo $filter === 'suspended' ? 'btn-primary' : 'btn-secondary'; ?>">
                Suspendus (<?php echo $suspendedCount; ?>)
            </a>
        </div>
    </div>
</div>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <?php $message = $success; $type = 'success'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div class="card">
    <?php if (empty($users)): ?>
        <p class="text-center text-muted" style="padding: var(--spacing-lg);">
            Aucun utilisateur trouvé.
        </p>
    <?php else: ?>
        <table class="users-table">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border);">
                    <th style="padding: var(--spacing-base); text-align: left; font-weight: 600;">Utilisateur</th>
                    <th style="padding: var(--spacing-base); text-align: center; font-weight: 600;">Rôle</th>
                    <th style="padding: var(--spacing-base); text-align: center; font-weight: 600;">Statut</th>
                    <th style="padding: var(--spacing-base); text-align: right; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td style="padding: var(--spacing-base);">
                            <div style="display: flex; align-items: center; gap: var(--spacing-base);">
                                <div class="user-avatar" style="flex-shrink: 0;">
                                    <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 500;">
                                        <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                                    </div>
                                    <div style="font-size: 12px; color: var(--color-text-secondary);">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td style="padding: var(--spacing-base); text-align: center;">
                            <span class="badge badge-primary">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>

                        <td style="padding: var(--spacing-base); text-align: center;">
                            <span class="status-badge status-<?php echo $user['statut']; ?>">
                                <?php echo ucfirst($user['statut']); ?>
                            </span>
                        </td>

                        <td style="padding: var(--spacing-base); text-align: right;">
                            <div style="display: flex; gap: var(--spacing-sm); justify-content: flex-end;">
                                <!-- Activer si inactif -->
                                <?php if ($user['statut'] === 'inactif'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm" title="Activer le compte">
                                            <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Suspendre si actif -->
                                <?php if ($user['statut'] === 'actif'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="suspend">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Suspendre le compte" onclick="return confirm('Êtes-vous sûr ?')">
                                            <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
