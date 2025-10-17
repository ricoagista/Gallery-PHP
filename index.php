<?php
// index.php - Halaman utama galeri
require_once 'config.php';

// Ambil semua gambar dari database
$stmt = $conn->query("SELECT * FROM images ORDER BY uploaded_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Gambar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            padding: 40px 0;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .gallery-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }
        .image-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .image-display {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .image-placeholder {
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }
        .card-body {
            padding: 20px;
        }
        .btn-download {
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            width: 100%;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn-download:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Galeri Gambar EXIF</h1>
        <p>Klik tombol di bawah setiap gambar untuk mengunduhnya.</p>
    </div>

    <div class="gallery-container">
        <div class="row">
            <?php 
            $colors = ['#5C6BF2', '#F25CA2', '#3DDC84', '#FF8A00', '#06B6D4', '#7C4DFF'];
            $index = 0;
            
            if ($stmt && $stmt->rowCount() > 0):
                while($row = $stmt->fetch()): 
                    $color = $colors[$index % count($colors)];
                    $index++;
                    $image_path = 'uploads/' . htmlspecialchars($row['filename']);
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="image-card">
                        <?php if (file_exists($image_path)): ?>
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="image-display">
                        <?php else: ?>
                            <div class="image-placeholder" style="background: <?php echo $color; ?>;">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title mb-3"><?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?></h5>
                            <form action="download.php" method="GET">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-download">Download</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
            ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada gambar di galeri. Silakan upload melalui panel admin.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn = null; ?>