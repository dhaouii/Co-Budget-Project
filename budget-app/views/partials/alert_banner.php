<?php
/**
 * Composant alerte réutilisable
 * Affiche une alerte avec icône, message et bouton fermer
 * Paramètres : $message, $type (success/danger/warning/info), $dismissible (boolean)
 */

if (!isset($message) || empty($message)) return;

$type = $type ?? 'info';
$dismissible = $dismissible ?? TRUE;

$bgClass = match($type) {
    'success' => 'alert-success',
    'danger' => 'alert-danger',
    'warning' => 'alert-warning',
    default => 'alert-info'
};

$icon = match($type) {
    'success' => 'check-circle',
    'danger' => 'alert-circle',
    'warning' => 'alert-triangle',
    default => 'info'
};
?>

<div class="alert <?php echo $bgClass; ?>" role="alert" style="
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-base);
">
    <div style="display: flex; align-items: center; gap: var(--spacing-base);">
        <i data-lucide="<?php echo $icon; ?>" style="width: 18px; height: 18px;"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>

    <?php if ($dismissible): ?>
        <button
            type="button"
            onclick="this.parentElement.style.display='none'"
            style="
                background: none;
                border: none;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
            "
            aria-label="Fermer"
        >
            <i data-lucide="x" style="width: 18px; height: 18px;"></i>
        </button>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
