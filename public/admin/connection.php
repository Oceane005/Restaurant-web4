<?php require_once '../components/header/header.php'; ?>
<?php require_once '../components/navigation/navigation.php'; ?>

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

<style>
.connection-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--dark);
    padding: 120px 20px 60px;
}

.connection-container {
    background-color: var(--dark-card);
    border: 1px solid rgba(0, 229, 209, 0.15);
    border-radius: 12px;
    padding: clamp(32px, 5vw, 56px);
    width: 100%;
    max-width: 420px;
}

.connection-title {
    font-family: var(--font-display);
    font-size: clamp(1.8rem, 4vw, 2.4rem);
    color: var(--teal);
    text-align: center;
    margin-bottom: 36px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.connection-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-family: var(--font-body);
    font-size: 0.85rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.form-group input {
    background-color: var(--dark-mid);
    border: 1px solid rgba(0, 229, 209, 0.2);
    border-radius: 6px;
    color: var(--text);
    font-family: var(--font-body);
    font-size: 1rem;
    padding: 12px 16px;
    transition: border-color 0.2s;
    outline: none;
}

.form-group input::placeholder {
    color: var(--text-muted);
    opacity: 0.5;
}

.form-group input:focus {
    border-color: var(--teal);
}

.btn-connect {
    margin-top: 8px;
    background-color: var(--magenta);
    color: var(--text);
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    border: none;
    border-radius: 6px;
    padding: 14px;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
}

.btn-connect:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

</style>

<?php require_once '../components/footer/footer.php'; ?>
