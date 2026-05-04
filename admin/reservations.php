<?php
require_once '_include/auth.php';

// -----------------------------------------------------------
// Export CSV
// -----------------------------------------------------------
if (isset($_GET['export'])) {
    $rows = $pdo->query(
        "SELECT nom, email, telephone, nb_personne, date, heure, notes, created_at
         FROM reservation ORDER BY created_at DESC"
    )->fetchAll();

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="reservations.csv"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    // BOM UTF-8 pour que Excel ouvre correctement
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Nom','Courriel','Téléphone','Nb personnes','Date','Heure','Notes','Créé le'], ';');
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['nom']        ?? '',
            $r['email']      ?? '',
            $r['telephone']  ?? '',
            $r['nb_personne'],
            $r['date'],
            $r['heure'],
            $r['notes']      ?? '',
            $r['created_at'],
        ], ';');
    }
    fclose($out);
    exit();
}

// -----------------------------------------------------------
// Suppression
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $pdo->prepare("DELETE FROM reservation WHERE id = ?")->execute([(int)$_POST['id']]);
    $_SESSION['flash'] = ['ok', 'La réservation a bien été supprimée.'];
    header("Location: reservations.php");
    exit();
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$reservations = $pdo->query(
    "SELECT * FROM reservation ORDER BY created_at DESC"
)->fetchAll();

require_once '_include/admin-head.php';
require_once '_include/admin-nav.php';
?>
<main class="adm-main">
  <div class="adm-section-header">
    <h1 class="adm-page-title" style="margin:0;">Réservations</h1>
    <a href="?export=1" class="adm-btn adm-btn--export">Exporter CSV</a>
  </div>

  <?php if ($flash): ?>
    <div class="adm-flash adm-flash--<?= $flash[0] === 'ok' ? 'success' : 'error' ?>">
      <?= htmlspecialchars($flash[1]) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($reservations)): ?>
    <p style="color:var(--text-muted);">Aucune réservation pour le moment.</p>
  <?php else: ?>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>#</th><th>Nom</th><th>Courriel</th><th>Téléphone</th>
          <th>Personnes</th><th>Date</th><th>Heure</th><th>Notes</th>
          <th>Reçue le</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservations as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['nom'] ?? '—') ?></td>
          <td><?= htmlspecialchars($r['email'] ?? '—') ?></td>
          <td><?= htmlspecialchars($r['telephone'] ?? '—') ?></td>
          <td><?= (int)$r['nb_personne'] ?></td>
          <td><?= htmlspecialchars($r['date']) ?></td>
          <td><?= htmlspecialchars($r['heure']) ?></td>
          <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?= htmlspecialchars($r['notes'] ?? '') ?: '—' ?>
          </td>
          <td style="white-space:nowrap;"><?= htmlspecialchars($r['created_at']) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Supprimer cette réservation ?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button type="submit" class="adm-btn adm-btn--delete">Supprimer</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</main>
<?php require_once '_include/admin-foot.php'; ?>
