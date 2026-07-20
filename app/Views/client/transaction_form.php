<?php
/**
 * Shared view for the 3 transaction screens (depot / retrait / transfert).
 * Expects: $type ('depot'|'retrait'|'transfert'), $title, $description, $solde
 */
$isTransfert = $type === 'transfert';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVola — <?= esc($title) ?></title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/transaction-form.css') ?>">
</head>
<body>

    <?= $this->include('partials/navbar') ?>

    <main class="mvola-txform">
        <div class="mvola-txform__text">
            <h1 class="mvola-txform__title"><?= esc($title) ?></h1>
            <p class="mvola-txform__desc"><?= esc($description) ?></p>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="hero-alert hero-alert--error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form class="txf-form" data-txf-form action="<?= base_url('client/operation') ?>" method="post" novalidate>
                <input type="hidden" name="type_operation" value="<?= esc($type) ?>">

                <label class="txf-label" for="txf-montant">Entrez le montant en Ariary&nbsp;:</label>
                <input
                    class="txf-input"
                    type="number"
                    id="txf-montant"
                    name="montant"
                    min="1"
                    step="1"
                    placeholder="20000"
                    value="<?= esc(old('montant')) ?>"
                    required
                    data-txf-montant
                >

                <p class="txf-solde">Solde actuel&nbsp;: <span data-txf-solde><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</span></p>

                <?php if ($isTransfert) : ?>
                    <div id="destinations-container">
                        <div class="txf-destination-item">
                            <div class="txf-phone">
                                <span class="txf-phone__prefix">+261</span>
                                <input
                                    class="txf-phone__input"
                                    type="tel"
                                    inputmode="numeric"
                                    id="txf-destination"
                                    name="numero_user_destination[]"
                                    placeholder="38 63 456 98"
                                    maxlength="12"
                                    required
                                    data-txf-destination
                                >
                            </div>
                        </div>
                    </div>

                    <button type="button" id="btn-add-destination">
                        + Ajouter un numéro
                    </button>
                <?php endif; ?>

                <button type="submit" class="txf-btn txf-btn--valider" data-txf-submit>
                    <span class="spinner"></span>
                    <span>VALIDER MA TRANSACTION</span>
                </button>

                <a href="<?= base_url('client/operation') ?>" class="txf-btn txf-btn--retour">RETOUR AU MENU</a>
            </form>
        </div>

        <div class="mvola-txform__media">
            <div class="mvola-txform__blob" aria-hidden="true"></div>
            <img class="mvola-txform__photo" src="<?= base_url('Mvoladashboard/SVG/Picture.png') ?>" alt="Utilisateurs MVola">
        </div>
    </main>

    <script src="<?= base_url('Mvoladashboard/js/transaction-form.js') ?>"></script>
</body>
</html>
