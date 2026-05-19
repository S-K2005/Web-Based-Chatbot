<?php
ob_start();
include 'db_connect.php';

// --- IMAGE UPLOAD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $stream = $_POST['stream'];
    $year = $_POST['year'];
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir);

    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $conn->query("INSERT INTO images (path, stream, year) VALUES ('$targetFile', '$stream', '$year')");
        header("Location: admin_images.php?success=1");
        exit();
    } else {
        header("Location: admin_images.php?error=1");
        exit();
    }
}

// --- DELETE IMAGE ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT path FROM images WHERE id=$id");
    if ($result && $row = $result->fetch_assoc()) {
        if (file_exists($row['path'])) unlink($row['path']);
    }
    $conn->query("DELETE FROM images WHERE id=$id");
    header("Location: admin_images.php?deleted=1");
    exit();
}

// --- UPDATE IMAGE ---
if (isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    if (!empty($_FILES['update_image']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["update_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["update_image"]["tmp_name"], $targetFile)) {
            $conn->query("UPDATE images SET path='$targetFile' WHERE id=$id");
            header("Location: admin_images.php?updated=1");
            exit();
        }
    }
}

$images = $conn->query("SELECT * FROM images ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Image Manager</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #f5f6fa;
  color: #333;
  padding: 40px;
}

.container {
  max-width: 1100px;
  margin: auto;
  background: #fff;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
  color: #222;
  margin-bottom: 25px;
}

.upload-box {
  border: 2px dashed #007bff;
  border-radius: 12px;
  padding: 25px;
  text-align: center;
  margin-bottom: 30px;
  transition: 0.3s;
}
.upload-box:hover {
  background: #f8f9ff;
}

.upload-box select, input[type="file"], button {
  margin-top: 10px;
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 0.95rem;
}

.upload-btn {
  background: #007bff;
  color: white;
  border: none;
  cursor: pointer;
  transition: 0.3s;
}
.upload-btn:hover {
  background: #0056b3;
}

.message {
  text-align: center;
  font-weight: 600;
  margin-top: 10px;
}

.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.card {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: 0.3s;
  background: #fff;
}
.card:hover {
  transform: translateY(-6px);
}

.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  display: block;
  border-radius: 12px;
}

/* Hover overlay */
.card .overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.55);
  opacity: 0;
  transition: all 0.3s ease;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  border-radius: 12px;
}

.card:hover .overlay {
  opacity: 1;
}

.overlay .btn {
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
  color: #fff;
}

.btn-edit { background: #28a745; }
.btn-edit:hover { background: #218838; }

.btn-delete { background: #dc3545; }
.btn-delete:hover { background: #c82333; }

.card-info {
  text-align: center;
  padding: 8px 0;
  font-size: 0.9rem;
  font-weight: 500;
  color: #333;
}

input[type="file"].update-input {
  display: none;
}

@media (max-width: 768px) {
  .container {
    padding: 20px;
  }
}
</style>
</head>

<body>
<div class="container">
  <h2><i class="fa-solid fa-image"></i> Manage Practical Images</h2>

  <form method="POST" enctype="multipart/form-data" class="upload-box">
    <select name="stream" required>
      <option value="">Select Stream</option>
      <option value="BSC IT">BSC IT</option>
      <option value="BSC CS">BSC</option>
      <option value="DSAI">DSAI</option>
    </select>
    <select name="year" required>
      <option value="">Select Year</option>
      <option value="FY">FY</option>
      <option value="SY">SY</option>
      <option value="TY">TY</option>
    </select>
    <br><br>
    <input type="file" name="image" required>
    <br>
    <button type="submit" class="upload-btn"><i class="fa-solid fa-upload"></i> Upload</button>
  </form>

  <?php
  if (isset($_GET['success'])) echo "<p class='message' style='color:green;'>✅ Image uploaded successfully!</p>";
  if (isset($_GET['updated'])) echo "<p class='message' style='color:blue;'>✅ Image updated successfully!</p>";
  if (isset($_GET['deleted'])) echo "<p class='message' style='color:red;'>🗑️ Image deleted successfully!</p>";
  if (isset($_GET['error'])) echo "<p class='message' style='color:red;'>❌ Upload failed!</p>";
  ?>

  <div class="gallery">
    <?php if ($images && $images->num_rows > 0): ?>
      <?php while($row = $images->fetch_assoc()): ?>
        <div class="card">
          <img src="<?= htmlspecialchars($row['path']) ?>" alt="Image">
          <div class="overlay">
            <form method="POST" enctype="multipart/form-data">
              <label class="btn btn-edit">
                <i class="fa-solid fa-pen"></i> Edit
                <input type="file" name="update_image" class="update-input" onchange="this.form.submit()">
                <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
              </label>
            </form>
            <a href="?delete=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this image?')">
              <i class="fa-solid fa-trash"></i> Delete
            </a>
          </div>
          <div class="card-info">
            <?= htmlspecialchars($row['stream']) ?> — <?= htmlspecialchars($row['year']) ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; font-weight:600; color:#555;">No images uploaded yet!</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
