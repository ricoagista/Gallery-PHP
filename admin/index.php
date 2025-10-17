<?php
// admin/index.php - Auto redirect ke login atau dashboard
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Jika belum login, redirect ke login
header('Location: login.php');
exit;
?>