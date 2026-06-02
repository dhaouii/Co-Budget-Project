<?php
/**
 * Configuration globale de l'application
 * Constantes : noms, URLs, durées de session, rôles autorisés
 */

define('APP_NAME', 'Budget Collaboratif');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost:8082');

// Durée de session en secondes (1 heure)
define('SESSION_LIFETIME', 3600);

// Rôles d'utilisateurs
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'utilisateur');
define('ROLE_GUEST', 'visiteur');

// Statuts d'utilisateurs
define('STATUS_INACTIVE', 'inactif');
define('STATUS_ACTIVE', 'actif');
define('STATUS_SUSPENDED', 'suspendu');

// Types de périodes de budgets
define('PERIOD_WEEKLY', 'hebdomadaire');
define('PERIOD_MONTHLY', 'mensuel');
define('PERIOD_CUSTOM', 'personnalise');

// Types de transactions
define('TRANSACTION_INCOME', 'revenu');
define('TRANSACTION_EXPENSE', 'depense');

// Pagination
define('ITEMS_PER_PAGE', 20);

// Seuil d'alerte pour budgets (%)
define('ALERT_THRESHOLD', 80);
