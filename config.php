<?php
// config.php - Konfigurasi database SQLite

// Path untuk database dan uploads
define('DB_PATH', __DIR__ . '/data/gallery.sqlite');
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// Buat folder data jika belum ada
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Buat folder uploads jika belum ada
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Koneksi database SQLite
try {
    $conn = new PDO('sqlite:' . DB_PATH);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Enable foreign keys
    $conn->exec('PRAGMA foreign_keys = ON');
    
    // Inisialisasi tabel jika belum ada
    $conn->exec("
        CREATE TABLE IF NOT EXISTS images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            filename TEXT NOT NULL,
            title TEXT NOT NULL DEFAULT 'Untitled',
            uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>