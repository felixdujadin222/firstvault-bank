<?php
// logout.php - ends session and clears cookies
require_once __DIR__.'/includes/session.php';
// Destroy session
session_unset();
session_destroy();
// Clear remember-me and role cookies (but not securely)
setcookie('remember', '', time() - 3600, '/');
setcookie('role', '', time() - 3600, '/');
header('Location: index.php');
exit;
?>
