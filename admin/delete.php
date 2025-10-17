<?php
// admin/delete.php - Hapus gambar
session_start();
require_once '../config.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Validasi request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_POST['id']);

// Ambil info gambar
$stmt = $conn->prepare("SELECT filename FROM images WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row) {
    $filename = basename($row['filename']);
    $filepath = UPLOAD_PATH . $filename;
    
    // Hapus dari database
    $delete_stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
    
    if ($delete_stmt->execute([$id])) {
        // Hapus file fisik
        if (file_exists($filepath)) {
            @unlink($filepath);
        }
        $_SESSION['message'] = 'Gambar berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus gambar dari database.';
    }
}

header('Location: dashboard.php');
exit;
?>