<?php
/**
 * Suppression d'une transaction
 * Vérifie la propriété, supprime et redirige
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';

requireLogin();
blockGuest();
$userId = getCurrentUserId();

$transactionId = intval($_GET['id'] ?? 0);

if (!$transactionId) {
    header('Location: ' . BASE_URL . '/modules/transactions/list.php');
    exit;
}

try {
    // Vérifie que l'utilisateur est propriétaire
    $stmt = $pdo->prepare('SELECT id FROM transactions WHERE id = ? AND id_utilisateur = ?');
    $stmt->execute([$transactionId, $userId]);

    if (!$stmt->fetch()) {
        http_response_code(403);
        die('Accès refusé');
    }

    // Supprime la transaction
    $deleteStmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND id_utilisateur = ?');
    $deleteStmt->execute([$transactionId, $userId]);

    header('Location: ' . BASE_URL . '/modules/transactions/list.php?success=1');
    exit;
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/modules/transactions/list.php');
    exit;
}
