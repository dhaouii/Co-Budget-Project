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

// Modification de couleur d'une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_color'])) {
    $catId = intval($_POST['cat_id'] ?? 0);
    $newColor = sanitize($_POST['new_color'] ?? '#2DD4BF');

    try {
        $stmt = $pdo->prepare('UPDATE categories SET couleur = ? WHERE id = ?');
        $stmt->execute([$newColor, $catId]);
        $success = 'Couleur modifiée !';

        // Reload
        $reloadStmt = $pdo->prepare(
            'SELECT * FROM categories WHERE id_utilisateur IS NULL OR id_utilisateur = ? ORDER BY est_defaut DESC, nom'
        );
        $reloadStmt->execute([$userId]);
        $categories = $reloadStmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Erreur lors de la modification.';
    }
}
// Ajout de catégorie personnalisée
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

// Suppression (autorisée pour TOUTES les catégories, même par défaut)
if (isset($_GET['delete'])) {
    $catId = intval($_GET['delete']);
    try {
        // Met à NULL les transactions liées avant de supprimer la catégorie
        $updateTransStmt = $pdo->prepare('UPDATE transactions SET id_categorie = NULL WHERE id_categorie = ?');
        $updateTransStmt->execute([$catId]);

        // Supprime la catégorie
        $delStmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $delStmt->execute([$catId]);
        $success = 'Catégorie supprimée.';

        // Reload la liste
        $reloadStmt = $pdo->prepare(
            'SELECT * FROM categories WHERE id_utilisateur IS NULL OR id_utilisateur = ? ORDER BY est_defaut DESC, nom'
        );
        $reloadStmt->execute([$userId]);
        $categories = $reloadStmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $error = 'Erreur lors de la suppression.';
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
                        <!-- Color picker pour modifier la couleur -->
                        <form method="POST" style="margin: 0; display: flex; align-items: center;">
                            <input type="hidden" name="edit_color" value="1">
                            <input type="hidden" name="cat_id" value="<?php echo $cat['id']; ?>">
                            <label style="
                                position: relative;
                                cursor: pointer;
                                display: inline-block;
                            " title="Cliquez pour changer la couleur">
                                <div style="
                                    width: 28px;
                                    height: 28px;
                                    border-radius: 6px;
                                    background-color: <?php echo htmlspecialchars($cat['couleur']); ?>;
                                    border: 2px solid white;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    cursor: pointer;
                                    transition: transform 0.2s;
                                " onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></div>
                                <input
                                    type="color"
                                    name="new_color"
                                    value="<?php echo htmlspecialchars($cat['couleur']); ?>"
                                    onchange="this.form.submit()"
                                    style="
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        width: 100%;
                                        height: 100%;
                                        opacity: 0;
                                        cursor: pointer;
                                    "
                                >
                            </label>
                        </form>
                        <span><?php echo htmlspecialchars($cat['nom']); ?></span>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm js-delete-btn" data-url="?delete=<?php echo $cat['id']; ?>" data-message="Êtes-vous sûr de vouloir supprimer cette catégorie ?" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Supprimer
                    </button>
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
