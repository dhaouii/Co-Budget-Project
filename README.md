# Budget Collaboratif - Application Web de Gestion de Budget Personnel

Une application web moderne pour gérer collaborativement votre budget personnel. Créez des budgets, suivez vos dépenses, et partagez vos budgets avec d'autres utilisateurs.

## Stack Technique

- **Frontend :** HTML, CSS, JavaScript (vanilla)
- **Backend :** PHP 8.2
- **Base de données :** MySQL 8.0
- **Déploiement :** Docker Compose

## Installation & Démarrage

### Prérequis

- Docker Desktop (sur macOS, Windows) ou Docker + Docker Compose (Linux)
- Terminal/Ligne de commande

### Étapes de démarrage

1. **Clonez ou accédez au répertoire du projet :**
   ```bash
   cd /Users/hibadhaoui/Desktop/co\ budget\ project
   ```

2. **Lancez les conteneurs Docker :**
   ```bash
   docker-compose up -d
   ```

3. **Vérifiez que les conteneurs sont en cours d'exécution :**
   ```bash
   docker-compose ps
   ```

4. **Ouvrez l'application dans votre navigateur :**
   - Application : [http://localhost:8080](http://localhost:8080)
   - phpMyAdmin : [http://localhost:8081](http://localhost:8081)

## Comptes de test

Après le démarrage, les comptes suivants sont disponibles :

| Email | Mot de passe | Rôle | Statut |
|-------|-------------|------|--------|
| admin@budget.app | (hashé en BD) | Admin | Actif |
| jean.dupont@email.com | (hashé en BD) | Utilisateur | Actif |
| sophie.martin@email.com | (hashé en BD) | Utilisateur | Actif |

Les mots de passe sont hashés en bcrypt. Pour tester avec le compte admin, vous pouvez modifier `database/seed.sql` ou utiliser la base de données phpMyAdmin.

## Architecture du projet

```
budget-app/
├── config/              # Configuration (BD, constantes)
├── modules/             # Modules métier (auth, transactions, budgets, etc.)
├── views/               # Templates HTML (layouts, partials)
├── assets/              # Ressources (CSS, JS, images)
├── helpers/             # Fonctions réutilisables
├── database/            # Schéma SQL et données de test
└── index.php            # Point d'entrée principal
```

## Fonctionnalités principales

### Authentification
- Inscription avec validation admin
- Connexion sécurisée (bcrypt)
- Rôles : Admin, Utilisateur, Visiteur
- Gestion de session

### Transactions
- Ajouter revenus/dépenses
- Filtrage par type, catégorie, date
- Modification et suppression
- Association aux budgets

### Budgets
- Budgets individuels ou partagés
- Suivi de consommation avec barre de progression
- Alertes au-delà de 80%
- Gestion des membres (invitation par email)

### Dashboard
- Statistiques financières (revenus, dépenses, solde)
- Graphiques : répartition par catégorie (Chart.js)
- Graphiques : évolution mensuelle (Chart.js)
- 5 dernières transactions

### Administration
- Dashboard statistiques globales
- Gestion des utilisateurs (actif/inactif/suspendu)
- Validation des comptes
- Changement de rôle

## Commandes Docker utiles

```bash
# Démarrer en arrière-plan
docker-compose up -d

# Arrêter les conteneurs
docker-compose stop

# Arrêter et supprimer (attention : BD sera aussi supprimée)
docker-compose down

# Voir les logs PHP
docker-compose logs web

# Voir les logs MySQL
docker-compose logs db

# Reconstruire après modification du Dockerfile
docker-compose up -d --build

# Réinitialiser la base de données
docker-compose down -v && docker-compose up -d
```

## Sécurité

L'application implémente plusieurs mesures de sécurité :

- ✓ Requêtes SQL préparées (PDO) - Prévention SQL Injection
- ✓ Hashage bcrypt des mots de passe
- ✓ Sanitisation des inputs avec htmlspecialchars
- ✓ Contrôle d'accès par rôle (admin, utilisateur)
- ✓ Vérification de propriété des objets
- ✓ Protection CSRF avec tokens
- ✓ En-têtes HTTP sécurisés

## Points pour la soutenance

### Module Authentification
- Flux inscription → validation admin → activation
- Schéma utilisateurs avec rôles et statuts
- Sessions PHP et hashage bcrypt

### Module Transactions
- CRUD complet avec requêtes préparées
- Filtrage dynamique (type, catégorie, date)
- Association aux budgets et catégories

### Module Budgets
- Relation N-N avec budget_membres
- Calcul de consommation et alertes
- Budgets partagés avec gestion de membres

### Tableau de bord
- API JSON (stats_api.php) pour les données
- Graphiques Chart.js (camembert + courbe)
- Statistiques en temps réel

### Admin
- Contrôle d'accès par rôle
- Validation et suspension de comptes
- Statistiques globales

## Structure de la base de données

**Tables principales :**
- `utilisateurs` - Comptes utilisateur avec rôles
- `budgets` - Budgets individuels ou partagés
- `budget_membres` - Relation N-N (utilisateurs dans budgets)
- `transactions` - Revenus/dépenses
- `categories` - Catégories (défaut ou personnalisées)
- `alertes` - Notifications de dépassement
- `commentaires` - Commentaires sur transactions

## Design System

**Palette :**
- Accent : `#2DD4BF` (Teal)
- Texte principal : `#1C1C1E` (Charcoal)
- Arrière-plan : `#FAFAFA` (Off-white)
- Danger : `#EF4444` (Red)
- Succès : `#10B981` (Green)

**Typos :**
- Corps : Inter
- Titres : DM Sans

**Icônes :** Lucide Icons (CDN)

## Troubleshooting

**L'app ne démarre pas :**
```bash
# Vérifiez les logs
docker-compose logs
```

**Port 8080 déjà utilisé :**
Modifiez le port dans `docker-compose.yml`

**Erreur de connexion à la BD :**
Attendez 10-15 secondes après `docker-compose up` - MySQL met du temps à démarrer

**Réinitialiser la BD :**
```bash
docker-compose down -v
docker-compose up -d
```

## Ressources

- [PHP 8.2 Docs](https://www.php.net/docs.php)
- [MySQL 8.0 Docs](https://dev.mysql.com/doc/)
- [Chart.js](https://www.chartjs.org/)
- [Lucide Icons](https://lucide.dev/)

---

**Créé pour un projet universitaire de gestion de budget collaboratif.**
