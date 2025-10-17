<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && isset($_POST['title'])) {
        $file = $_FILES['image'];
        $title = trim($_POST['title']);
        // Gunakan judul default jika tidak diisi
        if (empty($title)) {
            $title = "Untitled";
        }
        
        $uploadDir = "uploads/";
        $filename = basename($file['name']);
        $targetPath = $uploadDir . $filename;

        // Pastikan ekstensi aman
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            die("Format tidak didukung. Hanya JPG, JPEG, PNG.");
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Simpan ke database (nama file + judul + waktu)
            $stmt = $conn->prepare("INSERT INTO images (filename, title) VALUES (?, ?)");
            $stmt->bind_param("ss", $filename, $title);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?upload=success");
            exit;
        } else {
            echo "Gagal mengunggah file.";
        }
    }
}
?>
