<?php
// Traitement du formulaire de réservation
$res_flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'reservation') {
    // Chemin depuis n'importe quelle page publique incluant ce composant
    $db_path = $_SERVER['DOCUMENT_ROOT'] . '/database/database.sqlite';
    // Repli : résoudre le chemin relatif à ce fichier
    if (!file_exists($db_path)) {
        $db_path = __DIR__ . '/../../database/database.sqlite';
    }

    $nom          = trim($_POST['res_nom']     ?? '');
    $email        = trim($_POST['res_email']   ?? '');
    $telephone    = trim($_POST['res_tel']     ?? '');
    $nb_personne  = (int)($_POST['res_nb']     ?? 1);
    $date         = $_POST['res_date']          ?? '';
    $heure        = $_POST['res_heure']         ?? '';
    $notes        = trim($_POST['res_notes']   ?? '');

    if ($nom && $email && $date && $heure && $nb_personne >= 1) {
        $pdo_res = new PDO("sqlite:$db_path");
        $pdo_res->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo_res->prepare(
            "INSERT INTO reservation (nom, email, telephone, nb_personne, date, heure, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        )->execute([$nom, $email, $telephone, $nb_personne, $date, $heure, $notes]);

        $res_flash = 'success';
    } else {
        $res_flash = 'error';
    }
}
?>
<section class="reservation" id="reservation">
  <p class="reservation-label">RÉSERVEZ UNE TABLE</p>

  <?php if ($res_flash === 'success'): ?>
    <div class="reservation-flash reservation-flash--ok">
      Votre réservation a bien été envoyée. Nous vous contacterons pour confirmation.
    </div>
  <?php elseif ($res_flash === 'error'): ?>
    <div class="reservation-flash reservation-flash--err">
      Veuillez remplir tous les champs obligatoires.
    </div>
  <?php endif; ?>

  <form class="reservation-form" method="POST" action="#reservation">
    <input type="hidden" name="form" value="reservation">

    <div class="reservation-row">
      <div class="reservation-group">
        <label for="res_nom">Nom complet *</label>
        <input type="text" id="res_nom" name="res_nom" placeholder="Jean Dupont" required
               value="<?= htmlspecialchars($_POST['res_nom'] ?? '') ?>">
      </div>
      <div class="reservation-group">
        <label for="res_email">Courriel *</label>
        <input type="email" id="res_email" name="res_email" placeholder="votre@courriel.com" required
               value="<?= htmlspecialchars($_POST['res_email'] ?? '') ?>">
      </div>
      <div class="reservation-group">
        <label for="res_tel">Téléphone</label>
        <input type="tel" id="res_tel" name="res_tel" placeholder="514-000-0000"
               value="<?= htmlspecialchars($_POST['res_tel'] ?? '') ?>">
      </div>
    </div>

    <div class="reservation-row">
      <div class="reservation-group">
        <label for="res_nb">Nombre de personnes *</label>
        <input type="number" id="res_nb" name="res_nb" min="1" max="20" value="2" required>
      </div>
      <div class="reservation-group">
        <label for="res_date">Date *</label>
        <input type="date" id="res_date" name="res_date" required
               min="<?= date('Y-m-d') ?>">
      </div>
      <div class="reservation-group">
        <label for="res_heure">Heure *</label>
        <select id="res_heure" name="res_heure" required>
          <option value="">-- Choisir --</option>
          <?php
          $slots = ['17:00','17:30','18:00','18:30','19:00','19:30',
                    '20:00','20:30','21:00','21:30','22:00'];
          foreach ($slots as $slot): ?>
            <option value="<?= $slot ?>"><?= $slot ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="reservation-group reservation-group--full">
      <label for="res_notes">Notes ou demandes spéciales</label>
      <textarea id="res_notes" name="res_notes" placeholder="Allergie, occasion spéciale…"><?= htmlspecialchars($_POST['res_notes'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="reservation-btn">RÉSERVER</button>
  </form>
</section>
