<?php require_once '../components/header/header.php'; ?>
<?php require_once '../components/navigation/navigation.php'; ?>
<link rel="stylesheet" href="css/adm-style.css">
<main class="connection-page">
    <div class="connection-container">
        <h1 class="connection-title">Connexion</h1>

        <form class="connection-form" method="POST" action="">
            <div class="form-group">
                <label for="email">Courriel</label>
                <input type="email" id="email" name="email" placeholder="votre@courriel.com" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-connect">Se connecter</button>
        </form>


    </div>
</main>

<?php require_once '../components/footer/footer.php'; ?>