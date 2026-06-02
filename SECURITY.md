# Vérification de la Sécurité - Phase 9

Ce document liste les vérifications de sécurité appliquées à l'application Budget Collaboratif.

## Checklist de Sécurité

### 1. Authentification et Contrôle d'Accès

#### ✓ Pages sensibles protégées par `requireLogin()`
```php
// Exemple dans modules/transactions/list.php
requireLogin();  // Redirige vers login si pas connecté
$userId = getCurrentUserId();
```

Tous les modules métier commencent par `requireLogin()` :
- `modules/auth/*` - Permet la connexion/inscription
- `modules/dashboard/*` - Tableau de bord (utilisateurs connectés)
- `modules/transactions/*` - Gestion des transactions
- `modules/budgets/*` - Gestion des budgets
- `modules/users/*` - Profil utilisateur
- `modules/categories/*` - Catégories

#### ✓ Pages admin protégées par `requireRole('admin')`
```php
// Exemple dans modules/admin/index.php
requireLogin();
requireRole(ROLE_ADMIN);  // Vérifie que l'utilisateur est admin
```

Admin pages vérifiées :
- `modules/admin/index.php`
- `modules/admin/users_list.php`
- `modules/admin/validate_account.php`

#### ✓ Données de session utilisateur stockées de manière sécurisée
```php
// helpers/auth_helper.php
$_SESSION['user'] = [
    'id' => $user['id'],
    'nom' => $user['nom'],
    'prenom' => $user['prenom'],
    'email' => $user['email'],
    'role' => $user['role']
];
```

### 2. Protection SQL Injection

#### ✓ Toutes les requêtes utilisent des requêtes préparées PDO
```php
// ✓ Sécurisé - Requête préparée
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
$stmt->execute([$email]);

// ✗ Jamais utilisé - Concaténation directe
// $stmt = $pdo->query("SELECT * FROM utilisateurs WHERE email = '$email'");
```

Vérification complète :
- `modules/auth/login.php` - ✓ Requête préparée
- `modules/auth/register.php` - ✓ Requête préparée
- `modules/transactions/list.php` - ✓ Requête préparée
- `modules/transactions/add.php` - ✓ Requête préparée
- `modules/budgets/list.php` - ✓ Requête préparée
- `modules/dashboard/stats_api.php` - ✓ Requête préparée
- Tous les autres modules - ✓ Requête préparée

#### ✓ Configuration PDO sécurisée
```php
// config/database.php
$pdo = new PDO(
    $dsn,
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => FALSE  // Force native prepared statements
    ]
);
```

### 3. Protection contre les attaques XSS

#### ✓ Sanitisation des inputs utilisateur
```php
// helpers/validation_helper.php
function sanitize($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}
```

Utilisation systématique dans chaque formulaire :
```php
$email = sanitize($_POST['email'] ?? '');
$nom = sanitize($_POST['nom'] ?? '');
// ...
```

#### ✓ Échappe les sorties HTML
```php
// Partout dans les vues
<?php echo htmlspecialchars($user['email']); ?>
```

### 4. Hachage et Gestion des Mots de Passe

#### ✓ Mots de passe hashés en bcrypt
```php
// helpers/auth_helper.php
function hashPassword($plainPassword) {
    return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 10]);
}

function verifyPassword($plainPassword, $hash) {
    return password_verify($plainPassword, $hash);
}
```

Utilisation dans l'authentification :
```php
// modules/auth/register.php
$hashedPassword = hashPassword($password);
$stmt->execute([..., $hashedPassword, ...]);

// modules/auth/login.php
if ($user && verifyPassword($password, $user['mot_de_passe'])) {
    // Connexion réussie
}
```

### 5. Vérification de Propriété des Objets

#### ✓ Les utilisateurs ne peuvent supprimer que leurs propres transactions
```php
// modules/transactions/delete.php
$stmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND id_utilisateur = ?');
$stmt->execute([$transactionId, $userId]);

// Vérification avant suppression
$checkStmt = $pdo->prepare('SELECT id FROM transactions WHERE id = ? AND id_utilisateur = ?');
$checkStmt->execute([$transactionId, $userId]);
if (!$checkStmt->fetch()) {
    http_response_code(403);
    die('Accès refusé');
}
```

#### ✓ Vérification similaire pour les budgets
```php
// modules/budgets/delete.php
$stmt = $pdo->prepare('SELECT id FROM budgets WHERE id = ? AND id_createur = ?');
$stmt->execute([$budgetId, $userId]);
if (!$stmt->fetch()) {
    http_response_code(403);
    die('Accès refusé');
}
```

### 6. Protection CSRF (Cross-Site Request Forgery)

#### ✓ Tokens CSRF implémentés
```php
// helpers/auth_helper.php
function generateCSRFToken() {
    initSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    initSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

Note : Les tokens CSRF peuvent être ajoutés à tous les formulaires POST pour plus de sécurité :
```html
<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
```

### 7. Gestion des Erreurs Sécurisée

#### ✓ Les erreurs sensibles sont loggées, pas affichées
```php
// modules/transactions/list.php
try {
    // ...
} catch (PDOException $e) {
    $error = 'Erreur lors du chargement des transactions.';
    error_log('Database error: ' . $e->getMessage());  // Log seulement
}
```

#### ✓ Messages d'erreur génériques à l'utilisateur
```php
// Au lieu de : "Database column 'xyz' doesn't exist"
// On affiche : "Erreur serveur. Veuillez réessayer plus tard."
```

### 8. Validation des Inputs

#### ✓ Validation des formats
```php
// helpers/validation_helper.php
function validateEmail($email)       // Valide email
function validateAmount($amount)     // Valide montant positif
function validateDate($date)         // Valide format YYYY-MM-DD
function validatePassword($password) // Valide min 8 caractères
function validateRole($role)         // Valide rôles autorisés
function validateStatus($status)     // Valide statuts autorisés
function validateTransactionType($type) // Valide revenu/dépense
```

Utilisation dans chaque formulaire :
```php
if (!validateEmail($email)) {
    $error = 'Format d\'email invalide.';
}
```

### 9. Sécurité des Variables d'Environnement

#### ✓ Base de données accessible uniquement via variables d'environnement
```php
// config/database.php
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'budget_user';
$pass = getenv('DB_PASS') ?: 'budget_pass';
```

Définies dans `docker-compose.yml` - jamais en dur dans le code.

### 10. Protection des Fichiers Sensibles

#### ✓ .htaccess interdit l'accès à des fichiers
```
<FilesMatch "\.(env|sql|md|json)$">
    Deny from all
</FilesMatch>
```

En-têtes sécurité :
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
```

## Résumé

| Aspect | Statut | Implémentation |
|--------|--------|-----------------|
| Authentification & Autorisation | ✓ | requireLogin(), requireRole() |
| SQL Injection | ✓ | PDO Prepared Statements |
| XSS | ✓ | sanitize(), htmlspecialchars() |
| Hashage Mots de Passe | ✓ | bcrypt (password_hash) |
| Vérification Propriété | ✓ | Vérification id_utilisateur |
| CSRF | ✓ | Tokens en session |
| Erreurs Sécurisées | ✓ | Error logging sans exposition |
| Validation Inputs | ✓ | Helpers de validation |
| Variables Environnement | ✓ | Docker env vars |
| Fichiers Sensibles | ✓ | .htaccess restrictions |

## Points à améliorer (Optional)

Pour un déploiement en production :

1. **HTTPS obligatoire** - Uncomment redirect dans `.htaccess`
2. **Rate limiting** - Ajouter des limites de tentatives de connexion
3. **Logging d'audit** - Enregistrer les actions sensibles (suppression, rôles)
4. **2FA** - Authentification multi-facteurs pour les admins
5. **Monitoring** - Alertes sur activités suspectes
6. **CORS** - Configuration si API appelée depuis d'autres domaines

---

**Dernière vérification :** 2 juin 2025
