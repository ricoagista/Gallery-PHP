<?php
include 'config.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Debug info
echo "<!-- Admin logged in: " . $_SESSION['admin'] . " -->";

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $res = $conn->query("SELECT filename FROM images WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        unlink("uploads/" . $row['filename']);
    }
    $conn->query("DELETE FROM images WHERE id=$id");
    header("Location: admin.php?deleted");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<link rel="stylesheet" href="assets/bootstrap.min.css">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container text-center mt-5">
  <h2 class="mb-3 fw-bold">ADMIN PANEL</h2>
  <a href="logout.php" class="btn btn-outline-dark mb-4">Logout</a>

  <form action="upload.php" method="POST" enctype="multipart/form-data" class="mb-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="mb-3">
          <input type="text" name="title" class="form-control mb-3" placeholder="Judul Gambar" required>
          <input type="file" name="image" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-dark">Upload Image</button>
      </div>
    </div>
  </form>

  <div class="row justify-content-center">
    <?php
    $result = $conn->query("SELECT * FROM images ORDER BY uploaded_at DESC");
    while ($row = $result->fetch_assoc()):
    ?>
    <div class="col-md-3 col-sm-6 mb-4">
      <div class="card shadow-sm">
        <img src="uploads/<?php echo htmlspecialchars($row['filename']); ?>"
             class="card-img-top" style="height:200px; object-fit:cover;">
        <div class="card-body">
          <h5 class="card-title mb-2"><?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?></h5>
          <a href="delete.php?id=<?php echo $row['id']; ?>"
             onclick="return confirm('Yakin ingin menghapus gambar ini?')"
             class="btn btn-danger w-100">Delete</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>

</body>
</html>
