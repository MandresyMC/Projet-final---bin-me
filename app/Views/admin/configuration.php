<?php
/**
 * Attend, fournis par Admin\BaremeFraisController::index() :
 * $types (liste des types d'operation), $baremes (regles de frais existantes),
 * $operateurs (tous les operateurs avec leur proprietaire), $operateursAutres (operateurs non proprietaires),
 * $prefixes (prefixes configures avec operateur + proprietaire), $commissions (commissions configurees)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVola Admin — Configuration des règles</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-common.css') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-configuration.css') ?>">
</head>
<body>

    <?= view('partials/navbar_admin', ['active' => 'configuration']) ?>

    <main class="admin-page">

        <nav class="admin-quicknav" aria-label="Navigation rapide">
            <a class="admin-quicknav__link" data-quicknav-link href="#prefixes">Prefixes</a>
            <a class="admin-quicknav__link" data-quicknav-link href="#taxes">Taxes et frais</a>
            <a class="admin-quicknav__link" data-quicknav-link href="#commissions">Commissions</a>
        </nav>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="taxes-form__note is-info" style="margin-bottom: 24px;"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="taxes-form__note is-error" style="margin-bottom: 24px;"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <!-- ===== PREFIXES (relie aux tables prefixe / operateur / proprietaire) ===== -->
        <section class="admin-section" id="prefixes" data-quicknav-target>
            <h1 class="admin-section__title">PREFIXES VALABLES</h1>
            <p class="admin-section__desc">
                Configurez les préfixes téléphoniques et associez-les à un opérateur (Yas, Orange, Airtel, ...).
                Seuls les préfixes de l'opérateur propriétaire (Yas) peuvent créer un compte MVola ;
                les autres opérateurs peuvent tout de même être destinataires d'un transfert.
            </p>

            <form class="taxes-form prefix-form" action="<?= base_url('admin/prefixe') ?>" method="post" data-prefix-form novalidate>
                <div class="taxes-form__field">
                    <label class="admin-label" for="prefix-input">Préfixe</label>
                    <input
                        class="taxes-form__input prefix-form__input"
                        id="prefix-input"
                        type="text"
                        inputmode="numeric"
                        maxlength="3"
                        placeholder="038"
                        name="prefixe"
                        data-prefix-input
                        required
                    >
                </div>

                <div class="taxes-form__field">
                    <label class="admin-label" for="prefix-operateur">Opérateur</label>
                    <select class="taxes-form__input prefix-form__select" id="prefix-operateur" name="id_operateur" data-prefix-operateur required>
                        <option value="">Choisir...</option>
                        <?php foreach ($operateurs as $o) : ?>
                            <option value="<?= esc($o['id']) ?>" data-proprietaire="<?= esc(strtolower($o['proprietaire_nom'])) ?>">
                                <?= esc($o['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="taxes-form__field prefix-form__preview-field">
                    <span class="admin-label">Type</span>
                    <span class="pill-badge prefix-form__preview" data-prefix-badge hidden></span>
                </div>

                <button type="submit" class="admin-btn admin-btn--green taxes-form__submit">Ajouter</button>
            </form>

            <div class="admin-section__block">
                <span class="admin-label">Vos préfixes en vigueur</span>
                <div class="pill-list" data-prefix-list>
                        <?php foreach ($prefixes as $p) : ?>
                            <div class="pill <?= $p['actif'] ? '' : 'is-off' ?>">
                                <span class="pill__value">
                                    <?= esc($p['prefixe']) ?> — <?= esc($p['operateur_nom']) ?>
                                    <span class="pill-badge pill-badge--<?= strtolower($p['proprietaire_nom']) === 'local' ? 'local' : 'autre' ?>">
                                        <?= strtolower($p['proprietaire_nom']) === 'local' ? 'LOCAL' : 'AUTRE' ?>
                                    </span>
                                </span>
                                <form action="<?= base_url('admin/prefixe/' . $p['id'] . '/toggle') ?>" method="post" class="pill__inline-form">
                                    <button type="submit" class="pill__toggle" data-state="<?= $p['actif'] ? 'on' : 'off' ?>">
                                        <?= $p['actif'] ? 'DESACTIVER' : 'ACTIVER' ?>
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/prefixe/' . $p['id'] . '/delete') ?>" method="post" class="pill__inline-form">
                                    <button type="submit" class="pill__delete">SUPPRIMER</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($prefixes)) : ?>
                            <p class="admin-empty">Aucun préfixe configuré pour le moment.</p>
                        <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- ===== TAXES ET FRAIS (relie a la table bareme_frais) ===== -->
        <section class="admin-section" id="taxes" data-quicknav-target>
            <h1 class="admin-section__title">TAXES ET FRAIS</h1>
            <p class="admin-section__desc">
                Définissez les frais appliqués selon le type d'opération et la tranche de montant.
                Exemple&nbsp;: pour un transfert entre 10&nbsp;000 et 20&nbsp;000&nbsp;Ar, appliquez 300&nbsp;Ar de frais.
                Le dépôt reste toujours gratuit.
            </p>

            <form class="taxes-form" action="<?= base_url('admin/bareme-frais') ?>" method="post" data-taxes-form novalidate>
                <div class="taxes-form__field">
                    <label class="admin-label" for="taxes-type">Type d'opération</label>
                    <select class="taxes-form__input" id="taxes-type" name="id_type" data-taxes-type>
                        <?php foreach ($types as $t) : ?>
                            <option
                                value="<?= esc($t['id']) ?>"
                                data-nom="<?= esc($t['nom']) ?>"
                                <?= $t['nom'] === 'transfert' ? 'selected' : '' ?>
                            >
                                <?= esc(ucfirst($t['nom'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="taxes-form__field">
                    <label class="admin-label" for="taxes-min">Montant min (Ar)</label>
                    <input class="taxes-form__input" type="number" id="taxes-min" name="montant_min" min="0" step="100" placeholder="10000" data-taxes-min required>
                </div>

                <div class="taxes-form__field">
                    <label class="admin-label" for="taxes-max">Montant max (Ar)</label>
                    <input class="taxes-form__input" type="number" id="taxes-max" name="montant_max" min="0" step="100" placeholder="20000" data-taxes-max required>
                </div>

                <div class="taxes-form__field">
                    <label class="admin-label" for="taxes-frais">Frais (Ar)</label>
                    <input class="taxes-form__input" type="number" id="taxes-frais" name="frais" min="0" step="10" placeholder="300" data-taxes-frais required>
                </div>

                <button type="submit" class="admin-btn admin-btn--green taxes-form__submit" data-taxes-submit>Ajouter</button>
            </form>
            <p class="taxes-form__note" data-taxes-note hidden></p>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Montant min</th>
                            <th>Montant max</th>
                            <th>Frais</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody data-taxes-body>
                        <?php foreach ($baremes as $b) : ?>
                            <tr data-taxes-row>
                                <td><?= esc(ucfirst($b['type_nom'])) ?></td>
                                <td><?= number_format((float) $b['montant_min'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format((float) $b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format((float) $b['frais'], 0, ',', ' ') ?> Ar</td>
                                <td>
                                    <form action="<?= base_url('admin/bareme-frais/' . $b['id'] . '/delete') ?>" method="post">
                                        <button type="submit" class="admin-btn admin-btn--orange admin-btn--small">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="admin-empty" data-taxes-empty <?= count($baremes) > 0 ? 'hidden' : '' ?>>Aucun frais configuré pour le moment.</p>
            </div>
        </section>

        <!-- ===== COMMISSIONS (transferts vers les autres operateurs) ===== -->
        <section class="admin-section" id="commissions" data-quicknav-target>
            <h1 class="admin-section__title">COMMISSIONS AUTRES OPERATEURS</h1>
            <p class="admin-section__desc">
                En plus des frais habituels, définissez un pourcentage de commission appliqué au montant
                lorsqu'un client transfère de l'argent vers un numéro d'un opérateur non propriétaire (ex&nbsp;: Orange, Airtel).
            </p>

            <form class="taxes-form" action="<?= base_url('admin/commission') ?>" method="post" novalidate>
                <div class="taxes-form__field">
                    <label class="admin-label" for="commission-operateur">Opérateur</label>
                    <select class="taxes-form__input" id="commission-operateur" name="id_operateur" required>
                        <?php foreach ($operateursAutres as $o) : ?>
                            <option value="<?= esc($o['id']) ?>"><?= esc($o['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="taxes-form__field">
                    <label class="admin-label" for="commission-pourcentage">Pourcentage (%)</label>
                    <input class="taxes-form__input" type="number" id="commission-pourcentage" name="pourcentage" min="0" max="100" step="0.1" placeholder="2.5" required>
                </div>

                <button type="submit" class="admin-btn admin-btn--green taxes-form__submit">Ajouter</button>
            </form>
            <?php if (empty($operateursAutres)) : ?>
                <p class="taxes-form__note is-error">Aucun opérateur non propriétaire n'est configuré pour le moment.</p>
            <?php endif; ?>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Opérateur</th>
                            <th>Commission</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commissions as $c) : ?>
                            <tr>
                                <td><?= esc($c['operateur_nom']) ?></td>
                                <td><?= number_format((float) $c['pourcentage'], 2, ',', ' ') ?> %</td>
                                <td><?= esc($c['date_creation']) ?></td>
                                <td>
                                    <form action="<?= base_url('admin/commission/' . $c['id'] . '/delete') ?>" method="post">
                                        <button type="submit" class="admin-btn admin-btn--orange admin-btn--small">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="admin-empty" <?= count($commissions) > 0 ? 'hidden' : '' ?>>Aucune commission configurée pour le moment.</p>
            </div>
        </section>

    </main>

    <script src="<?= base_url('Mvoladashboard/js/admin-common.js') ?>"></script>
    <script src="<?= base_url('Mvoladashboard/js/admin-configuration.js') ?>"></script>
</body>
</html>
