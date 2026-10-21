<?php
// includes/db.php

// Load local config if present
$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    include $localConfig;
}

$host = getenv('DB_HOST') ?: ($db_host ?? 'localhost');
$db   = getenv('DB_NAME') ?: ($db_name ?? 'firstvault_bank');
$user = getenv('DB_USER') ?: ($db_user ?? 'root');
$pass = getenv('DB_PASS') ?: ($db_pass ?? '');

// Suppress raw fatal error on connection attempt to provide friendly redirect to installer
$mysqli = @new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    // If running in a web browser, redirect to the installer wizard
    $currentScript = basename($_SERVER['PHP_SELF'] ?? '');
    if (php_sapi_name() !== 'cli' && $currentScript !== 'install.php') {
        header('Location: install.php');
        exit;
    }
    die('Database connection error: ' . $mysqli->connect_error);
}
?>
