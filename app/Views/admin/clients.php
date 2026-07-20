<?php
// Front-only mock rows to demonstrate the working filters.
$clients = [
    ['numero' => '038 83 350 87', 'inscription' => '2025-11-02', 'solde' => 452000, 'statut' => 'actif'],
    ['numero' => '033 12 345 67', 'inscription' => '2025-12-14', 'solde' => 128500, 'statut' => 'actif'],
    ['numero' => '032 45 678 90', 'inscription' => '2026-01-05', 'solde' => 980000, 'statut' => 'actif'],
    ['numero' => '034 22 111 09', 'inscription' => '2026-02-19', 'solde' => 15200,  'statut' => 'bloque'],
    ['numero' => '037 55 044 21', 'inscription' => '2026-03-08', 'solde' => 63000,  'statut' => 'actif'],
    ['numero' => '038 63 456 98', 'inscription' => '2026-04-22', 'solde' => 210300, 'statut' => 'actif'],
    ['numero' => '033 98 011 44', 'inscription' => '2026-05-30', 'solde' => 4200,   'statut' => 'bloque'],
    ['numero' => '032 71 200 15', 'inscription' => '2026-06-11', 'solde' => 349900, 'statut' => 'actif'],
    ['numero' => '034 60 733 82', 'inscription' => '2026-07-01', 'solde' => 0,      'statut' => 'bloque'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVola Admin — Clients</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-common.css') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-clients.css') ?>">
</head>
<body>

    <?= view('partials/navbar_admin', ['active' => 'clients']) ?>

    <main class="admin-page">
        <section class="admin-section">
            <h1 class="admin-section__title">MES CLIENTS MVOLA</h1>
            <p class="admin-section__desc">
                Consultez et filtrez la liste de vos clients&nbsp;: statut du compte, solde et date d'inscription.
            </p>

            <div class="clients-toolbar">
                <div class="clients-search">
                    <input type="text" class="clients-search__input" placeholder="Rechercher un numero" data-clients-search>
                    <button type="button" class="clients-search__btn" aria-label="Rechercher">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="7" stroke="#ffffff" stroke-width="2.4"/>
                            <path d="M21 21L16.65 16.65" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <button type="button" class="admin-btn admin-btn--green" data-clients-solde-filter>
                    <span data-clients-solde-label>Trier par solde</span>
                </button>

                <button type="button" class="admin-btn admin-btn--yellow" data-clients-status-filter data-status="tous">
                    <span data-clients-status-label>Filtrer par statut</span>
                </button>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Date d'inscription</th>
                            <th>Solde</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody data-clients-body>
                        <?php foreach ($clients as $c) : ?>
                            <tr
                                data-clients-row
                                data-search="<?= esc(strtolower($c['numero'])) ?>"
                                data-solde="<?= esc($c['solde']) ?>"
                                data-statut="<?= esc($c['statut']) ?>"
                            >
                                <td class="admin-table__strong"><?= esc($c['numero']) ?></td>
                                <td><?= esc(date('d/m/Y', strtotime($c['inscription']))) ?></td>
                                <td><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
                                <td>
                                    <span class="admin-badge <?= $c['statut'] === 'actif' ? 'admin-badge--ok' : 'admin-badge--off' ?>">
                                        <?= $c['statut'] === 'actif' ? 'ACTIF' : 'BLOQUE' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="admin-empty" data-clients-empty hidden>Aucun client ne correspond à votre recherche.</p>
            </div>
        </section>
    </main>

    <script src="<?= base_url('Mvoladashboard/js/admin-clients.js') ?>"></script>
</body>
</html>
