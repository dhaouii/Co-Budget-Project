<?php
/**
 * Pied de page commun
 * Ferme les conteneurs ouverts dans header.php
 */
?>
        </main>
    </div>

    <!-- Footer -->
    <footer style="
        background-color: var(--color-surface);
        border-top: 1px solid var(--color-border);
        padding: var(--spacing-lg);
        text-align: center;
        font-size: 12px;
        color: var(--color-text-secondary);
        margin-top: auto;
    ">
        <div class="container">
            <p style="margin: 0;">
                &copy; 2025 <?php echo APP_NAME; ?>. Tous droits réservés.
            </p>
        </div>
    </footer>

    <!-- JS Global -->
    <script src="<?php echo BASE_URL; ?>/assets/js/global.js"></script>

    <!-- JS Spécifiques (à charger après) -->
    <?php if (isset($customJS)): ?>
        <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo $customJS; ?>"></script>
    <?php endif; ?>
</body>
</html>
