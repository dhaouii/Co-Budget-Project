-- =====================================================
-- Données de test initiales
-- Insère : 1 admin, 2 utilisateurs test, 5 catégories par défaut
-- =====================================================

-- Insérer un utilisateur admin
-- Mot de passe : "password123"
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, statut) VALUES
('Admin', 'Budget', 'admin@budget.app', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KLm', 'admin', 'actif');

-- Insérer 3 utilisateurs de test avec vos noms
-- Mot de passe pour tous : "password123"
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, statut) VALUES
('Hibadhaoui', 'Hiba', 'hibadhaoui@budget.app', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KLm', 'utilisateur', 'actif'),
('Ben Mansour', 'Ahmend', 'ahmend.mansour@budget.app', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KLm', 'utilisateur', 'actif'),
('Adsyari', 'Mohamed', 'mohamed.adsyari@budget.app', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KLm', 'utilisateur', 'actif');

-- Insérer les catégories par défaut (pour tous : id_utilisateur = NULL)
INSERT INTO categories (nom, icone, couleur, est_defaut, id_utilisateur) VALUES
('Alimentation', 'utensils', '#F59E0B', TRUE, NULL),
('Transport', 'car', '#3B82F6', TRUE, NULL),
('Loisirs', 'music', '#8B5CF6', TRUE, NULL),
('Logement', 'home', '#EF4444', TRUE, NULL),
('Santé', 'heart', '#10B981', TRUE, NULL);

-- Insérer 1 budget pour Jean Dupont
INSERT INTO budgets (nom, montant_limite, devise, periode, date_debut, date_fin, est_partage, id_createur) VALUES
('Budget juin 2025', 2000.00, 'TND', 'mensuel', '2025-06-01', '2025-06-30', FALSE, 1);

-- Insérer 1 budget partagé
INSERT INTO budgets (nom, montant_limite, devise, periode, date_debut, date_fin, est_partage, id_createur) VALUES
('Budget couple', 5000.00, 'TND', 'mensuel', '2025-06-01', '2025-06-30', TRUE, 1);

-- Ajouter les membres au budget partagé (budget_id = 2)
-- Budget partagé entre Hiba et Ahmend
INSERT INTO budget_membres (id_budget, id_utilisateur, role_membre) VALUES
(2, 1, 'proprietaire'),
(2, 2, 'membre');

-- Insérer quelques transactions de test
-- Transactions de Hiba (id=1)
INSERT INTO transactions (montant, type, description, date_transaction, id_utilisateur, id_budget, id_categorie) VALUES
(100.00, 'depense', 'Courses au marché', '2025-06-01', 1, 1, 1),
(75.50, 'depense', 'Essence voiture', '2025-06-02', 1, 1, 2),
(2000.00, 'revenu', 'Salaire juin', '2025-06-01', 1, 1, NULL),
(150.00, 'depense', 'Cinéma et popcorn', '2025-06-05', 1, 1, 3),
-- Transactions d'Ahmend (id=2) - budget partagé avec Hiba
(800.00, 'revenu', 'Salaire juin', '2025-06-01', 2, 2, NULL),
(200.00, 'depense', 'Courses alimentaires', '2025-06-03', 2, 2, 1);
