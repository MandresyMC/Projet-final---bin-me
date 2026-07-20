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
    <section id="page-dashboard-admin">
  <div class="app-wrapper">

    <aside class="sidebar">
      <div class="sidebar-logo">Fit<span>Space</span> <span style="font-size:0.6rem;background:var(--accent);color:#fff;padding:2px 6px;border-radius:4px;vertical-align:middle;">Admin</span></div>
      <div class="sidebar-section">Gestion</div>
      <ul class="sidebar-nav">
        <li>
          <a href="/admin/reservations">
            <i class="bi bi-bookmark-star-fill"></i> Réservations
            <span class="sidebar-badge urgent"><?= isset($reservations) ? count($reservations) : 0 ?></span>
          </a>
        </li>
        <li><a href="/admin/creneaux"><i class="bi bi-calendar-week-fill"></i> Créneaux</a></li>
        <li><a href="#page-admin-clients"><i class="bi bi-people-fill"></i> Clients</a></li>
      </ul>
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="avatar" style="background:#0f3460;">AD</div>
          <div class="user-info"><div class="name">Admin</div><div class="role">Administrateur</div></div>
          <a href="#page-login" style="margin-left:auto;color:rgba(255,255,255,0.3);font-size:1.1rem;"><i class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
    </aside>

    <div class="main-content">
      <div class="topbar">
        <span class="topbar-title">Vue d'ensemble</span>
        <div class="topbar-actions">
          <a href="#page-admin-creneaux" class="icon-btn" title="Ajouter un créneau"><i class="bi bi-plus-lg"></i></a>
        </div>
      </div>

      <div class="page-content">

        <div class="metrics-row">
          <div class="metric-card">
            <div class="metric-icon yellow"><i class="bi bi-hourglass-split"></i></div>
            <div class="metric-value"><?= isset($nbAttente) ? $nbAttente : 0 ?></div>
            <div class="metric-label">En attente</div>
            <!-- <div class="metric-trend up"><i class="bi bi-arrow-up-short"></i> +2 aujourd'hui</div> -->
          </div>
          <div class="metric-card">
            <div class="metric-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div class="metric-value"><?= isset($nbConfirmees) ? $nbConfirmees : 0 ?></div>
            <div class="metric-label">Confirmées ce mois</div>
          </div>
          <div class="metric-card">
            <div class="metric-icon blue"><i class="bi bi-calendar-check"></i></div>
            <div class="metric-value">6</div>
            <div class="metric-label">Créneaux actifs</div>
          </div>
          <div class="metric-card">
            <div class="metric-icon red"><i class="bi bi-people-fill"></i></div>
            <div class="metric-value">31</div>
            <div class="metric-label">Clients inscrits</div>
            <div class="metric-trend up"><i class="bi bi-arrow-up-short"></i> +3 cette semaine</div>
          </div>
        </div>

        <!-- Réservations récentes -->
        <div class="data-card">
          <div class="data-card-header">
            <h3>Réservations récentes</h3>
            <a href="#page-admin-reservations" style="font-size:0.8rem;color:var(--accent);text-decoration:none;">Tout voir →</a>
          </div>
          <table class="table-custom">
            <thead>
              <tr><th>Client</th><th>Créneau</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php $count = 1;
              foreach(($reservations ?? []) as $r) {
                if ($count > 2) break;
                $count++;
                $dt_r = new DateTime($r['created_at']);
                $date_reservation = $dt_r->format('l d F Y');
                $heure_reservation = $dt_r->format('H\hi'); ?>
              <tr>
                <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar" style="width:28px;height:28px;font-size:0.65rem;">MC</div><span class="td-name"><?= $r['user_nom'] ?></span></div></td>
                <td class="td-muted"><?= $r['ressource_nom'] ?></td>
                <td class="td-muted"><?= $date_reservation ?> · <?= $heure_reservation ?></td>
                <td><span class="badge-statut s-<?= $r['statut'] == 'en_attente' ? 'attente' : ($r['statut'] == 'confirmée' ? 'confirmee' : 'annulee') ?>"><?= $r['statut'] ?></span></td>
                <td>
                  <?php if ($r['statut'] == 'en_attente') { ?>
                    <div class="action-btns">
                      <a href="/admin/reservations/confirmer/<?= $r['id'] ?>" class="btn-sm-custom btn-confirm"><i class="bi bi-check"></i> Confirmer</a>
                      <a href="/admin/reservations/refuser/<?= $r['id'] ?>" class="btn-sm-custom btn-refuse"><i class="bi bi-x"></i> Refuser</a>
                    </div>
                  <?php } else { ?>
                    <span style="font-size:0.75rem;color:var(--muted);">—</span>
                  <?php } ?>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>