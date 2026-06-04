<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/global.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F7F7FA 0%, #EEF2FF 100%);
            padding: 20px;
        }

        .reset-card {
            background: white;
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 24px 60px rgba(17, 24, 39, 0.08);
        }

        .reset-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .icon-circle {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4338CA 0%, #4F46E5 100%);
            border-radius: 18px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(67, 56, 202, 0.25);
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .subtitle {
            font-size: 14px;
            color: #6B7280;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #4338CA;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <div class="reset-header">
            <div class="icon-circle">🔑</div>
            <h1>Mot de passe oublié ?</h1>
            <p class="subtitle">Entrez votre email pour réinitialiser votre mot de passe</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">
                Réinitialiser mon mot de passe
            </button>
        </form>

        <a href="<?php echo BASE_URL; ?>/modules/auth/login.php" class="back-link">
            ← Retour à la connexion
        </a>
    </div>
</body>
</html>
