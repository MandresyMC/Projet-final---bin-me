<?php
/**
 * Attend $operations, fourni par Admin\HistoriqueController::index().
 * Chaque ligne contient : id, type_nom, source_numero, destination_numero, operateur_nom,
 * montant, frais, frais_base, pourcentage_commission, montant_commission, statut, date_creation.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVola Admin — Historique</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-common.css') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-historique.css') ?>">
</head>
<body>

    <?= view('partials/navbar_admin', ['active' => 'historique']) ?>

    <main class="admin-page">
        <section class="admin-section">
            <h1 class="admin-section__title">HISTORIQUE DES TRANSACTIONS</h1>
            <p class="admin-section__desc">
                Vue complète de toutes les transactions MVola, avec le détail de la répartition des frais&nbsp;:
                part fixe (barème) et part commission pour les transferts vers les autres opérateurs.
            </p>

            <div class="admin-table-wrap">
                <table class="admin-table admin-table--historique">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Opérateur dest.</th>
                            <th>Montant</th>
                            <th>Frais base</th>
                            <th>Commission</th>
                            <th>Frais total</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($operations as $op) :
                            $idAffiche = strtoupper(substr($op['type_nom'], 0, 3)) . '-' . str_pad((string) $op['id'], 4, '0', STR_PAD_LEFT);
                            $aCommission = (float) $op['pourcentage_commission'] > 0;
                        ?>
                            <tr>
                                <td><?= esc($idAffiche) ?></td>
                                <td><?= esc(ucfirst($op['type_nom'])) ?></td>
                                <td><?= esc($op['source_numero'] ?? '-') ?></td>
                                <td><?= esc($op['destination_numero'] ?? '-') ?></td>
                                <td><?= esc($op['operateur_nom'] ?? '—') ?></td>
                                <td><?= number_format((float) $op['montant'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format((float) $op['frais_base'], 0, ',', ' ') ?> Ar</td>
                                <td>
                                    <?php if ($aCommission) : ?>
                                        <span class="admin-badge admin-badge--off">
                                            <?= number_format((float) $op['pourcentage_commission'], 2, ',', ' ') ?>%
                                            (<?= number_format((float) $op['montant_commission'], 0, ',', ' ') ?> Ar)
                                        </span>
                                    <?php else : ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= number_format((float) $op['frais'], 0, ',', ' ') ?> Ar</strong></td>
                                <td>
                                    <span class="admin-badge <?= strtolower($op['statut']) === 'valide' ? 'admin-badge--ok' : 'admin-badge--off' ?>">
                                        <?= esc($op['statut']) ?>
                                    </span>
                                </td>
                                <td><?= esc(date('d/m/Y H:i', strtotime($op['date_creation']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="admin-empty" <?= count($operations) > 0 ? 'hidden' : '' ?>>Aucune transaction enregistrée pour le moment.</p>
            </div>
        </section>
    </main>

</body>
</html>
