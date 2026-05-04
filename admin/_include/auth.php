<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /admin/connexion.php");
    exit();
}
require_once __DIR__ . '/bdd.php';
