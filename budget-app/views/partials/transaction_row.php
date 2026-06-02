<?php
/**
 * Composant ligne de transaction réutilisable
 * Affiche : type, montant, catégorie, date, actions
 * Paramètres : $transaction (array), $showActions (boolean)
 */

if (!isset($transaction) || empty($transaction)) return;

require_once __DIR__ . '/../../helpers/format_helper.php';

$showActions = $showActions ?? TRUE;
$transactionType = $transaction['type'];
$isIncome = $transactionType === 'revenu';
$amount = $transaction['montant'];
$formattedAmount = formatMoney($amount);
$amountColor = $isIncome ? '#10B981' : '#EF4444';
$amountSign = $isIncome ? '+' : '-';
$categoryName = $transaction['categorie'] ?? 'Sans catégorie';
$transactionDate = formatDate($transaction['date_transaction']);
$description = $transaction['description'] ?? '';
?>

<tr style="border-bottom: 1px solid var(--color-border);">
    <td style="padding: var(--spacing-base);">
        <div style="
            display: flex;
            align-items: center;
            gap: var(--spacing-base);
        ">
            <div style="
                width: 40px;
                height: 40px;
                border-radius: 8px;
                background-color: rgba(<?php echo $isIncome ? '16, 185, 129' : '239, 68, 68'; ?>, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: <?php echo $amountColor; ?>;
            ">
                <i data-lucide="<?php echo $isIncome ? 'arrow-down-left' : 'arrow-up-right'; ?>" style="width: 20px; height: 20px;"></i>
            </div>

            <div style="flex: 1;">
                <?php if ($description): ?>
                    <p style="
                        font-weight: 500;
                        margin: 0;
                        color: var(--color-text-primary);
                    ">
                        <?php echo htmlspecialchars($description); ?>
                    </p>
                <?php endif; ?>
                <p style="
                    font-size: 12px;
                    color: var(--color-text-secondary);
                    margin: 4px 0 0 0;
                ">
                    <?php echo htmlspecialchars($categoryName); ?>
                </p>
            </div>
        </div>
    </td>

    <td style="
        padding: var(--spacing-base);
        text-align: right;
        font-weight: 600;
        color: <?php echo $amountColor; ?>;
    ">
        <?php echo $amountSign; ?><?php echo htmlspecialchars($formattedAmount); ?>
    </td>

    <td style="
        padding: var(--spacing-base);
        font-size: 13px;
        color: var(--color-text-secondary);
    ">
        <?php echo $transactionDate; ?>
    </td>

    <?php if ($showActions && !isGuest()): ?>
        <td style="padding: var(--spacing-base); text-align: right;">
            <div style="display: flex; gap: var(--spacing-sm); justify-content: flex-end;">
                <a href="<?php echo BASE_URL; ?>/modules/transactions/edit.php?id=<?php echo $transaction['id']; ?>" class="btn btn-secondary btn-sm" title="Modifier" style="display: inline-flex; align-items: center; gap: 6px;">
                    ✏️ Modifier
                </a>
                <a href="<?php echo BASE_URL; ?>/modules/transactions/delete.php?id=<?php echo $transaction['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDeleteAndNavigate(this, 'Êtes-vous sûr de vouloir supprimer cette transaction ?');" title="Supprimer" style="display: inline-flex; align-items: center; gap: 6px;">
                    🗑️ Supprimer
                </a>
            </div>
        </td>
    <?php endif; ?>
</tr>
