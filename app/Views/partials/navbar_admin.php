<?php $active = $active ?? ''; ?>
<header class="mvola-navbar">
    <div class="mvola-navbar__inner">
        <a class="mvola-navbar__brand" href="<?= base_url('admin/dashboard') ?>">
            <img src="<?= base_url('Mvoladashboard/SVG/MVola%20logo.svg') ?>" alt="MVola" class="mvola-navbar__logo">
        </a>

        <button type="button" class="mvola-navbar__toggle" data-navbar-toggle aria-label="Ouvrir le menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="mvola-navbar__actions" data-navbar-menu>
            <a href="<?= base_url('admin/dashboard') ?>" class="nav-btn <?= $active === 'dashboard' ? 'nav-btn--active' : 'nav-btn--outline' ?>">Dashboard</a>
            <a href="<?= base_url('admin/configuration') ?>" class="nav-btn <?= $active === 'configuration' ? 'nav-btn--active' : 'nav-btn--outline' ?>">Configuration des regles</a>
            <a href="<?= base_url('admin/clients') ?>" class="nav-btn <?= $active === 'clients' ? 'nav-btn--active' : 'nav-btn--outline' ?>">Clients</a>
            <a href="<?= base_url('admin/historique') ?>" class="nav-btn <?= $active === 'historique' ? 'nav-btn--active' : 'nav-btn--outline' ?>">Historique</a>
            <a href="<?= base_url('deconnexion') ?>" class="nav-btn nav-btn--solid">Deconnexion</a>
        </nav>
    </div>
</header>

<div class="mvola-toast" data-navbar-toast></div>

<link rel="stylesheet" href="<?= base_url('Mvoladashboard/css/navbar.css') ?>">
<script src="<?= base_url('Mvoladashboard/js/navbar.js') ?>" defer></script>
