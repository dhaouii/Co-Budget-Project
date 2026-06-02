<?php
/**
 * Script de seed - Ajoute des transactions de test
 * À exécuter une seule fois pour remplir la base de données avec des données de test
 */

require_once 'config/database.php';

try {
    // D'abord, récupère les IDs des utilisateurs
    $usersStmt = $pdo->query('SELECT id, prenom FROM utilisateurs LIMIT 3');
    $users = $usersStmt->fetchAll();

    if (empty($users)) {
        echo "❌ Aucun utilisateur trouvé ! Exécute d'abord init-admin.php\n";
        exit;
    }

    // Récupère les catégories
    $catsStmt = $pdo->query('SELECT id, nom FROM categories LIMIT 5');
    $categories = $catsStmt->fetchAll();

    if (empty($categories)) {
        echo "❌ Aucune catégorie trouvée ! Exécute d'abord le seed.sql\n";
        exit;
    }

    // Transactions de test
    $transactions = [
        // Revenus
        ['type' => 'revenu', 'montant' => 3500, 'description' => 'Salaire mensuel', 'date' => date('Y-m-d')],
        ['type' => 'revenu', 'montant' => 500, 'description' => 'Prime', 'date' => date('Y-m-d', strtotime('-5 days'))],

        // Dépenses courantes
        ['type' => 'depense', 'montant' => 45.50, 'description' => 'Épicerie', 'date' => date('Y-m-d')],
        ['type' => 'depense', 'montant' => 120, 'description' => 'Restaurant', 'date' => date('Y-m-d', strtotime('-1 days'))],
        ['type' => 'depense', 'montant' => 80, 'description' => 'Carburant', 'date' => date('Y-m-d', strtotime('-2 days'))],
        ['type' => 'depense', 'montant' => 35, 'description' => 'Café', 'date' => date('Y-m-d', strtotime('-3 days'))],
        ['type' => 'depense', 'montant' => 200, 'description' => 'Courses shopping', 'date' => date('Y-m-d', strtotime('-4 days'))],
        ['type' => 'depense', 'montant' => 1200, 'description' => 'Loyer', 'date' => date('Y-m-01')],
        ['type' => 'depense', 'montant' => 60, 'description' => 'Internet', 'date' => date('Y-m-01')],
        ['type' => 'depense', 'montant' => 50, 'description' => 'Téléphone', 'date' => date('Y-m-01')],
    ];

    $stmt = $pdo->prepare(
        'INSERT INTO transactions (id_utilisateur, type, montant, description, id_categorie, date_transaction, date_creation)
         VALUES (?, ?, ?, ?, ?, ?, NOW())'
    );

    $count = 0;
    foreach ($users as $user) {
        foreach ($transactions as $trans) {
            // Ajoute la transaction pour chaque utilisateur
            $categoryId = $categories[array_rand($categories)]['id'];

            $stmt->execute([
                $user['id'],
                $trans['type'],
                $trans['montant'],
                $trans['description'],
                $categoryId,
                $trans['date']
            ]);

            $count++;
        }
    }

    echo "✅ $count transactions créées avec succès !\n";
    echo "Les transactions sont disponibles pour les 3 utilisateurs de test.\n";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    error_log('Database error: ' . $e->getMessage());
}
