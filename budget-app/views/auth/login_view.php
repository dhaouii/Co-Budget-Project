<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo APP_NAME; ?></title>
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
            --color-accent: #3B82F6;
            --color-accent-dark: #1E40AF;
            --color-danger: #EF4444;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text-primary);
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            padding: 40px 32px;
        }

        .login-header {
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
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--color-text-primary);
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }

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

        .login-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .login-footer a {
            color: var(--color-accent);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            font-size: 12px;
            color: var(--color-text-secondary);
            text-decoration: none;
        }

        .forgot-password a:hover {
            color: var(--color-accent);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="app-title"><?php echo APP_NAME; ?></h1>
                <p class="app-subtitle">Gestion collaborative de budget</p>
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

            <form method="POST" action="<?php echo BASE_URL; ?>/modules/auth/login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="forgot-password">
                    <a href="<?php echo BASE_URL; ?>/modules/auth/password_reset.php">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>

            <div class="login-footer">
                Pas encore de compte ? <a href="<?php echo BASE_URL; ?>/modules/auth/register.php">S'inscrire</a>
            </div>

            <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--color-border);">
                <p style="font-size: 13px; color: var(--color-text-secondary); margin-bottom: 12px;">
                    Vous voulez juste voir comment ça marche ?
                </p>
                <a href="<?php echo BASE_URL; ?>/modules/auth/guest_login.php" style="
                    display: inline-block;
                    padding: 10px 20px;
                    background: rgba(45, 212, 191, 0.1);
                    color: var(--color-accent-dark);
                    border: 1px solid var(--color-accent);
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 600;
                    text-decoration: none;
                    transition: all 0.2s;
                " onmouseover="this.style.background='rgba(45, 212, 191, 0.2)'" onmouseout="this.style.background='rgba(45, 212, 191, 0.1)'">
                    Continuer en tant que visiteur
                </a>
            </div>
        </div>
    </div>
</body>
</html>
