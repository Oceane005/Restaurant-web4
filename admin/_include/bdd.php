<?php
$bd  = __DIR__ . "/../../database/database.sqlite";
$pdo = new PDO("sqlite:" . $bd);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Ajoute les colonnes manquantes à la table reservation si elles n'existent pas encore
$existing_cols = array_column(
    $pdo->query("PRAGMA table_info(reservation)")->fetchAll(),
    'name'
);
foreach (['nom', 'email', 'telephone', 'notes'] as $col) {
    if (!in_array($col, $existing_cols)) {
        $pdo->exec("ALTER TABLE reservation ADD COLUMN $col TEXT");
    }
}