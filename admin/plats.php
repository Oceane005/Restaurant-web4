<?php
require_once '_include/auth.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$upload_dir = __DIR__ . '/uploads/plats/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

function handle_image(array $file, string $upload_dir, ?string $current): ?string {
    if (!empty($file['name'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) return $current;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('plat_') . '.' . strtolower($ext);
        move_uploaded_file($file['tmp_name'], $upload_dir . $filename);
        return '/admin/uploads/plats/' . $filename;
    }
    return $current;
}

// -----------------------------------------------------------
// Traitement POST — ajout / modification / suppression
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $type   = $_POST['type']   ?? 'menu'; // 'menu' ou 'boisson'

    if ($action === 'delete_menu') {
        $pdo->prepare("DELETE FROM menu WHERE id = ?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = ['ok', 'Le plat a bien été supprimé.'];

    } elseif ($action === 'delete_boisson') {
        $pdo->prepare("DELETE FROM boisson WHERE id = ?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = ['ok', 'La boisson a bien été supprimée.'];

    } elseif ($action === 'add_menu' || $action === 'edit_menu') {
        $nom        = trim($_POST['nom']        ?? '');
        $ingredient = trim($_POST['ingredient'] ?? '');
        $cat_id     = (int)($_POST['categorie_id'] ?? 1);
        $tag        = trim($_POST['tag']        ?? '');
        $nb_morceau = $_POST['nb_morceau'] !== '' ? (int)$_POST['nb_morceau'] : null;
        $allergene  = trim($_POST['allergene']  ?? '');
        $prix       = (float)($_POST['prix']    ?? 0);
        $img        = handle_image($_FILES['image'] ?? [], $upload_dir, $_POST['img_actuelle'] ?? null);

        if (!empty($_POST['img_url'])) $img = $_POST['img_url'];

        if ($action === 'add_menu') {
            $pdo->prepare("INSERT INTO menu (nom,ingredient,categorie_id,tag,nb_morceau,allergene,prix,img_url)
                           VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$nom,$ingredient,$cat_id,$tag,$nb_morceau,$allergene,$prix,$img]);
            $_SESSION['flash'] = ['ok', 'Le plat a bien été ajouté.'];
        } else {
            $id = (int)$_POST['id'];
            $pdo->prepare("UPDATE menu SET nom=?,ingredient=?,categorie_id=?,tag=?,nb_morceau=?,allergene=?,prix=?,img_url=? WHERE id=?")
                ->execute([$nom,$ingredient,$cat_id,$tag,$nb_morceau,$allergene,$prix,$img,$id]);
            $_SESSION['flash'] = ['ok', 'Le plat a bien été modifié.'];
        }

    } elseif ($action === 'add_boisson' || $action === 'edit_boisson') {
        $nom    = trim($_POST['nom']    ?? '');
        $pays   = trim($_POST['pays']   ?? '');
        $annee  = $_POST['annee'] !== '' ? (int)$_POST['annee'] : null;
        $cat_id = (int)($_POST['categorie_id'] ?? 4);
        $prix   = (float)($_POST['prix'] ?? 0);
        $img    = handle_image($_FILES['image'] ?? [], $upload_dir, $_POST['img_actuelle'] ?? null);
        if (!empty($_POST['img_url'])) $img = $_POST['img_url'];

        if ($action === 'add_boisson') {
            $pdo->prepare("INSERT INTO boisson (nom,pay,annee,categorie_id,prix,img_url) VALUES (?,?,?,?,?,?)")
                ->execute([$nom,$pays,$annee,$cat_id,$prix,$img]);
            $_SESSION['flash'] = ['ok', 'La boisson a bien été ajoutée.'];
        } else {
            $id = (int)$_POST['id'];
            $pdo->prepare("UPDATE boisson SET nom=?,pay=?,annee=?,categorie_id=?,prix=?,img_url=? WHERE id=?")
                ->execute([$nom,$pays,$annee,$cat_id,$prix,$img,$id]);
            $_SESSION['flash'] = ['ok', 'La boisson a bien été modifiée.'];
        }
    }

    header("Location: plats.php");
    exit();
}

// -----------------------------------------------------------
// Récupération des données
// -----------------------------------------------------------
$food_cats  = $pdo->query("SELECT * FROM categorie WHERE type <= 3 ORDER BY type")->fetchAll();
$drink_cats = $pdo->query("SELECT * FROM categorie WHERE type >= 4 ORDER BY type")->fetchAll();

// Toutes les catégories indexées par type
$cat_map = [];
foreach ($pdo->query("SELECT * FROM categorie") as $c) {
    $cat_map[$c['type']] = $c['nom'];
}

// Plats groupés par catégorie
$plats_by_cat = [];
foreach ($food_cats as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE categorie_id = ? ORDER BY nom");
    $stmt->execute([$cat['type']]);
    $plats_by_cat[$cat['type']] = ['cat' => $cat, 'items' => $stmt->fetchAll()];
}

// Boissons groupées par catégorie
$boissons_by_cat = [];
foreach ($drink_cats as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM boisson WHERE categorie_id = ? ORDER BY nom");
    $stmt->execute([$cat['type']]);
    $boissons_by_cat[$cat['type']] = ['cat' => $cat, 'items' => $stmt->fetchAll()];
}

// Mode modification ?
$edit_menu    = null;
$edit_boisson = null;
if (isset($_GET['edit_menu']) && is_numeric($_GET['edit_menu'])) {
    $s = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
    $s->execute([(int)$_GET['edit_menu']]);
    $edit_menu = $s->fetch();
}
if (isset($_GET['edit_boisson']) && is_numeric($_GET['edit_boisson'])) {
    $s = $pdo->prepare("SELECT * FROM boisson WHERE id = ?");
    $s->execute([(int)$_GET['edit_boisson']]);
    $edit_boisson = $s->fetch();
}

require_once '_include/admin-head.php';
require_once '_include/admin-nav.php';
?>
<main class="adm-main">
  <h1 class="adm-page-title">Plats &amp; Boissons</h1>

  <?php if ($flash): ?>
    <div class="adm-flash adm-flash--<?= $flash[0] === 'ok' ? 'success' : 'error' ?>">
      <?= htmlspecialchars($flash[1]) ?>
    </div>
  <?php endif; ?>

  <!-- ======================================================
       SECTION : PLATS (menu table)
       ====================================================== -->
  <div class="adm-section-header">
    <h2 class="adm-section-title">Plats</h2>
    <a href="#form-plat" class="adm-btn adm-btn--primary">+ Ajouter un plat</a>
  </div>

  <?php foreach ($plats_by_cat as $group): ?>
    <h3 style="color:var(--text-muted);font-size:.8rem;letter-spacing:.1em;text-transform:uppercase;margin:20px 0 8px;">
      <?= htmlspecialchars(ucfirst($group['cat']['nom'])) ?>
    </h3>
    <?php if (empty($group['items'])): ?>
      <p style="color:var(--text-muted);font-size:.88rem;margin:0 0 16px;">Aucun plat dans cette catégorie.</p>
    <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Photo</th><th>Nom</th><th>Ingrédients</th><th>Tag</th>
            <th>Morceaux</th><th>Allergènes</th><th>Prix</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($group['items'] as $item): ?>
          <tr>
            <td>
              <?php if ($item['img_url']): ?>
                <img src="<?= htmlspecialchars($item['img_url']) ?>" alt="" class="adm-thumb">
              <?php else: ?>—<?php endif; ?>
            </td>
            <td><?= htmlspecialchars($item['nom']) ?></td>
            <td style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              <?= htmlspecialchars($item['ingredient'] ?? '') ?>
            </td>
            <td><?= $item['tag'] ? '<span class="adm-cat-badge">' . htmlspecialchars($item['tag']) . '</span>' : '—' ?></td>
            <td><?= $item['nb_morceau'] ?? '—' ?></td>
            <td><?= htmlspecialchars($item['allergene'] ?? '') ?: '—' ?></td>
            <td><?= number_format((float)$item['prix'], 2) ?> $</td>
            <td style="white-space:nowrap;display:flex;gap:6px;">
              <a href="?edit_menu=<?= $item['id'] ?>#form-plat" class="adm-btn adm-btn--edit">Modifier</a>
              <form method="POST" style="display:inline;"
                    onsubmit="return confirm('Supprimer ce plat ?')">
                <input type="hidden" name="action" value="delete_menu">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="adm-btn adm-btn--delete">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- FORMULAIRE PLAT (ajout / modification) -->
  <div class="adm-form-panel" id="form-plat">
    <h2><?= $edit_menu ? 'Modifier le plat' : 'Ajouter un plat' ?></h2>
    <form class="adm-form" method="POST" action="plats.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?= $edit_menu ? 'edit_menu' : 'add_menu' ?>">
      <?php if ($edit_menu): ?>
        <input type="hidden" name="id" value="<?= $edit_menu['id'] ?>">
        <input type="hidden" name="img_actuelle" value="<?= htmlspecialchars($edit_menu['img_url'] ?? '') ?>">
      <?php endif; ?>

      <div class="adm-form-group">
        <label for="p-nom">Nom *</label>
        <input id="p-nom" type="text" name="nom" required
               value="<?= htmlspecialchars($edit_menu['nom'] ?? '') ?>">
      </div>
      <div class="adm-form-group">
        <label for="p-cat">Catégorie *</label>
        <select id="p-cat" name="categorie_id">
          <?php foreach ($food_cats as $cat): ?>
            <option value="<?= $cat['type'] ?>"
              <?= ($edit_menu && $edit_menu['categorie_id'] == $cat['type']) ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucfirst($cat['nom'])) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="adm-form-group">
        <label for="p-prix">Prix ($) *</label>
        <input id="p-prix" type="number" name="prix" step="0.01" min="0" required
               value="<?= $edit_menu['prix'] ?? '' ?>">
      </div>
      <div class="adm-form-group">
        <label for="p-tag">Tag</label>
        <input id="p-tag" type="text" name="tag" placeholder="ex: table_d_hote"
               value="<?= htmlspecialchars($edit_menu['tag'] ?? '') ?>">
      </div>
      <div class="adm-form-group">
        <label for="p-morceau">Nombre de morceaux</label>
        <input id="p-morceau" type="number" name="nb_morceau" min="1"
               value="<?= $edit_menu['nb_morceau'] ?? '' ?>">
      </div>
      <div class="adm-form-group">
        <label for="p-allergene">Allergènes</label>
        <input id="p-allergene" type="text" name="allergene" placeholder="ex: gluten, lactose"
               value="<?= htmlspecialchars($edit_menu['allergene'] ?? '') ?>">
      </div>
      <div class="adm-form-group adm-form-full">
        <label for="p-ingredient">Ingrédients / Description *</label>
        <textarea id="p-ingredient" name="ingredient" required><?= htmlspecialchars($edit_menu['ingredient'] ?? '') ?></textarea>
      </div>
      <div class="adm-form-group">
        <label for="p-img-file">Photo (fichier)</label>
        <?php if (!empty($edit_menu['img_url'])): ?>
          <div class="adm-current-img">
            <img src="<?= htmlspecialchars($edit_menu['img_url']) ?>" alt="">
            <span>Image actuelle</span>
          </div>
        <?php endif; ?>
        <input id="p-img-file" type="file" name="image" accept="image/*">
      </div>
      <div class="adm-form-group">
        <label for="p-img-url">Ou URL de l'image</label>
        <input id="p-img-url" type="url" name="img_url" placeholder="https://..."
               value="">
      </div>
      <div class="adm-form-actions adm-form-full">
        <button type="submit" class="adm-btn adm-btn--primary">
          <?= $edit_menu ? 'Enregistrer les modifications' : 'Ajouter le plat' ?>
        </button>
        <?php if ($edit_menu): ?>
          <a href="plats.php" class="adm-btn adm-btn--export">Annuler</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- ======================================================
       SECTION : BOISSONS (boisson table)
       ====================================================== -->
  <div class="adm-section-header" style="margin-top:16px;">
    <h2 class="adm-section-title">Cave &amp; Boissons</h2>
    <a href="#form-boisson" class="adm-btn adm-btn--primary">+ Ajouter une boisson</a>
  </div>

  <?php foreach ($boissons_by_cat as $group): ?>
    <h3 style="color:var(--text-muted);font-size:.8rem;letter-spacing:.1em;text-transform:uppercase;margin:20px 0 8px;">
      <?= htmlspecialchars(ucfirst($group['cat']['nom'])) ?>
    </h3>
    <?php if (empty($group['items'])): ?>
      <p style="color:var(--text-muted);font-size:.88rem;margin:0 0 16px;">Aucune boisson dans cette catégorie.</p>
    <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr><th>Photo</th><th>Nom</th><th>Pays</th><th>Année</th><th>Prix</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($group['items'] as $item): ?>
          <tr>
            <td>
              <?php if ($item['img_url']): ?>
                <img src="<?= htmlspecialchars($item['img_url']) ?>" alt="" class="adm-thumb">
              <?php else: ?>—<?php endif; ?>
            </td>
            <td><?= htmlspecialchars($item['nom']) ?></td>
            <td><?= htmlspecialchars($item['pay'] ?? '—') ?></td>
            <td><?= $item['annee'] ?? '—' ?></td>
            <td><?= number_format((float)$item['prix'], 2) ?> $</td>
            <td style="white-space:nowrap;display:flex;gap:6px;">
              <a href="?edit_boisson=<?= $item['id'] ?>#form-boisson" class="adm-btn adm-btn--edit">Modifier</a>
              <form method="POST" style="display:inline;"
                    onsubmit="return confirm('Supprimer cette boisson ?')">
                <input type="hidden" name="action" value="delete_boisson">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="adm-btn adm-btn--delete">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- FORMULAIRE BOISSON -->
  <div class="adm-form-panel" id="form-boisson">
    <h2><?= $edit_boisson ? 'Modifier la boisson' : 'Ajouter une boisson' ?></h2>
    <form class="adm-form" method="POST" action="plats.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?= $edit_boisson ? 'edit_boisson' : 'add_boisson' ?>">
      <?php if ($edit_boisson): ?>
        <input type="hidden" name="id" value="<?= $edit_boisson['id'] ?>">
        <input type="hidden" name="img_actuelle" value="<?= htmlspecialchars($edit_boisson['img_url'] ?? '') ?>">
      <?php endif; ?>

      <div class="adm-form-group">
        <label for="b-nom">Nom *</label>
        <input id="b-nom" type="text" name="nom" required
               value="<?= htmlspecialchars($edit_boisson['nom'] ?? '') ?>">
      </div>
      <div class="adm-form-group">
        <label for="b-cat">Catégorie *</label>
        <select id="b-cat" name="categorie_id">
          <?php foreach ($drink_cats as $cat): ?>
            <option value="<?= $cat['type'] ?>"
              <?= ($edit_boisson && $edit_boisson['categorie_id'] == $cat['type']) ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucfirst($cat['nom'])) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="adm-form-group">
        <label for="b-pays">Pays</label>
        <input id="b-pays" type="text" name="pays" placeholder="ex: Japon"
               value="<?= htmlspecialchars($edit_boisson['pay'] ?? '') ?>">
      </div>
      <div class="adm-form-group">
        <label for="b-annee">Année</label>
        <input id="b-annee" type="number" name="annee" min="1900" max="2099"
               value="<?= $edit_boisson['annee'] ?? '' ?>">
      </div>
      <div class="adm-form-group">
        <label for="b-prix">Prix ($) *</label>
        <input id="b-prix" type="number" name="prix" step="0.01" min="0" required
               value="<?= $edit_boisson['prix'] ?? '' ?>">
      </div>
      <div class="adm-form-group">
        <label for="b-img-file">Photo (fichier)</label>
        <?php if (!empty($edit_boisson['img_url'])): ?>
          <div class="adm-current-img">
            <img src="<?= htmlspecialchars($edit_boisson['img_url']) ?>" alt="">
            <span>Image actuelle</span>
          </div>
        <?php endif; ?>
        <input id="b-img-file" type="file" name="image" accept="image/*">
      </div>
      <div class="adm-form-group">
        <label for="b-img-url">Ou URL de l'image</label>
        <input id="b-img-url" type="url" name="img_url" placeholder="https://...">
      </div>
      <div class="adm-form-actions adm-form-full">
        <button type="submit" class="adm-btn adm-btn--primary">
          <?= $edit_boisson ? 'Enregistrer les modifications' : 'Ajouter la boisson' ?>
        </button>
        <?php if ($edit_boisson): ?>
          <a href="plats.php" class="adm-btn adm-btn--export">Annuler</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

</main>
<?php require_once '_include/admin-foot.php'; ?>
