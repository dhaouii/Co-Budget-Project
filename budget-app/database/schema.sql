-- =====================================================
-- Schéma de base de données - Application Budget
-- Création des tables : utilisateurs, catégories, budgets,
-- transactions, alertes, commentaires et relations N-N
-- =====================================================

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('visiteur', 'utilisateur', 'admin') DEFAULT 'utilisateur',
  statut ENUM('inactif', 'actif', 'suspendu') DEFAULT 'inactif',
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_statut (statut),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  icone VARCHAR(50),
  couleur VARCHAR(7),
  est_defaut BOOLEAN DEFAULT FALSE,
  id_utilisateur INT,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  INDEX idx_utilisateur (id_utilisateur),
  INDEX idx_defaut (est_defaut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des budgets
CREATE TABLE IF NOT EXISTS budgets (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(150) NOT NULL,
  montant_limite DECIMAL(10, 2) NOT NULL,
  devise VARCHAR(3) DEFAULT 'TND',
  periode ENUM('hebdomadaire', 'mensuel', 'personnalise') DEFAULT 'mensuel',
  date_debut DATE NOT NULL,
  date_fin DATE,
  est_partage BOOLEAN DEFAULT FALSE,
  id_createur INT NOT NULL,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_createur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  INDEX idx_createur (id_createur),
  INDEX idx_partage (est_partage),
  INDEX idx_periode (periode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table d'association budget-utilisateurs (N-N pour budgets partagés)
CREATE TABLE IF NOT EXISTS budget_membres (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_budget INT NOT NULL,
  id_utilisateur INT NOT NULL,
  role_membre ENUM('proprietaire', 'membre') DEFAULT 'membre',
  date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_budget) REFERENCES budgets(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  UNIQUE KEY unique_budget_user (id_budget, id_utilisateur),
  INDEX idx_budget (id_budget),
  INDEX idx_utilisateur (id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des transactions
CREATE TABLE IF NOT EXISTS transactions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  montant DECIMAL(10, 2) NOT NULL,
  type ENUM('revenu', 'depense') NOT NULL,
  description VARCHAR(255),
  date_transaction DATE NOT NULL,
  id_utilisateur INT NOT NULL,
  id_budget INT,
  id_categorie INT,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  FOREIGN KEY (id_budget) REFERENCES budgets(id) ON DELETE SET NULL,
  FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_utilisateur (id_utilisateur),
  INDEX idx_budget (id_budget),
  INDEX idx_categorie (id_categorie),
  INDEX idx_date (date_transaction),
  INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des alertes
CREATE TABLE IF NOT EXISTS alertes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  seuil_pourcentage TINYINT DEFAULT 80,
  message VARCHAR(255),
  date_declenchement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  est_lue BOOLEAN DEFAULT FALSE,
  id_budget INT NOT NULL,
  id_utilisateur INT NOT NULL,
  FOREIGN KEY (id_budget) REFERENCES budgets(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  INDEX idx_budget (id_budget),
  INDEX idx_utilisateur (id_utilisateur),
  INDEX idx_lue (est_lue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commentaires sur transactions
CREATE TABLE IF NOT EXISTS commentaires (
  id INT PRIMARY KEY AUTO_INCREMENT,
  contenu TEXT NOT NULL,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  id_transaction INT NOT NULL,
  id_utilisateur INT NOT NULL,
  FOREIGN KEY (id_transaction) REFERENCES transactions(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  INDEX idx_transaction (id_transaction),
  INDEX idx_utilisateur (id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
