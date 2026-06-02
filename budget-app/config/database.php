<?php
/**
 * Configuration de connexion PDO à MySQL
 * Lit les variables d'environnement injectées par Docker
 * Charset : utf8mb4 pour support complet des caractères Unicode
 */

try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'budget_db';
    $user = getenv('DB_USER') ?: 'budget_user';
    $pass = getenv('DB_PASS') ?: 'budget_pass';
    $port = getenv('DB_PORT') ?: '3306';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
