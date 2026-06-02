<?php
/**
 * Validation d'un compte utilisateur
 * Endpoint rapide pour changer le statut à "actif"
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/mail_helper.php';

requireLogin();
requireRole(ROLE_ADMIN);

$userId = intval($_GET['id'] ?? $_POST['user_id'] ?? 0);

if (!$userId) {
    header('Location: ' . BASE_URL . '/modules/admin/users_list.php');
    exit;
}

try {
    // Récupère l'utilisateur
    $stmt = $pdo->prepare('SELECT id, prenom, email, statut FROM utilisateurs WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        die('Utilisateur non trouvé');
    }

    // Vérifie que le compte est inactif
    if ($user['statut'] !== 'inactif') {
        header('Location: ' . BASE_URL . '/modules/admin/users_list.php');
        exit;
    }

    // Change le statut à actif
    $updateStmt = $pdo->prepare('UPDATE utilisateurs SET statut = ? WHERE id = ?');
    $updateStmt->execute(['actif', $userId]);

    // Envoie un email de confirmation
    sendActivationEmail($user['email'], $user['prenom']);

    // Redirige avec message de succès
    header('Location: ' . BASE_URL . '/modules/admin/users_list.php?success=1');
    exit;
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/modules/admin/users_list.php');
    exit;
}
