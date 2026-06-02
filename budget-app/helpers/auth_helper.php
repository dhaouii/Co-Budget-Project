<?php
/**
 * Helpers d'authentification
 * Sessions PHP, vérification de rôles, hashage de mots de passe bcrypt
 */

/**
 * Initialise la session si elle n'existe pas
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Vérifie si l'utilisateur est connecté
 * Redirige vers login sinon
 */
function requireLogin() {
    initSession();
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/modules/auth/login.php');
        exit;
    }
}

/**
 * Vérifie si l'utilisateur possède le rôle spécifié
 * Redirige avec erreur sinon
 */
function requireRole($requiredRole) {
    requireLogin();
    $user = getCurrentUser();
    if ($user['role'] !== $requiredRole) {
        http_response_code(403);
        die('Accès refusé. Vous n\'avez pas les permissions nécessaires.');
    }
}

/**
 * Retourne les données de l'utilisateur connecté
 * Retourne null si pas connecté
 */
function getCurrentUser() {
    initSession();
    return $_SESSION['user'] ?? null;
}

/**
 * Vérifie si l'utilisateur actuel est un visiteur (mode lecture seule)
 */
function isGuest() {
    $user = getCurrentUser();
    return $user && ($user['role'] === ROLE_GUEST || !empty($user['is_guest']));
}

/**
 * Bloque l'accès aux visiteurs (actions de modification)
 * Redirige avec message d'erreur
 */
function blockGuest($message = 'Mode visiteur : Cette action nécessite un compte. Veuillez vous inscrire.') {
    if (isGuest()) {
        header('Location: ' . BASE_URL . '/modules/auth/register.php?guest_blocked=1');
        exit;
    }
}

/**
 * Retourne l'ID de l'utilisateur connecté
 */
function getCurrentUserId() {
    $user = getCurrentUser();
    return $user['id'] ?? null;
}

/**
 * Hash un mot de passe en bcrypt
 */
function hashPassword($plainPassword) {
    return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Vérifie un mot de passe contre son hash
 */
function verifyPassword($plainPassword, $hash) {
    return password_verify($plainPassword, $hash);
}

/**
 * Détruit la session de l'utilisateur
 */
function logout() {
    initSession();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
}

/**
 * Génère un token CSRF et le stocke en session
 */
function generateCSRFToken() {
    initSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide un token CSRF
 */
function validateCSRFToken($token) {
    initSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
