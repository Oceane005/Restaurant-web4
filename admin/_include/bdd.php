<?php
$bd  = __DIR__ . "/../../database/database.sqlite";
$pdo = new PDO("sqlite:" . $bd);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

