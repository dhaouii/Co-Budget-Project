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

try {
    // Calcule le total des revenus
    $incomeStmt = $pdo->prepare(
        'SELECT SUM(montant) as total FROM transactions WHERE id_utilisateur = ? AND type = "revenu"'
    );
    $incomeStmt->execute([$userId]);
    $totalIncome = $incomeStmt->fetch()['total'] ?? 0;

    // Calcule le total des dépenses
    $expenseStmt = $pdo->prepare(
        'SELECT SUM(montant) as total FROM transactions WHERE id_utilisateur = ? AND type = "depense"'
    );
    $expenseStmt->execute([$userId]);
    $totalExpense = $expenseStmt->fetch()['total'] ?? 0;

    // Répartition par catégorie
    $categoryStmt = $pdo->prepare(
        'SELECT c.nom, SUM(t.montant) as total
         FROM transactions t
         LEFT JOIN categories c ON t.id_categorie = c.id
         WHERE t.id_utilisateur = ? AND t.type = "depense"
         GROUP BY t.id_categorie
         ORDER BY total DESC
         LIMIT 10'
    );
    $categoryStmt->execute([$userId]);
    $categories = $categoryStmt->fetchAll();

    $categoryData = [];
    foreach ($categories as $cat) {
        $categoryData[] = [
            'label' => $cat['nom'] ?? 'Sans catégorie',
            'value' => (float) $cat['total']
        ];
    }

    // Évolution mensuelle (12 derniers mois)
    $monthlyStmt = $pdo->prepare(
        'SELECT DATE_FORMAT(date_transaction, "%Y-%m") as month,
                SUM(CASE WHEN type = "revenu" THEN montant ELSE 0 END) as income,
                SUM(CASE WHEN type = "depense" THEN montant ELSE 0 END) as expense
         FROM transactions
         WHERE id_utilisateur = ? AND date_transaction >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
         GROUP BY month
         ORDER BY month ASC'
    );
    $monthlyStmt->execute([$userId]);
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
        'evolution' => $evolutionData
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
    error_log('Database error: ' . $e->getMessage());
}
