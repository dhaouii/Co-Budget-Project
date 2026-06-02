<?php
/**
 * Traitement du formulaire d'inscription
 * Crée le compte avec statut inactif, envoie email de validation
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';
require_once '../../helpers/mail_helper.php';

initSession();

$error = '';
$success = '';

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom'] ?? '');
    $prenom = sanitize($_POST['prenom'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation des inputs
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $error = 'Tous les champs sont requis.';
    } elseif (!validateEmail($email)) {
        $error = 'Format d\'email invalide.';
    } elseif (!validatePassword($password)) {
        $error = 'Le mot de passe doit contenir au minimum 8 caractères.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            // Vérifie si l'email existe déjà
            $query = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
            $query->execute([$email]);

            if ($query->fetch()) {
                $error = 'Un compte avec cet email existe déjà.';
            } else {
                // Hash le mot de passe
                $hashedPassword = hashPassword($password);

                // Insère le nouvel utilisateur avec statut 'inactif'
                $query = $pdo->prepare(
                    'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, statut)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );

                $query->execute([
                    $nom,
                    $prenom,
                    $email,
                    $hashedPassword,
                    ROLE_USER,
                    STATUS_INACTIVE
                ]);

                // Envoie un email de validation
                sendValidationEmail($email, $prenom);

                $success = 'Compte créé avec succès ! Un administrateur doit valider votre compte avant que vous puissiez vous connecter.';

                // Vide le formulaire
                $nom = $prenom = $email = '';
            }
        } catch (PDOException $e) {
            $error = 'Erreur serveur. Veuillez réessayer plus tard.';
            error_log('Database error in register: ' . $e->getMessage());
        }
    }
}

// Affiche la vue d'inscription
require_once '../../views/auth/register_view.php';
