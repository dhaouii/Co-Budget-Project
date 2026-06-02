<?php
/**
 * Script d'initialisation - Crée les comptes admin avec les bons hashes bcrypt
 * À exécuter une seule fois pour initialiser les comptes admin de test
 */

require_once 'config/database.php';
require_once 'helpers/auth_helper.php';

try {
    // Supprime tous les utilisateurs existants
    $pdo->query('DELETE FROM utilisateurs');
    echo "✓ Anciens utilisateurs supprimés.\n";

    // Crée les mots de passe hashés
    $password = "password123";
    $hashedPassword = hashPassword($password);

    // Insère les comptes admin
    $stmt = $pdo->prepare(
        'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, statut)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    // Admin principal
    $stmt->execute(['Admin', 'Budget', 'admin@budget.app', $hashedPassword, 'admin', 'actif']);
    echo "✓ Admin créé : admin@budget.app / password123\n";

    // Utilisateurs de test avec statut actif
    $stmt->execute(['Hibadhaoui', 'Hiba', 'hibadhaoui@budget.app', $hashedPassword, 'utilisateur', 'actif']);
    echo "✓ Utilisateur créé : hibadhaoui@budget.app / password123\n";

    $stmt->execute(['Ben Mansour', 'Ahmend', 'ahmend.mansour@budget.app', $hashedPassword, 'utilisateur', 'actif']);
    echo "✓ Utilisateur créé : ahmend.mansour@budget.app / password123\n";

    $stmt->execute(['Adsyari', 'Mohamed', 'mohamed.adsyari@budget.app', $hashedPassword, 'utilisateur', 'actif']);
    echo "✓ Utilisateur créé : mohamed.adsyari@budget.app / password123\n";

    echo "\n✅ Initialisation complétée avec succès !\n";
    echo "Tu peux maintenant te connecter avec n'importe quel compte ci-dessus.\n";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
