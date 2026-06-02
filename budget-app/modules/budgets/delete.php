<?php
/**
 * Suppression d'un budget
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';

requireLogin();
blockGuest();
$userId = getCurrentUserId();

$budgetId = intval($_GET['id'] ?? 0);

if (!$budgetId) {
    header('Location: ' . BASE_URL . '/modules/budgets/list.php');
    exit;
}

try {
    // Vérifie que l'utilisateur est propriétaire
    $stmt = $pdo->prepare('SELECT id FROM budgets WHERE id = ? AND id_createur = ?');
    $stmt->execute([$budgetId, $userId]);

    if (!$stmt->fetch()) {
        http_response_code(403);
        die('Accès refusé');
    }

    // Supprime le budget (les transactions associées restent avec id_budget = NULL)
    $deleteStmt = $pdo->prepare('DELETE FROM budgets WHERE id = ? AND id_createur = ?');
    $deleteStmt->execute([$budgetId, $userId]);

    header('Location: ' . BASE_URL . '/modules/budgets/list.php?success=1');
    exit;
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/modules/budgets/list.php');
    exit;
}
