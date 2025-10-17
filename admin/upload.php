<?php
// admin/upload.php - Upload gambar
session_start();
require_once '../config.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Proses upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $title = trim($_POST['title'] ?? '');
    
    // Gunakan judul default jika tidak diisi
    if (empty($title)) {
        $title = "Untitled";
    }
    
    // Validasi error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Terjadi kesalahan saat upload file.';
    } else {
        // Validasi MIME type
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            $error = 'Tipe file tidak diizinkan. Hanya JPEG, PNG, dan WebP yang diperbolehkan.';
        } else {
            // Generate nama file unik untuk menghindari konflik
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('img_', true) . '.' . $extension;
            
            // Gunakan basename untuk keamanan
            $filename = basename($filename);
            $destination = UPLOAD_PATH . $filename;
            
            // Pindahkan file tanpa manipulasi (metadata tetap utuh)
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Simpan ke database
                $stmt = $conn->prepare("INSERT INTO images (filename, title, uploaded_at) VALUES (?, ?, NOW())");
                $stmt->bind_param("ss", $filename, $title);
                
                if ($stmt->execute()) {
                    $message = 'Gambar berhasil diupload!';
                } else {
                    $error = 'Gagal menyimpan ke database.';
                    // Hapus file jika gagal disimpan ke database
                    unlink($destination);
                }
                $stmt->close();
            } else {
                $error = 'Gagal memindahkan file.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Gambar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .upload-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            transition: background 0.3s;
        }
        .upload-area:hover {
            background: #f8f9fa;
        }
        .btn-upload {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-upload:hover {
            background: #5568d3;
        }
    </style>
</head>
<body style="background: #f8f9fa;">
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">📤 Upload Gambar</span>
            <div>
                <a href="dashboard.php" class="btn btn-outline-light">← Kembali ke Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="upload-container">
        <h3 class="mb-4">Upload Gambar Baru</h3>
        
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Judul Gambar</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            
            <div class="upload-area mb-4">
                <label for="image" style="cursor: pointer; width: 100%;">
                    <div>
                        <svg width="64" height="64" fill="#667eea" style="margin-bottom: 15px;">
                            <use href="#upload-icon"/>
                        </svg>
                        <p class="mb-2"><strong>Klik untuk memilih gambar</strong></p>
                        <p class="text-muted small">Format: JPEG, PNG, WebP</p>
                    </div>
                </label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" 
                       class="form-control" style="display: none;" required onchange="showFileName(this)">
            </div>
            
            <div id="file-name" class="mb-3 text-center text-muted"></div>
            
            <button type="submit" class="btn-upload w-100">Upload Gambar</button>
        </form>
    </div>

    <svg style="display: none;">
        <symbol id="upload-icon" viewBox="0 0 24 24">
            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
        </symbol>
    </svg>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showFileName(input) {
            const fileName = input.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName ? '📁 ' + fileName : '';
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>