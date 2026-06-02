<?php
/**
 * Script pour corriger "SantA" → "Santé"
 */

require_once 'config/database.php';

try {
    $stmt = $pdo->prepare('UPDATE categories SET nom = ? WHERE nom = ?');
    $stmt->execute(['Santé', 'SantA@']);

    echo "✅ Catégorie mise à jour : SantA@ → Santé\n";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
