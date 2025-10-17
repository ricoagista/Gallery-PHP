<?php
// config.php - Konfigurasi database

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'sriwijaya');
define('DB_NAME', 'ctf_gallery');

// Koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Path untuk upload
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// Buat folder uploads jika belum ada
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Tambahkan kolom title jika belum ada
$result = $conn->query("SHOW COLUMNS FROM `images` LIKE 'title'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE `images` ADD `title` VARCHAR(255) NOT NULL DEFAULT 'Untitled' AFTER `filename`");
}
?>