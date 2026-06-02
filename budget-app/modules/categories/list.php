<?php
/**
 * Liste et gestion des catégories
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../helpers/auth_helper.php';
require_once '../../helpers/validation_helper.php';

requireLogin();
$userId = getCurrentUserId();

$pageTitle = 'Catégories';

try {
    // Récupère les catégories (par défaut + utilisateur)
    $stmt = $pdo->prepare(
        'SELECT * FROM categories WHERE id_utilisateur IS NULL OR id_utilisateur = ? ORDER BY est_defaut DESC, nom'
    );
    $stmt->execute([$userId]);
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $categories = [];
}

$error = '';
$success = '';

// Ajout de catégorie personnalisée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom'] ?? '');
    $couleur = sanitize($_POST['couleur'] ?? '#2DD4BF');

    if (empty($nom)) {
        $error = 'Le nom est requis.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (nom, couleur, id_utilisateur) VALUES (?, ?, ?)');
            $stmt->execute([$nom, $couleur, $userId]);
            $success = 'Catégorie créée !';

            // Reload
            $reloadStmt = $pdo->prepare(
                'SELECT * FROM categories WHERE id_utilisateur IS NULL OR id_utilisateur = ? ORDER BY est_defaut DESC, nom'
            );
            $reloadStmt->execute([$userId]);
            $categories = $reloadStmt->fetchAll();
        } catch (PDOException $e) {
            $error = 'Erreur lors de la création.';
        }
    }
}

// Suppression
if (isset($_GET['delete'])) {
    $catId = intval($_GET['delete']);
    try {
        $delStmt = $pdo->prepare('DELETE FROM categories WHERE id = ? AND id_utilisateur = ?');
        $delStmt->execute([$catId, $userId]);
        $success = 'Catégorie supprimée.';
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
    }
}

require_once '../../views/layouts/header.php';
?>

<h1>Catégories</h1>

<?php if (!empty($error)): ?>
    <?php $message = $error; $type = 'danger'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <?php $message = $success; $type = 'success'; require_once '../../views/partials/alert_banner.php'; ?>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
    <!-- Catégories existantes -->
    <div class="card">
        <h3 style="margin-top: 0;">Vos catégories</h3>

        <div style="display: flex; flex-direction: column; gap: var(--spacing-base);">
            <?php foreach ($categories as $cat): ?>
                <div style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: var(--spacing-base);
                    background-color: var(--color-bg);
                    border-radius: var(--radius);
                ">
                    <div style="display: flex; align-items: center; gap: var(--spacing-base);">
                        <div style="
                            width: 20px;
                            height: 20px;
                            border-radius: 4px;
                            background-color: <?php echo htmlspecialchars($cat['couleur']); ?>;
                        "></div>
                        <span><?php echo htmlspecialchars($cat['nom']); ?></span>
                        <?php if ($cat['est_defaut']): ?>
                            <span class="badge badge-primary" style="font-size: 10px;">Défaut</span>
                        <?php endif; ?>
                    </div>

                    <?php if (!$cat['est_defaut']): ?>
                        <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Ajouter une catégorie -->
    <div class="card">
        <h3 style="margin-top: 0;">Nouvelle catégorie</h3>

        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Ex: Restaurant" required>
            </div>

            <div class="form-group">
                <label for="couleur">Couleur</label>
                <input type="color" id="couleur" name="couleur" value="#2DD4BF">
            </div>

            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<?php require_once '../../views/layouts/footer.php'; ?>
