<?php
require_once '_include/auth.php';

$nb_plats        = (int)$pdo->query("SELECT COUNT(*) FROM menu")->fetchColumn();
$nb_boissons     = (int)$pdo->query("SELECT COUNT(*) FROM boisson")->fetchColumn();
$nb_reservations = (int)$pdo->query("SELECT COUNT(*) FROM reservation")->fetchColumn();
$nb_admins       = (int)$pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
$nb_infos        = (int)$pdo->query("SELECT COUNT(*) FROM info_site")->fetchColumn();

require_once '_include/admin-head.php';
require_once '_include/admin-nav.php';
?>
<main class="adm-main">
  <h1 class="adm-page-title">Tableau de bord</h1>
  <div class="adm-cards">

    <a href="plats.php" class="adm-card">
      <span class="adm-card-icon">&#x1F372;</span>
      <span class="adm-card-count"><?= $nb_plats + $nb_boissons ?></span>
      <span class="adm-card-label">Plats &amp; Boissons</span>
    </a>

    <a href="reservations.php" class="adm-card">
      <span class="adm-card-icon">&#x1F4C5;</span>
      <span class="adm-card-count"><?= $nb_reservations ?></span>
      <span class="adm-card-label">Réservations</span>
    </a>

    <a href="administrateurs.php" class="adm-card">
      <span class="adm-card-icon">&#x1F464;</span>
      <span class="adm-card-count"><?= $nb_admins ?></span>
      <span class="adm-card-label">Administrateurs</span>
    </a>

    <a href="informations.php" class="adm-card">
      <span class="adm-card-icon">&#x1F4F0;</span>
      <span class="adm-card-count"><?= $nb_infos ?></span>
      <span class="adm-card-label">Informations</span>
    </a>

  </div>
</main>
<?php require_once '_include/admin-foot.php'; ?>
