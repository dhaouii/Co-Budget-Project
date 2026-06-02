<?php
/**
 * Déconnexion utilisateur
 * Détruit la session et redirige vers la page de connexion
 */

require_once '../../config/config.php';
require_once '../../helpers/auth_helper.php';

initSession();
logout();

// Redirige vers la page de connexion
header('Location: ' . BASE_URL . '/modules/auth/login.php');
exit;
