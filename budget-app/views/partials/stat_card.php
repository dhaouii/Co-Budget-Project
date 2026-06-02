<?php
/**
 * Composant carte statistique réutilisable
 * Affiche : icône, label, valeur, variation en %
 * Paramètres : $label, $value, $icon, $variation, $currency
 */

if (!isset($label) || !isset($value)) return;

$icon = $icon ?? 'activity';
$variation = $variation ?? 0;
$currency = $currency ?? 'TND';
$subtitle = $subtitle ?? '';
$variationClass = $variation >= 0 ? 'text-success' : 'text-danger';
$variationIcon = $variation >= 0 ? 'arrow-up' : 'arrow-down';

// Déterminer la couleur selon le label
$colorMap = [
    'Revenus' => [
        'bg' => '#F0FDF4',
        'border' => '#DCFCE7',
        'icon_bg' => '#DCFCE7',
        'icon_color' => '#15803D',
        'text_color' => '#15803D'
    ],
    'Dépenses' => [
        'bg' => '#FEF2F2',
        'border' => '#FEE2E2',
        'icon_bg' => '#FEE2E2',
        'icon_color' => '#991B1B',
        'text_color' => '#991B1B'
    ],
    'Solde' => [
        'bg' => '#EFF6FF',
        'border' => '#DBEAFE',
        'icon_bg' => '#DBEAFE',
        'icon_color' => '#1E40AF',
        'text_color' => '#1E40AF'
    ],
    'Budgets actifs' => [
        'bg' => '#FAF5FF',
        'border' => '#F3E8FF',
        'icon_bg' => '#F3E8FF',
        'icon_color' => '#6B21A8',
        'text_color' => '#6B21A8'
    ]
];

$colors = $colorMap[$label] ?? [
    'bg' => '#F5F5F7',
    'border' => '#E5E7EB',
    'icon_bg' => '#E5E7EB',
    'icon_color' => '#0066FF',
    'text_color' => '#0066FF'
];
?>

<div class="card" style="
    padding: var(--spacing-lg);
    background-color: <?php echo $colors['bg']; ?>;
    border-color: <?php echo $colors['border']; ?>;
">
    <div style="
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: var(--spacing-base);
    ">
        <div style="flex: 1;">
            <p style="
                font-size: 14px;
                color: var(--color-text-secondary);
                margin-bottom: var(--spacing-sm);
            ">
                <?php echo htmlspecialchars($label); ?>
            </p>
            <h3 style="
                font-size: 28px;
                font-weight: 700;
                color: var(--color-text-primary);
                margin: 0 0 var(--spacing-sm) 0;
            ">
                <?php echo htmlspecialchars($value); ?> <?php echo isset($currency) && $currency !== 'TND' ? $currency : 'TND'; ?>
            </h3>
            <?php if ($subtitle): ?>
                <p style="
                    font-size: 12px;
                    color: var(--color-text-tertiary);
                    margin: 0;
                ">
                    <?php echo htmlspecialchars($subtitle); ?>
                </p>
            <?php endif; ?>
        </div>

        <div style="
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background-color: <?php echo $colors['icon_bg']; ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            color: <?php echo $colors['icon_color']; ?>;
        ">
            <i data-lucide="<?php echo $icon; ?>" style="width: 24px; height: 24px;"></i>
        </div>
    </div>

    <?php if ($variation !== 0): ?>
        <div style="
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: 13px;
        " class="<?php echo $variationClass; ?>">
            <i data-lucide="<?php echo $variationIcon; ?>" style="width: 14px; height: 14px;"></i>
            <span><?php echo abs($variation); ?>% depuis le mois dernier</span>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
