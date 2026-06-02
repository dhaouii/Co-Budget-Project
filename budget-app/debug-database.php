<?php
/**
 * Script de diagnostic - Vérifie la base de données
 */

require_once 'config/database.php';

echo "=== 📊 DIAGNOSTIC BASE DE DONNÉES ===\n\n";

try {
    // Utilisateurs
    $usersStmt = $pdo->query('SELECT id, prenom, nom, email, role FROM utilisateurs');
    $users = $usersStmt->fetchAll();
    echo "👥 Utilisateurs (" . count($users) . "):\n";
    foreach ($users as $user) {
        echo "  - ID: {$user['id']} | {$user['prenom']} {$user['nom']} ({$user['email']}) | Rôle: {$user['role']}\n";
    }

    // Transactions totales
    echo "\n";
    $transStmt = $pdo->query('SELECT COUNT(*) as count FROM transactions');
    $transCount = $transStmt->fetch()['count'];
    echo "💰 Transactions totales: $transCount\n";

    // Transactions par utilisateur
    echo "\n📝 Transactions par utilisateur:\n";
    foreach ($users as $user) {
        $userTransStmt = $pdo->prepare('SELECT COUNT(*) as count FROM transactions WHERE id_utilisateur = ?');
        $userTransStmt->execute([$user['id']]);
        $count = $userTransStmt->fetch()['count'];
        echo "  - {$user['prenom']}: $count transaction(s)\n";

        if ($count > 0) {
            $detailStmt = $pdo->prepare('SELECT id, type, montant, description, date_transaction FROM transactions WHERE id_utilisateur = ? ORDER BY date_transaction DESC LIMIT 3');
            $detailStmt->execute([$user['id']]);
            $trans = $detailStmt->fetchAll();
            foreach ($trans as $t) {
                echo "    • {$t['date_transaction']} | {$t['type']} | {$t['montant']} | {$t['description']}\n";
            }
        }
    }

    // Catégories
    echo "\n";
    $catsStmt = $pdo->query('SELECT COUNT(*) as count FROM categories');
    $catCount = $catsStmt->fetch()['count'];
    echo "📂 Catégories: $catCount\n";

    // Budgets
    echo "\n";
    $budStmt = $pdo->query('SELECT COUNT(*) as count FROM budgets');
    $budCount = $budStmt->fetch()['count'];
    echo "🎯 Budgets: $budCount\n";

    echo "\n✅ Diagnostic terminé !\n";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
