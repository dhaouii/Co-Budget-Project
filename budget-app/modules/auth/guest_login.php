<?php
/**
 * Connexion en tant que visiteur (mode lecture seule)
 * Permet d'explorer l'application sans créer de compte
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';

initSession();

// Création d'une session visiteur (mode lecture seule)
$_SESSION['user'] = [
    'id' => 0,
    'role' => ROLE_GUEST,
    'email' => 'visiteur@budget.app',
    'prenom' => 'Visiteur',
    'nom' => 'Invité',
    'is_guest' => true
];

// Redirection vers le tableau de bord
header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
exit;
