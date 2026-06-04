<?php
/**
 * Endpoint API JSON pour le tableau de bord
 * Retourne : revenus, dépenses, solde, répartition par catégorie, évolution mensuelle
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';

header('Content-Type: application/json');

requireLogin();
$userId = getCurrentUserId();

// Filtres
$filterYear = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$filterMonth = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$filterBudget = isset($_GET['budget']) && $_GET['budget'] !== '' ? intval($_GET['budget']) : null;

$dateConditions = '';
$dateParams = [];
if ($filterYear) {
    $dateConditions .= ' AND YEAR(date_transaction) = ?';
    $dateParams[] = $filterYear;
}
if ($filterMonth) {
    $dateConditions .= ' AND MONTH(date_transaction) = ?';
    $dateParams[] = $filterMonth;
}
if ($filterBudget) {
    $dateConditions .= ' AND id_budget = ?';
    $dateParams[] = $filterBudget;
}

// Condition pour inclure transactions partagées
$sharedCondition = ' (id_utilisateur = ? OR id_budget IN (
    SELECT id FROM budgets WHERE id_createur = ?
    UNION
    SELECT id_budget FROM budget_membres WHERE id_utilisateur = ?
))';
$sharedParams = [$userId, $userId, $userId];

try {
    // Calcule le total des revenus
    $incomeStmt = $pdo->prepare(
        'SELECT SUM(montant) as total FROM transactions WHERE' . $sharedCondition . ' AND type = "revenu"' . $dateConditions
    );
    $incomeStmt->execute(array_merge($sharedParams, $dateParams));
    $totalIncome = $incomeStmt->fetch()['total'] ?? 0;

    // Calcule le total des dépenses
    $expenseStmt = $pdo->prepare(
        'SELECT SUM(montant) as total FROM transactions WHERE' . $sharedCondition . ' AND type = "depense"' . $dateConditions
    );
    $expenseStmt->execute(array_merge($sharedParams, $dateParams));
    $totalExpense = $expenseStmt->fetch()['total'] ?? 0;

    // Répartition par catégorie (avec filtres + budgets partagés)
    $catConditions = str_replace('date_transaction', 't.date_transaction', $dateConditions);
    $catConditions = str_replace('id_budget', 't.id_budget', $catConditions);
    $catSharedCondition = ' (t.id_utilisateur = ? OR t.id_budget IN (
        SELECT id FROM budgets WHERE id_createur = ?
        UNION
        SELECT id_budget FROM budget_membres WHERE id_utilisateur = ?
    ))';

    // Dépenses par catégorie (avec couleurs)
    $categoryStmt = $pdo->prepare(
        'SELECT c.nom, c.couleur, SUM(t.montant) as total
         FROM transactions t
         LEFT JOIN categories c ON t.id_categorie = c.id
         WHERE' . $catSharedCondition . ' AND t.type = "depense"' . $catConditions . '
         GROUP BY t.id_categorie, c.nom, c.couleur
         ORDER BY total DESC
         LIMIT 10'
    );
    $categoryStmt->execute(array_merge($sharedParams, $dateParams));
    $categories = $categoryStmt->fetchAll();

    // Revenus par catégorie (avec couleurs)
    $incomeCatStmt = $pdo->prepare(
        'SELECT c.nom, c.couleur, SUM(t.montant) as total
         FROM transactions t
         LEFT JOIN categories c ON t.id_categorie = c.id
         WHERE' . $catSharedCondition . ' AND t.type = "revenu"' . $catConditions . '
         GROUP BY t.id_categorie, c.nom, c.couleur
         ORDER BY total DESC
         LIMIT 10'
    );
    $incomeCatStmt->execute(array_merge($sharedParams, $dateParams));
    $incomeCategories = $incomeCatStmt->fetchAll();

    $categoryData = [];
    foreach ($categories as $cat) {
        $categoryData[] = [
            'label' => $cat['nom'] ?? 'Sans catégorie',
            'value' => (float) $cat['total'],
            'color' => $cat['couleur'] ?? '#9CA3AF'
        ];
    }

    $incomeCategoryData = [];
    foreach ($incomeCategories as $cat) {
        $incomeCategoryData[] = [
            'label' => $cat['nom'] ?? 'Sans catégorie',
            'value' => (float) $cat['total'],
            'color' => $cat['couleur'] ?? '#9CA3AF'
        ];
    }

    // Évolution mensuelle (avec filtres + budgets partagés)
    $monthlyStmt = $pdo->prepare(
        'SELECT DATE_FORMAT(date_transaction, "%Y-%m") as month,
                SUM(CASE WHEN type = "revenu" THEN montant ELSE 0 END) as income,
                SUM(CASE WHEN type = "depense" THEN montant ELSE 0 END) as expense
         FROM transactions
         WHERE' . $sharedCondition . $dateConditions . '
         GROUP BY month
         ORDER BY month ASC'
    );
    $monthlyStmt->execute(array_merge($sharedParams, $dateParams));
    $monthlyData = $monthlyStmt->fetchAll();

    $evolutionData = [];
    foreach ($monthlyData as $month) {
        $evolutionData[] = [
            'label' => $month['month'],
            'income' => (float) $month['income'],
            'expense' => (float) $month['expense']
        ];
    }

    // Retour JSON
    echo json_encode([
        'total_income' => (float) $totalIncome,
        'total_expense' => (float) $totalExpense,
        'balance' => (float) ($totalIncome - $totalExpense),
        'categories' => $categoryData,
        'income_categories' => $incomeCategoryData,
        'evolution' => $evolutionData
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
    error_log('Database error: ' . $e->getMessage());
}
