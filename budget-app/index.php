<?php
/**
 * Point d'entrée principal de l'application
 * Redirige vers le dashboard ou la page de connexion
 */

require_once 'config/config.php';
require_once 'helpers/auth_helper.php';

initSession();

// Si l'utilisateur est connecté, redirige vers le dashboard
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
    exit;
}

// Sinon, redirige vers la page de connexion
header('Location: ' . BASE_URL . '/modules/auth/login.php');
exit;
