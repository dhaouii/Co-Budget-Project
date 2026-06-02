<?php
/**
 * Helpers pour l'envoi d'emails
 * Validation de compte, notifications
 */

/**
 * Envoie un email de validation de compte
 * Pour développement : log l'email dans un fichier si mail() n'est pas disponible
 */
function sendValidationEmail($email, $userName) {
    $subject = "Validation de votre compte " . APP_NAME;
    $message = "Bonjour $userName,\n\n";
    $message .= "Votre compte a été créé avec succès.\n";
    $message .= "Un administrateur doit valider votre compte avant que vous puissiez vous connecter.\n";
    $message .= "Vous recevrez une notification dès que votre compte sera activé.\n\n";
    $message .= "Cordialement,\nL'équipe " . APP_NAME;

    // Log dans un fichier en développement
    logEmail($email, $subject, $message);

    // Essayer d'envoyer via mail() si disponible
    if (function_exists('mail')) {
        $headers = "From: noreply@budget.app\r\nContent-Type: text/plain; charset=UTF-8";
        return mail($email, $subject, $message, $headers);
    }

    return TRUE; // Simule l'envoi si mail() n'est pas disponible
}

/**
 * Envoie un email de confirmation d'activation
 */
function sendActivationEmail($email, $userName) {
    $subject = "Votre compte a été activé !";
    $message = "Bonjour $userName,\n\n";
    $message .= "Votre compte a été validé et activé avec succès.\n";
    $message .= "Vous pouvez maintenant vous connecter à " . APP_NAME . "\n";
    $message .= "URL : " . BASE_URL . "\n\n";
    $message .= "Cordialement,\nL'équipe " . APP_NAME;

    logEmail($email, $subject, $message);

    if (function_exists('mail')) {
        $headers = "From: noreply@budget.app\r\nContent-Type: text/plain; charset=UTF-8";
        return mail($email, $subject, $message, $headers);
    }

    return TRUE;
}

/**
 * Envoie une notification de réinitialisation de mot de passe
 */
function sendPasswordResetEmail($email, $resetLink) {
    $subject = "Réinitialisation de votre mot de passe";
    $message = "Bonjour,\n\n";
    $message .= "Une demande de réinitialisation de mot de passe a été effectuée.\n";
    $message .= "Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :\n";
    $message .= $resetLink . "\n\n";
    $message .= "Ce lien est valable 1 heure.\n";
    $message .= "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n";
    $message .= "Cordialement,\nL'équipe " . APP_NAME;

    logEmail($email, $subject, $message);

    if (function_exists('mail')) {
        $headers = "From: noreply@budget.app\r\nContent-Type: text/plain; charset=UTF-8";
        return mail($email, $subject, $message, $headers);
    }

    return TRUE;
}

/**
 * Log un email dans un fichier (pour développement/debug)
 */
function logEmail($to, $subject, $message) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, TRUE);
    }

    $logFile = $logDir . '/emails.log';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: $to\n";
    $logEntry .= "SUBJECT: $subject\n";
    $logEntry .= "MESSAGE:\n$message\n";
    $logEntry .= str_repeat("-", 80) . "\n\n";

    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
