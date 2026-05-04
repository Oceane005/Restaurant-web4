<?php
$current = basename($_SERVER['PHP_SELF']);
function adm_nav_link(string $file, string $label, string $current): string {
    $active = ($current === $file) ? ' class="active"' : '';
    return "<li><a href=\"/admin/$file\"$active>$label</a></li>";
}
?>
<nav class="adm-nav">
  <a href="/admin/" class="adm-nav-brand">
    <img src="/img/logo.png" alt="Logo" class="adm-nav-logo">
    <span>Admin</span>
  </a>
  <button class="adm-nav-toggle" aria-label="Menu" onclick="this.closest('.adm-nav').classList.toggle('open')">
    <span></span><span></span><span></span>
  </button>
  <ul class="adm-nav-links">
    <?= adm_nav_link('index.php',          'Tableau de bord', $current) ?>
    <?= adm_nav_link('plats.php',          'Plats & Boissons', $current) ?>
    <?= adm_nav_link('reservations.php',   'Réservations',    $current) ?>
    <?= adm_nav_link('administrateurs.php','Administrateurs',  $current) ?>
    <?= adm_nav_link('informations.php',   'Informations',    $current) ?>
  </ul>
  <a href="/admin/deconnexion.php" class="adm-nav-logout">Déconnexion</a>
</nav>
