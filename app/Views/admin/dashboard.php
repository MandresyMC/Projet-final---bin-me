<?php
// Front-only mock data to illustrate the dashboard layout.
$stats = [
    ['label' => 'Utilisateurs actifs', 'value' => 12480, 'suffix' => '', 'trend' => '+8,4%', 'up' => true],
    ['label' => 'Volume total transigé', 'value' => 284500000, 'suffix' => ' Ar', 'trend' => '+14,1%', 'up' => true],
    ['label' => 'Transactions ce mois', 'value' => 9312, 'suffix' => '', 'trend' => '+3,2%', 'up' => true],
    ['label' => "Taux d'échec", 'value' => 1.8, 'suffix' => ' %', 'trend' => '-0,4%', 'up' => false],
];

$months = [
    ['label' => 'Fev', 'value' => 58],
    ['label' => 'Mar', 'value' => 64],
    ['label' => 'Avr', 'value' => 70],
    ['label' => 'Mai', 'value' => 66],
    ['label' => 'Juin', 'value' => 84],
    ['label' => 'Juil', 'value' => 93],
];
$maxMonth = max(array_column($months, 'value'));

$breakdown = [
    ['label' => 'Transfert', 'value' => 48, 'color' => '#317041'],
    ['label' => 'Retrait', 'value' => 33, 'color' => '#fed200'],
    ['label' => 'Depot', 'value' => 19, 'color' => '#4b7c39'],
];
$gradientStops = [];
$cursor = 0;
foreach ($breakdown as $slice) {
    $start = $cursor;
    $cursor += $slice['value'];
    $gradientStops[] = $slice['color'] . ' ' . $start . '% ' . $cursor . '%';
}
$conicGradient = 'conic-gradient(' . implode(', ', $gradientStops) . ')';
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
                        <span class="stat-card__value" data-count-target="<?= esc($stat['value']) ?>" data-count-suffix="<?= esc($stat['suffix']) ?>">0<?= esc($stat['suffix']) ?></span>
                        <span class="stat-card__trend <?= $stat['up'] ? 'is-up' : 'is-down' ?>">
                            <?= $stat['up'] ? '&#9650;' : '&#9660;' ?> <?= esc($stat['trend']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="admin-section">
            <div class="chart-grid">
                <div class="chart-card">
                    <h2 class="chart-card__title">Évolution des transactions (en milliers)</h2>
                    <div class="bar-chart">
                        <?php foreach ($months as $m) : ?>
                            <div class="bar-chart__col">
                                <div class="bar-chart__bar" data-bar-target="<?= round(($m['value'] / $maxMonth) * 100) ?>">
                                    <span class="bar-chart__value"><?= esc($m['value']) ?>k</span>
                                </div>
                                <span class="bar-chart__label"><?= esc($m['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="chart-card">
                    <h2 class="chart-card__title">Répartition par opération</h2>
                    <div class="donut-wrap">
                        <div class="donut" style="background: <?= $conicGradient ?>;"></div>
                        <ul class="donut-legend">
                            <?php foreach ($breakdown as $slice) : ?>
                                <li>
                                    <span class="donut-legend__swatch" style="background-color: <?= esc($slice['color']) ?>;"></span>
                                    <?= esc($slice['label']) ?> — <strong><?= esc($slice['value']) ?>%</strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="<?= base_url('Mvoladashboard/js/admin-dashboard.js') ?>"></script>
</body>
</html>
