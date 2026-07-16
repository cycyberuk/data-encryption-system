<?php
/**
 * User Logout
 * Data Encryption System
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Log activity before logout
if (isLoggedIn()) {
    logActivity($pdo, $_SESSION['user_id'], 'logout', 'User logged out');
}

// Destroy session
$_SESSION = array();
session_destroy();

// Redirect to login
header('Location: ' . SITE_URL . 'modules/auth/login.php');
exit();
?>