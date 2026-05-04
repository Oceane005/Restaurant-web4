<?php
require_once '_include/auth.php';

$current_id = (int)$_SESSION['admin_id'];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// -----------------------------------------------------------
// Gestion des POST
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        if ($id === $current_id) {
            $_SESSION['flash'] = ['err', 'Impossible de supprimer votre propre compte.'];
        } else {
            $pdo->prepare("DELETE FROM user WHERE id = ?")->execute([$id]);
            $_SESSION['flash'] = ['ok', "L'administrateur a bien été supprimé."];
        }

    } elseif ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = ['err', 'Tous les champs sont obligatoires.'];
        } else {
            $pdo->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)")
                ->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['flash'] = ['ok', "L'administrateur a bien été ajouté."];
        }

    } elseif ($action === 'edit') {
        $id       = (int)$_POST['id'];
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';

        if ($username === '' || $email === '') {
            $_SESSION['flash'] = ['err', 'Le nom et le courriel sont obligatoires.'];
        } elseif ($password !== '') {
            $pdo->prepare("UPDATE user SET username=?, email=?, password=? WHERE id=?")
                ->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $id]);
            $_SESSION['flash'] = ['ok', "L'administrateur a bien été modifié."];
        } else {
            $pdo->prepare("UPDATE user SET username=?, email=? WHERE id=?")
                ->execute([$username, $email, $id]);
            $_SESSION['flash'] = ['ok', "L'administrateur a bien été modifié."];
        }
    }

    header("Location: administrateurs.php");
    exit();
}

// -----------------------------------------------------------
// Requête GET
// -----------------------------------------------------------
$admins = $pdo->query("SELECT id, username, email, created_at FROM user ORDER BY id")->fetchAll();

$edit_admin = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $pdo->prepare("SELECT id, username, email FROM user WHERE id = ?");
    $s->execute([(int)$_GET['edit']]);
    $edit_admin = $s->fetch();
}

require_once '_include/admin-head.php';
require_once '_include/admin-nav.php';
?>
<main class="adm-main">
  <h1 class="adm-page-title">Administrateurs</h1>

  <?php if ($flash): ?>
    <div class="adm-flash adm-flash--<?= $flash[0] === 'ok' ? 'success' : 'error' ?>">
      <?= htmlspecialchars($flash[1]) ?>
    </div>
  <?php endif; ?>

  <!-- Liste -->
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr><th>#</th><th>Nom d'utilisateur</th><th>Courriel</th><th>Créé le</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($admins as $adm): ?>
        <tr>
          <td><?= $adm['id'] ?></td>
          <td><?= htmlspecialchars($adm['username']) ?></td>
          <td><?= htmlspecialchars($adm['email']) ?></td>
          <td><?= htmlspecialchars($adm['created_at'] ?? '—') ?></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <a href="?edit=<?= $adm['id'] ?>#form-admin" class="adm-btn adm-btn--edit">Modifier</a>
            <?php if ($adm['id'] !== $current_id): ?>
              <form method="POST" onsubmit="return confirm('Supprimer cet administrateur ?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $adm['id'] ?>">
                <button type="submit" class="adm-btn adm-btn--delete">Supprimer</button>
              </form>
            <?php else: ?>
              <span style="font-size:.75rem;color:var(--text-muted);padding:6px 0;">(vous)</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Formulaire ajout / modification -->
  <div class="adm-form-panel" id="form-admin">
    <h2><?= $edit_admin ? 'Modifier l\'administrateur' : 'Ajouter un administrateur' ?></h2>
    <form class="adm-form" method="POST" action="administrateurs.php">
      <input type="hidden" name="action" value="<?= $edit_admin ? 'edit' : 'add' ?>">
      <?php if ($edit_admin): ?>
        <input type="hidden" name="id" value="<?= $edit_admin['id'] ?>">
      <?php endif; ?>

      <div class="adm-form-group">
        <label for="a-username">Nom d'utilisateur *</label>
        <input id="a-username" type="text" name="username" required
               value="<?= htmlspecialchars($edit_admin['username'] ?? '') ?>">
      </div>
      <div class="adm-form-group">
        <label for="a-email">Courriel *</label>
        <input id="a-email" type="email" name="email" required
               value="<?= htmlspecialchars($edit_admin['email'] ?? '') ?>">
      </div>
      <div class="adm-form-group">
        <label for="a-password">
          <?= $edit_admin ? 'Nouveau mot de passe (laisser vide = inchangé)' : 'Mot de passe *' ?>
        </label>
        <input id="a-password" type="password" name="password"
               <?= $edit_admin ? '' : 'required' ?> placeholder="••••••••">
      </div>

      <div class="adm-form-actions adm-form-full">
        <button type="submit" class="adm-btn adm-btn--primary">
          <?= $edit_admin ? 'Enregistrer les modifications' : 'Ajouter l\'administrateur' ?>
        </button>
        <?php if ($edit_admin): ?>
          <a href="administrateurs.php" class="adm-btn adm-btn--export">Annuler</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</main>
<?php require_once '_include/admin-foot.php'; ?>
