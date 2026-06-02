<?php
/**
 * Profil utilisateur
 * Affichage et édition des informations personnelles
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
$userId = getCurrentUserId();
$user = getCurrentUser();

$pageTitle = 'Mon profil';

$error = '';
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'update_info') {
        $nom = sanitize($_POST['nom'] ?? '');
        $prenom = sanitize($_POST['prenom'] ?? '');

        if (empty($nom) || empty($prenom)) {
            $error = 'Tous les champs sont requis.';
        } else {
            try {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET nom = ?, prenom = ? WHERE id = ?');
                $stmt->execute([$nom, $prenom, $userId]);
                $success = 'Profil mis à jour !';

                // Update session
                $_SESSION['user']['nom'] = $nom;
                $_SESSION['user']['prenom'] = $prenom;
            } catch (PDOException $e) {
                $error = 'Erreur lors de la mise à jour.';
            }
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            $error = 'Tous les champs sont requis.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (!validatePassword($newPassword)) {
            $error = 'Le mot de passe doit contenir au minimum 8 caractères.';
        } else {
            try {
                // Vérifie le mot de passe actuel
                $stmt = $pdo->prepare('SELECT mot_de_passe FROM utilisateurs WHERE id = ?');
                $stmt->execute([$userId]);
                $dbUser = $stmt->fetch();

                if (!verifyPassword($currentPassword, $dbUser['mot_de_passe'])) {
                    $error = 'Mot de passe actuel incorrect.';
                } else {
                    // Met à jour le mot de passe
                    $hashedPassword = hashPassword($newPassword);
                    $updateStmt = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?');
                    $updateStmt->execute([$hashedPassword, $userId]);
                    $success = 'Mot de passe changé avec succès !';
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors du changement de mot de passe.';
            }
        }
    }
}

require_once '../../views/layouts/header.php';
?>

<h1>Mon profil</h1>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <?php $message = $success; $type = 'success'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
    <!-- Informations personnelles -->
    <div class="card">
        <h3 style="margin-top: 0;">Informations personnelles</h3>

        <form method="POST">
            <input type="hidden" name="action" value="update_info">

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                <p style="font-size: 12px; color: var(--color-text-secondary); margin: 4px 0 0 0;">
                    L'email ne peut pas être modifié
                </p>
            </div>

            <div class="form-group">
                <label>Rôle</label>
                <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>

    <!-- Changer le mot de passe -->
    <div class="card">
        <h3 style="margin-top: 0;">Changer le mot de passe</h3>

        <form method="POST">
            <input type="hidden" name="action" value="change_password">

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" placeholder="Minimum 8 caractères" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
        </form>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>
