<?php
/**
 * Attend, fournis par Admin\DashboardController::index() :
 * $nombreUtilisateurs, $volumeTotal, $nombreOperations, $gainsTotal, $tauxEchec,
 * $evolution (mois => nombre), $repartition (type_nom => nombre)
 */
$stats = [
    ['label' => 'Utilisateurs MVola', 'value' => number_format((float) $nombreUtilisateurs, 0, ',', ' ')],
    ['label' => 'Volume total transigé', 'value' => number_format((float) $volumeTotal, 0, ',', ' ') . ' Ar'],
    ['label' => 'Transactions totales', 'value' => number_format((float) $nombreOperations, 0, ',', ' ')],
    ['label' => 'Gains sur frais', 'value' => number_format((float) $gainsTotal, 0, ',', ' ') . ' Ar'],
    ['label' => "Taux d'échec", 'value' => str_replace('.', ',', (string) $tauxEchec) . ' %'],
];

$maxMonth = 1;
foreach ($evolution as $m) {
    $maxMonth = max($maxMonth, (int) $m['nombre']);
}

$couleurs = [
    'depot'     => '#4b7c39',
    'retrait'   => '#fed200',
    'transfert' => '#317041',
];

$totalRepartition = 0;
foreach ($repartition as $r) {
    $totalRepartition += (int) $r['nombre'];
}

$gradientStops = [];
$cursor = 0;
foreach ($repartition as $r) {
    $part = $totalRepartition > 0 ? round(($r['nombre'] / $totalRepartition) * 100) : 0;
    $start = $cursor;
    $cursor += $part;
    $couleur = $couleurs[$r['type_nom']] ?? '#3f3f44';
    $gradientStops[] = $couleur . ' ' . $start . '% ' . $cursor . '%';
}
$conicGradient = $gradientStops ? 'conic-gradient(' . implode(', ', $gradientStops) . ')' : '#e6e6e0';

$operateurCouleurs = ['#ff5c3f', '#317041', '#fed200', '#4b7c39', '#a72525', '#3f3f44'];

$maxCommission = 1;
foreach ($commissionParOperateur as $c) {
    $maxCommission = max($maxCommission, (float) $c['montant_commission']);
}

$totalVolumeOperateurs = 0;
foreach ($repartitionParOperateur as $r) {
    $totalVolumeOperateurs += (float) $r['volume'];
}

$gradientStopsOperateurs = [];
$cursorOp = 0;
foreach ($repartitionParOperateur as $i => $r) {
    $part = $totalVolumeOperateurs > 0 ? round(((float) $r['volume'] / $totalVolumeOperateurs) * 100) : 0;
    $start = $cursorOp;
    $cursorOp += $part;
    $couleur = $operateurCouleurs[$i % count($operateurCouleurs)];
    $gradientStopsOperateurs[] = $couleur . ' ' . $start . '% ' . $cursorOp . '%';
}
$conicGradientOperateurs = $gradientStopsOperateurs ? 'conic-gradient(' . implode(', ', $gradientStopsOperateurs) . ')' : '#e6e6e0';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVola Admin — Dashboard</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-common.css') ?>">
    <link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/admin-dashboard.css') ?>">
</head>
<body>

    <?= view('partials/navbar_admin', ['active' => 'dashboard']) ?>

    <main class="admin-page">
        <section class="admin-section">
            <h1 class="admin-section__title">EVOLUTIONS MVOLA</h1>
            <p class="admin-section__desc">
                Vue d'ensemble de l'activité de la plateforme&nbsp;: croissance des utilisateurs,
                volumes transigés et répartition des opérations.
            </p>

            <div class="stat-grid">
                <?php foreach ($stats as $i => $stat) : ?>
                    <div class="stat-card" style="animation-delay: <?= $i * 0.08 ?>s">
                        <span class="stat-card__label"><?= esc($stat['label']) ?></span>
                        <span class="stat-card__value"><?= esc($stat['value']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="admin-section">
            <div class="chart-grid">
                <div class="chart-card">
                    <h2 class="chart-card__title">Évolution des transactions (par mois)</h2>
                    <?php if (empty($evolution)) : ?>
                        <p class="admin-empty">Aucune transaction enregistrée pour le moment.</p>
                    <?php else : ?>
                        <div class="bar-chart">
                            <?php foreach ($evolution as $m) : ?>
                                <div class="bar-chart__col">
                                    <div class="bar-chart__bar" data-bar-target="<?= round(((int) $m['nombre'] / $maxMonth) * 100) ?>">
                                        <span class="bar-chart__value"><?= esc($m['nombre']) ?></span>
                                    </div>
                                    <span class="bar-chart__label"><?= esc(date('M Y', strtotime($m['mois'] . '-01'))) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="chart-card">
                    <h2 class="chart-card__title">Répartition par opération</h2>
                    <?php if (empty($repartition)) : ?>
                        <p class="admin-empty">Aucune donnée à afficher.</p>
                    <?php else : ?>
                        <div class="donut-wrap">
                            <div class="donut" style="background: <?= $conicGradient ?>;"></div>
                            <ul class="donut-legend">
                                <?php foreach ($repartition as $r) :
                                    $part = $totalRepartition > 0 ? round(($r['nombre'] / $totalRepartition) * 100) : 0;
                                ?>
                                    <li>
                                        <span class="donut-legend__swatch" style="background-color: <?= esc($couleurs[$r['type_nom']] ?? '#3f3f44') ?>;"></span>
                                        <?= esc(ucfirst($r['type_nom'])) ?> — <strong><?= esc($part) ?>%</strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="admin-section">
            <h1 class="admin-section__title">AUTRES OPERATEURS</h1>
            <p class="admin-section__desc">
                Montant de commission perçu sur les transferts vers les opérateurs non propriétaires,
                et répartition du volume transigé vers chacun d'eux.
            </p>

            <div class="chart-grid">
                <div class="chart-card">
                    <h2 class="chart-card__title">Montant pris par les autres opérateurs (commission)</h2>
                    <?php if (empty($commissionParOperateur)) : ?>
                        <p class="admin-empty">Aucune commission perçue pour le moment.</p>
                    <?php else : ?>
                        <div class="bar-chart">
                            <?php foreach ($commissionParOperateur as $c) : ?>
                                <div class="bar-chart__col">
                                    <div class="bar-chart__bar" data-bar-target="<?= round(((float) $c['montant_commission'] / $maxCommission) * 100) ?>">
                                        <span class="bar-chart__value"><?= number_format((float) $c['montant_commission'], 0, ',', ' ') ?> Ar</span>
                                    </div>
                                    <span class="bar-chart__label"><?= esc($c['operateur_nom']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="chart-card">
                    <h2 class="chart-card__title">Répartition du volume par opérateur</h2>
                    <?php if (empty($repartitionParOperateur)) : ?>
                        <p class="admin-empty">Aucun transfert externe enregistré pour le moment.</p>
                    <?php else : ?>
                        <div class="donut-wrap">
                            <div class="donut" style="background: <?= $conicGradientOperateurs ?>;"></div>
                            <ul class="donut-legend">
                                <?php foreach ($repartitionParOperateur as $i => $r) :
                                    $part = $totalVolumeOperateurs > 0 ? round(((float) $r['volume'] / $totalVolumeOperateurs) * 100) : 0;
                                ?>
                                    <li>
                                        <span class="donut-legend__swatch" style="background-color: <?= esc($operateurCouleurs[$i % count($operateurCouleurs)]) ?>;"></span>
                                        <?= esc($r['operateur_nom']) ?> — <strong><?= esc($part) ?>%</strong>
                                        (<?= number_format((float) $r['volume'], 0, ',', ' ') ?> Ar)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <script src="<?= base_url('Mvoladashboard/js/admin-dashboard.js') ?>"></script>
</body>
</html>
