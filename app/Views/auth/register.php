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

    <section id="page-inscription" style="background:var(--surface);">
        <nav class="nav-public">
            <a href="#" class="brand">Fit<span>Space</span></a>
        </nav>
        <div class="auth-wrapper">
            <div class="auth-card">
            <div class="auth-logo">Fit<span>Space</span></div>
            <div class="auth-subtitle">Créez votre compte client gratuitement.</div>

            <?php if (session('error')) { ?>
                <div class="flash-message flash-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?=  session('error') ?>
                </div>
            <?php } ?>
            <form action="/page-inscription" method="post">
                <div class="form-grid-2 mb-3">
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="Jean" value="<?= old('prenom') ?>" />
                </div>
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="Dupont" value="<?= old('nom') ?>" />
                </div>
                <?php if (session('errors.nom')) { ?>
                    <small style="color:var(--accent);font-size:0.78rem;margin-top:3px;width:100%;"><?= (session('errors.nom')) ?></small>
                <?php } ?>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" class="form-control" placeholder="jean.dupont@email.com" value="<?= old('email') ?>" />
                    <!-- Erreur de validation CI4 -->
                    <?php if (session('errors.email')) { ?>
                        <small style="color:var(--accent);font-size:0.78rem;margin-top:3px;"><?= (session('errors.email')) ?></small>
                    <?php } ?>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="8 caractères minimum" value="<?= old('password') ?>" />
                    <?php if (session('errors.password')) { ?>
                        <small style="color:var(--accent);font-size:0.78rem;margin-top:3px;"><?= (session('errors.password')) ?></small>
                    <?php } ?>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Retapez votre mot de passe" />
                    <?php if (session('errors.password_confirm')) { ?>
                        <small style="color:var(--accent);font-size:0.78rem;margin-top:3px;"><?= (session('errors.password_confirm')) ?></small>
                    <?php } ?>
                </div>
                <button type="submit" class="btn-primary-custom">Créer mon compte</button>
            </form>

            <hr class="auth-divider" />
            <div class="auth-footer">Déjà inscrit ? <a href="/page-login">Se connecter</a></div>
            </div>
        </div>
        </section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>