<?php
/**
 * Traitement du formulaire de connexion
 * Valide les identifiants, crée la session, redirige vers dashboard
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

initSession();

$error = '';
$success = '';

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation des inputs
    if (empty($email) || empty($password)) {
        $error = 'Email et mot de passe sont requis.';
    } elseif (!validateEmail($email)) {
        $error = 'Format d\'email invalide.';
    } else {
        try {
            // Recherche l'utilisateur par email
            $query = $pdo->prepare('SELECT id, nom, prenom, email, mot_de_passe, role, statut FROM utilisateurs WHERE email = ?');
            $query->execute([$email]);
            $user = $query->fetch();

            if ($user && verifyPassword($password, $user['mot_de_passe'])) {
                // Vérifie que le compte est actif
                if ($user['statut'] !== 'actif') {
                    $error = 'Votre compte n\'est pas encore activé. Veuillez contacter un administrateur.';
                } else {
                    // Crée la session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];

                    // Redirige vers dashboard
                    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
                    exit;
                }
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur serveur. Veuillez réessayer plus tard.';
            error_log('Database error in login: ' . $e->getMessage());
        }
    }
}

// Affiche la vue de connexion
require_once '../../views/auth/login_view.php';
