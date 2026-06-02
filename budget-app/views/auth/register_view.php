<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo APP_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-bg: #FAFAFA;
            --color-surface: #FFFFFF;
            --color-border: #E5E7EB;
            --color-text-primary: #1C1C1E;
            --color-text-secondary: #6B7280;
            --color-accent: #2DD4BF;
            --color-accent-dark: #0F9B8E;
            --color-danger: #EF4444;
            --color-success: #10B981;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text-primary);
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            width: 100%;
            max-width: 450px;
            padding: 40px 32px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .app-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--color-accent), var(--color-accent-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .app-subtitle {
            font-size: 14px;
            color: var(--color-text-secondary);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--color-text-primary);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--color-accent);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--color-accent-dark);
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #FCA5A5;
            color: #7F1D1D;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #86EFAC;
            color: #065F46;
        }

        .password-hint {
            font-size: 12px;
            color: var(--color-text-secondary);
            margin-top: 4px;
        }

        .register-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .register-footer a {
            color: var(--color-accent);
            text-decoration: none;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1 class="app-title"><?php echo APP_NAME; ?></h1>
                <p class="app-subtitle">Créer un compte</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo BASE_URL; ?>/modules/auth/register.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                    <p class="password-hint">Minimum 8 caractères</p>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>

            <div class="register-footer">
                Vous avez déjà un compte ? <a href="<?php echo BASE_URL; ?>/modules/auth/login.php">Se connecter</a>
            </div>
        </div>
    </div>
</body>
</html>
