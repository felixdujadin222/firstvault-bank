<?php
// includes/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// Remember-me token verification (educational challenge: md5(username.id))
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember'])) {
    $token = $_COOKIE['remember'];
    $result = $mysqli->query("SELECT * FROM users");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (md5($row['username'] . $row['id']) === $token) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                break;
            }
        }
    }
}
?>
