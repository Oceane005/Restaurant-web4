<?php
session_start();
require_once '_include/bdd.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $password    = $_POST['password']    ?? '';

    $stmt = $pdo->prepare(
        "SELECT id, password FROM user WHERE email = :id OR username = :id"
    );
    $stmt->execute([':id' => $identifiant]);
    $resultat = $stmt->fetch();

    if ($resultat && password_verify($password, $resultat['password'])) {
        $_SESSION['admin_id'] = $resultat['id'];
        header("Location: index.php");
        exit();
    }

    $_SESSION['erreur_connexion'] = true;
    header("Location: connexion.php");
    exit();
}

$erreur = isset($_SESSION['erreur_connexion']);
unset($_SESSION['erreur_connexion']);

require_once '_include/admin-head.php';
?>
<main class="adm-login-page">
  <div class="adm-login-box">
    <h1 class="adm-login-title">Connexion</h1>

    <?php if ($erreur): ?>
      <p class="adm-error-msg">Identifiant ou mot de passe incorrect.</p>
    <?php endif; ?>

    <form class="adm-login-form" method="POST" action="">
      <div class="adm-form-group">
        <label for="identifiant">Courriel ou nom d'utilisateur</label>
        <input type="text" id="identifiant" name="identifiant"
               placeholder="votre@courriel.com ou username" required>
      </div>
      <div class="adm-form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password"
               placeholder="••••••••" required>
      </div>
      <button type="submit" class="adm-btn adm-btn--primary" style="padding:12px;">
        Se connecter
      </button>
    </form>
  </div>
</main>
<?php require_once '_include/admin-foot.php'; ?>
