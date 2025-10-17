<?php
// admin/dashboard.php - Dashboard admin
session_start();
require_once '../config.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Ambil semua gambar
$sql = "SELECT * FROM images ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
        }
        .btn-delete:hover {
            background: #c82333;
        }
    </style>
</head>
<body style="background: #f8f9fa;">
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">📊 Admin Dashboard</span>
            <div>
                <a href="upload.php" class="btn btn-light me-2">Upload Gambar</a>
                <a href="../index.php" class="btn btn-outline-light me-2">Lihat Galeri</a>
                <a href="?logout=1" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="table-container">
            <h3 class="mb-4">Daftar Gambar</h3>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Nama File</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?></td>
                                    <td><?php echo htmlspecialchars($row['filename']); ?></td>
                                    <td><?php echo $row['uploaded_at']; ?></td>
                                    <td>
                                        <a href="../uploads/<?php echo urlencode($row['filename']); ?>" 
                                           class="btn btn-sm btn-info text-white me-2" target="_blank">Lihat</a>
                                        <form action="delete.php" method="POST" style="display:inline;" 
                                              onsubmit="return confirm('Yakin ingin menghapus gambar ini?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn-delete btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Belum ada gambar di galeri.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>