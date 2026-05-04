<?php
require_once '_include/auth.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$upload_dir = __DIR__ . '/uploads/infos/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// -----------------------------------------------------------
// Gestion des POST
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM info_site WHERE id = ?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = ['ok', "L'information a bien été supprimée."];

    } elseif ($action === 'add' || $action === 'edit') {
        $titre  = trim($_POST['titre']  ?? '');
        $desc   = trim($_POST['desc']   ?? '');
        $img    = $_POST['img_actuelle'] ?? null;

        // Téléversement de fichier
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (in_array(mime_content_type($_FILES['image']['tmp_name']), $allowed)) {
                $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('info_') . '.' . strtolower($ext);
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename);
                $img = '/admin/uploads/infos/' . $filename;
            }
        }
        if (!empty($_POST['img_url'])) $img = $_POST['img_url'];

        if ($action === 'add') {
            $pdo->prepare("INSERT INTO info_site (titre, desc, img_url) VALUES (?, ?, ?)")
                ->execute([$titre, $desc, $img]);
            $_SESSION['flash'] = ['ok', "L'information a bien été ajoutée."];
        } else {
            $pdo->prepare("UPDATE info_site SET titre=?, desc=?, img_url=? WHERE id=?")
                ->execute([$titre, $desc, $img, (int)$_POST['id']]);
            $_SESSION['flash'] = ['ok', "L'information a bien été modifiée."];
        }
    }

    header("Location: informations.php");
    exit();
}

// -----------------------------------------------------------
// Requête GET
// -----------------------------------------------------------
$infos = $pdo->query("SELECT * FROM info_site ORDER BY id DESC")->fetchAll();

$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM info_site WHERE id = ?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}

require_once '_include/admin-head.php';
require_once '_include/admin-nav.php';
?>
<main class="adm-main">
  <div class="adm-section-header">
    <h1 class="adm-page-title" style="margin:0;">Informations du site</h1>
    <a href="#form-info" class="adm-btn adm-btn--primary">+ Ajouter</a>
  </div>
  <p style="color:var(--text-muted);font-size:.85rem;margin:0 0 24px;">
    Actualités, prix gagnés, informations de contact ou toute autre information à mettre en avant.
  </p>

  <?php if ($flash): ?>
    <div class="adm-flash adm-flash--<?= $flash[0] === 'ok' ? 'success' : 'error' ?>">
      <?= htmlspecialchars($flash[1]) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($infos)): ?>
    <p style="color:var(--text-muted);">Aucune information pour le moment.</p>
  <?php else: ?>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr><th>Photo</th><th>Titre</th><th>Description</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($infos as $info): ?>
        <tr>
          <td>
            <?php if ($info['img_url']): ?>
              <img src="<?= htmlspecialchars($info['img_url']) ?>" alt="" class="adm-thumb">
            <?php else: ?>—<?php endif; ?>
          </td>
          <td><?= htmlspecialchars($info['titre']) ?></td>
          <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?= htmlspecialchars($info['desc'] ?? '') ?>
          </td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <a href="?edit=<?= $info['id'] ?>#form-info" class="adm-btn adm-btn--edit">Modifier</a>
            <form method="POST" onsubmit="return confirm('Supprimer cet élément ?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $info['id'] ?>">
              <button type="submit" class="adm-btn adm-btn--delete">Supprimer</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <!-- Form -->
  <div class="adm-form-panel" id="form-info">
    <h2><?= $edit ? 'Modifier l\'information' : 'Ajouter une information' ?></h2>
    <form class="adm-form" method="POST" action="informations.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'add' ?>">
      <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
        <input type="hidden" name="img_actuelle" value="<?= htmlspecialchars($edit['img_url'] ?? '') ?>">
      <?php endif; ?>

      <div class="adm-form-group adm-form-full">
        <label for="i-titre">Titre *</label>
        <input id="i-titre" type="text" name="titre" required
               value="<?= htmlspecialchars($edit['titre'] ?? '') ?>">
      </div>
      <div class="adm-form-group adm-form-full">
        <label for="i-desc">Description</label>
        <textarea id="i-desc" name="desc"><?= htmlspecialchars($edit['desc'] ?? '') ?></textarea>
      </div>
      <div class="adm-form-group">
        <label for="i-img-file">Photo (fichier)</label>
        <?php if (!empty($edit['img_url'])): ?>
          <div class="adm-current-img">
            <img src="<?= htmlspecialchars($edit['img_url']) ?>" alt="">
            <span>Image actuelle</span>
          </div>
        <?php endif; ?>
        <input id="i-img-file" type="file" name="image" accept="image/*">
      </div>
      <div class="adm-form-group">
        <label for="i-img-url">Ou URL de l'image</label>
        <input id="i-img-url" type="url" name="img_url" placeholder="https://...">
      </div>
      <div class="adm-form-actions adm-form-full">
        <button type="submit" class="adm-btn adm-btn--primary">
          <?= $edit ? 'Enregistrer les modifications' : 'Ajouter' ?>
        </button>
        <?php if ($edit): ?>
          <a href="informations.php" class="adm-btn adm-btn--export">Annuler</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</main>
<?php require_once '_include/admin-foot.php'; ?>
