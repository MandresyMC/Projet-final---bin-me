<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/css/style.css" />

</head>
<body>
    <section id="page-login" style="background:var(--surface);">
        <nav class="nav-public">
            <a href="#" class="brand">Fit<span>Space</span></a>
        </nav>
        <div class="auth-wrapper">
            <div class="auth-card">
            <div class="auth-logo">Fit<span>Space</span></div>
            <div class="auth-subtitle">Bienvenue ! Connectez-vous à votre espace.</div>

            <!-- Flashdata erreur CI4 -->
            <?php if (session('error')) { ?>
                <div class="flash-message flash-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?=  session('error') ?>
                </div>
            <?php } ?>

            <form action="/page-login" method="post">
                <div class="form-group mb-3">
                <label class="form-label">Adresse email</label>
                <input type="email" name="email" class="form-control" placeholder="votre@email.com" value="<?= old('email') ?>"/>
                </div>
                <div class="form-group mb-4">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" />
                </div>
                <button type="submit" class="btn-primary-custom">Se connecter</button>
            </form>

            <hr class="auth-divider" />
            <div class="auth-footer">Pas encore de compte ? <a href="page-inscription">Créer un compte</a></div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>