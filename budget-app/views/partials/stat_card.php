<?php
/**
 * Stat Card - Premium Fintech Design
 * Cards modernes avec icônes, gradient pour le solde
 */

if (!isset($label) || !isset($value)) return;

$icon = $icon ?? 'activity';
$variation = $variation ?? 0;
$currency = $currency ?? 'TND';
$subtitle = $subtitle ?? '';
$hideCurrency = $hideCurrency ?? false;

// Couleurs selon le label
$styles = [
    'Solde' => [
        'is_gradient' => true,
        'bg' => 'linear-gradient(135deg, #4338CA 0%, #4F46E5 100%)',
        'text' => '#FFFFFF',
        'subtext' => 'rgba(255, 255, 255, 0.7)',
        'icon_bg' => 'rgba(255, 255, 255, 0.2)',
        'icon_color' => '#FFFFFF',
        'shadow' => '0 8px 24px rgba(67, 56, 202, 0.25)'
    ],
    'Revenus' => [
        'is_gradient' => false,
        'bg' => '#FFFFFF',
        'text' => '#111827',
        'subtext' => '#6B7280',
        'icon_bg' => '#D1FAE5',
        'icon_color' => '#10B981',
        'shadow' => '0 1px 3px rgba(17, 24, 39, 0.06)'
    ],
    'Dépenses' => [
        'is_gradient' => false,
        'bg' => '#FFFFFF',
        'text' => '#111827',
        'subtext' => '#6B7280',
        'icon_bg' => '#FEE2E2',
        'icon_color' => '#EF4444',
        'shadow' => '0 1px 3px rgba(17, 24, 39, 0.06)'
    ],
    'Budgets actifs' => [
        'is_gradient' => false,
        'bg' => '#FFFFFF',
        'text' => '#111827',
        'subtext' => '#6B7280',
        'icon_bg' => '#FEF3C7',
        'icon_color' => '#F59E0B',
        'shadow' => '0 1px 3px rgba(17, 24, 39, 0.06)'
    ]
];

$style = $styles[$label] ?? [
    'is_gradient' => false,
    'bg' => '#FFFFFF',
    'text' => '#111827',
    'subtext' => '#6B7280',
    'icon_bg' => '#EEF2FF',
    'icon_color' => '#4338CA',
    'shadow' => '0 1px 3px rgba(17, 24, 39, 0.06)'
];
?>

<div style="
    padding: 24px;
    <?php echo $style['is_gradient'] ? 'background: ' . $style['bg'] : 'background-color: ' . $style['bg']; ?>;
    border: 1px solid <?php echo $style['is_gradient'] ? 'transparent' : 'var(--color-border-light)'; ?>;
    border-radius: 20px;
    box-shadow: <?php echo $style['shadow']; ?>;
    transition: all 200ms ease;
    cursor: default;
" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='<?php echo $style['is_gradient'] ? '0 16px 36px rgba(67, 56, 202, 0.3)' : '0 8px 24px rgba(17, 24, 39, 0.08)'; ?>'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='<?php echo $style['shadow']; ?>'">

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
        <div style="
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background-color: <?php echo $style['icon_bg']; ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            color: <?php echo $style['icon_color']; ?>;
        ">
            <i data-lucide="<?php echo $icon; ?>" style="width: 22px; height: 22px;"></i>
        </div>
    </div>

    <div>
        <p style="
            font-size: 13px;
            color: <?php echo $style['subtext']; ?>;
            margin: 0 0 8px 0;
            font-weight: 500;
        ">
            <?php echo htmlspecialchars($label); ?>
        </p>
        <h3 style="
            font-size: 26px;
            font-weight: 800;
            color: <?php echo $style['text']; ?>;
            margin: 0;
            letter-spacing: -0.5px;
        ">
            <?php echo htmlspecialchars($value); ?>
            <?php if (!$hideCurrency): ?>
                <span style="font-size: 16px; font-weight: 600; opacity: 0.7;">TND</span>
            <?php endif; ?>
        </h3>
        <?php if ($subtitle): ?>
            <p style="
                font-size: 12px;
                color: <?php echo $style['subtext']; ?>;
                margin: 8px 0 0 0;
                font-weight: 500;
            ">
                <?php echo htmlspecialchars($subtitle); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
