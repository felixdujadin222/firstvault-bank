<?php
// includes/db.php
$host = getenv('DB_HOST')   ?: 'localhost';
$db   = getenv('DB_NAME')   ?: 'firstvault_bank';
$user = getenv('DB_USER')   ?: 'root';
$pass = getenv('DB_PASS')   ?: '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('Database connection error');
}
?>
