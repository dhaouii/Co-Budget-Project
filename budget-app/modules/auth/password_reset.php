<?php
/**
 * Réinitialisation de mot de passe
 * Demande l'email + envoie un mot de passe temporaire
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

initSession();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'L\'email est requis.';
    } elseif (!validateEmail($email)) {
        $error = 'Email invalide.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, prenom FROM utilisateurs WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Génère un nouveau mot de passe temporaire
                $newPassword = 'Temp' . rand(1000, 9999) . '!';
                $hashedPassword = hashPassword($newPassword);

                $updateStmt = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?');
                $updateStmt->execute([$hashedPassword, $user['id']]);

                $success = 'Nouveau mot de passe temporaire : <strong style="font-size: 18px; color: #4338CA;">' . htmlspecialchars($newPassword) . '</strong><br>Connectez-vous et changez-le immédiatement.';
            } else {
                $error = 'Aucun compte associé à cet email.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur serveur.';
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

require_once '../../views/auth/password_reset_view.php';
