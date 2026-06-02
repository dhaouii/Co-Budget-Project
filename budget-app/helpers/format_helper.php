<?php
/**
 * Helpers de formatage
 * Dates, montants, devises, pourcentages
 */

/**
 * Formate un montant avec devise
 * Exemple : formatMoney(1234.56, 'TND') => "1 234,56 TND"
 */
function formatMoney($amount, $currency = 'TND') {
    $formatted = number_format((float) $amount, 2, ',', ' ');
    return $formatted . ' ' . $currency;
}

/**
 * Formate une date au format français
 * Exemple : formatDate('2025-06-12') => "12 juin 2025"
 */
function formatDate($date) {
    if (empty($date)) {
        return '';
    }

    $months = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    ];

    $dateObj = new DateTime($date);
    $day = $dateObj->format('d');
    $month = $dateObj->format('n');
    $year = $dateObj->format('Y');

    return $day . ' ' . $months[$month] . ' ' . $year;
}

/**
 * Formate une date et heure
 * Exemple : formatDateTime('2025-06-12 14:30:00') => "12 juin 2025 à 14h30"
 */
function formatDateTime($datetime) {
    if (empty($datetime)) {
        return '';
    }

    $dateObj = new DateTime($datetime);
    $date = formatDate($dateObj->format('Y-m-d'));
    $time = $dateObj->format('H\hi');

    return $date . ' à ' . $time;
}

/**
 * Calcule et formate un pourcentage
 * Exemple : getPercentage(800, 1000) => "80%"
 */
function getPercentage($consumed, $limit) {
    if ($limit == 0) {
        return '0%';
    }
    $percentage = round(($consumed / $limit) * 100, 1);
    return $percentage . '%';
}

/**
 * Retourne la classe CSS de couleur selon un pourcentage
 * Pour usage dans les barres de progression
 */
function getPercentageClass($consumed, $limit) {
    if ($limit == 0) {
        return 'bg-gray-300';
    }

    $percentage = ($consumed / $limit) * 100;

    if ($percentage < 50) {
        return 'bg-green-500';
    } elseif ($percentage < 80) {
        return 'bg-yellow-500';
    } else {
        return 'bg-red-500';
    }
}

/**
 * Tronque une chaîne et ajoute "..."
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Formate un nombre avec séparateurs de milliers
 */
function formatNumber($number, $decimals = 2) {
    return number_format((float) $number, $decimals, ',', ' ');
}

/**
 * Retourne la différence de temps en format lisible
 * Exemple : timeAgo('2025-06-12 10:00:00') => "il y a 2 heures"
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $dateObj = new DateTime($datetime);
    $interval = $dateObj->diff($now);

    if ($interval->y > 0) {
        return 'il y a ' . $interval->y . ' an' . ($interval->y > 1 ? 's' : '');
    }
    if ($interval->m > 0) {
        return 'il y a ' . $interval->m . ' mois';
    }
    if ($interval->d > 0) {
        return 'il y a ' . $interval->d . ' jour' . ($interval->d > 1 ? 's' : '');
    }
    if ($interval->h > 0) {
        return 'il y a ' . $interval->h . ' heure' . ($interval->h > 1 ? 's' : '');
    }
    if ($interval->i > 0) {
        return 'il y a ' . $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
    }

    return 'à l\'instant';
}
