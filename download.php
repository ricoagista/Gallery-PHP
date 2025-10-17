<?php
// download.php - Handler untuk download gambar (metadata tetap utuh)
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID gambar tidak valid");
}

$id = intval($_GET['id']);

// Ambil info gambar dari database
$stmt = $conn->prepare("SELECT filename FROM images WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Gambar tidak ditemukan");
}

$row = $result->fetch_assoc();
$filename = basename($row['filename']); // Keamanan: hindari directory traversal
$filepath = UPLOAD_PATH . $filename;

// Cek apakah file ada
if (!file_exists($filepath)) {
    die("File tidak ditemukan");
}

// Dapatkan MIME type file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $filepath);
finfo_close($finfo);

// Set headers untuk download (METADATA TETAP UTUH)
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Baca dan kirim file (metadata tetap utuh karena tidak ada manipulasi)
readfile($filepath);

$stmt->close();
$conn->close();
exit;
?>