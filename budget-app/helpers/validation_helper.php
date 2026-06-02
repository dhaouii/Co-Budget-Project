<?php
/**
 * Helpers de validation
 * Nettoyage des inputs, validation des formats (email, montant, date)
 */

/**
 * Nettoie une chaîne de caractères
 * Supprime les espaces, échappe les caractères spéciaux
 */
function sanitize($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Valide une adresse email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
}

/**
 * Valide un montant (doit être positif et numérique)
 */
function validateAmount($amount) {
    if (!is_numeric($amount)) {
        return FALSE;
    }
    $amount = (float) $amount;
    return $amount > 0;
}

/**
 * Valide une date au format YYYY-MM-DD
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Valide une chaîne de texte simple
 * (pas vide, longueur minimum)
 */
function validateText($text, $minLength = 1, $maxLength = 255) {
    $text = trim($text);
    return strlen($text) >= $minLength && strlen($text) <= $maxLength;
}

/**
 * Valide un mot de passe (min 8 caractères)
 */
function validatePassword($password) {
    return strlen($password) >= 8;
}

/**
 * Valide un statut utilisateur
 */
function validateStatus($status) {
    $validStatuses = ['inactif', 'actif', 'suspendu'];
    return in_array($status, $validStatuses);
}

/**
 * Valide un rôle utilisateur
 */
function validateRole($role) {
    $validRoles = ['visiteur', 'utilisateur', 'admin'];
    return in_array($role, $validRoles);
}

/**
 * Valide un type de transaction
 */
function validateTransactionType($type) {
    return in_array($type, ['revenu', 'depense']);
}

/**
 * Valide une devise (3 lettres)
 */
function validateCurrency($currency) {
    return preg_match('/^[A-Z]{3}$/', $currency) === 1;
}
