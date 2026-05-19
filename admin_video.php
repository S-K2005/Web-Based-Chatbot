<?php
ob_start();
include 'db_connect.php';

// --- VIDEO UPLOAD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $stream = $_POST['stream'];
    $year = $_POST['year'];
    $targetDir = "videos/";
    if (!is_dir($targetDir)) mkdir($targetDir);

    $fileName = time() . "_" . basename($_FILES["video"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetFile)) {
        $conn->query("INSERT INTO videos (path, stream, year) VALUES ('$targetFile', '$stream', '$year')");
        header("Location: admin_video.php?success=1");
        exit();
    } else {
        header("Location: admin_video.php?error=1");
        exit();
    }
}

// --- DELETE VIDEO ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT path FROM videos WHERE id=$id");
    if ($result && $row = $result->fetch_assoc()) {
        if (file_exists($row['path'])) unlink($row['path']);
    }
    $conn->query("DELETE FROM videos WHERE id=$id");
    header("Location: admin_video.php?deleted=1");
    exit();
}

// --- UPDATE VIDEO ---
if (isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    if (!empty($_FILES['update_video']['name'])) {
        $targetDir = "videos/";
        $fileName = time() . "_" . basename($_FILES["update_video"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["update_video"]["tmp_name"], $targetFile)) {
            $conn->query("UPDATE videos SET path='$targetFile' WHERE id=$id");
            header("Location: admin_video.php?updated=1");
            exit();
        }
    }
}

$videos = $conn->query("SELECT * FROM videos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Video Manager</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #eef1f6;
  color: #333;
  padding: 30px 15px;
}

.container {
  max-width: 1200px;
  margin: auto;
  background: #fff;
  padding: 40px 30px;
  border-radius: 16px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.08);
}

h2 {
  text-align: center;
  color: #222;
  margin-bottom: 25px;
  font-size: 1.9rem;
  letter-spacing: 0.5px;
}

.upload-box {
  border: 2px dashed #007bff;
  border-radius: 14px;
  padding: 25px;
  text-align: center;
  margin-bottom: 35px;
  background: #fafbff;
  transition: 0.3s ease;
}
.upload-box:hover {
  background: #f5f7ff;
}

.upload-box select, input[type="file"], button {
  margin: 10px 5px;
  padding: 12px 15px;
  border-radius: 10px;
  border: 1px solid #ccc;
  font-size: 0.95rem;
  width: 180px;
  transition: 0.3s;
}

.upload-btn {
  background: #007bff;
  color: white;
  border: none;
  cursor: pointer;
  padding: 12px 25px;
  font-weight: 600;
  border-radius: 10px;
  transition: 0.3s;
}
.upload-btn:hover {
  background: #0056b3;
  transform: translateY(-2px);
}

.message {
  text-align: center;
  font-weight: 600;
  margin-top: 10px;
  font-size: 1rem;
}

.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.card {
  border-radius: 14px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  background: #fff;
  overflow: hidden;
  transition: 0.3s ease;
}
.card:hover {
  transform: translateY(-4px);
}

.card video {
  width: 100%;
  height: 210px;
  object-fit: cover;
  display: block;
}

.card-info {
  text-align: center;
  padding: 10px 0;
  font-size: 1rem;
  font-weight: 600;
  color: #444;
  border-top: 1px solid #eee;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 12px;
  padding: 10px 0 15px;
}

.action-buttons form,
.action-buttons a {
  display: inline-block;
}

.action-btn {
  border: none;
  padding: 9px 14px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
  color: #fff;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-edit { background: #28a745; }
.btn-edit:hover { background: #218838; }

.btn-delete { background: #dc3545; }
.btn-delete:hover { background: #c82333; }

input[type="file"].update-input {
  display: none;
}

/* Responsive tweaks */
@media (max-width: 768px) {
  .container {
    padding: 25px 15px;
  }
  .upload-box {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .upload-box select, input[type="file"], button {
    width: 100%;
    margin: 8px 0;
  }
  .card video {
    height: 200px;
  }
}

@media (max-width: 480px) {
  h2 {
    font-size: 1.5rem;
  }
  .upload-btn {
    width: 100%;
  }
  .card-info {
    font-size: 0.9rem;
  }
}
</style>
</head>

<body>
<div class="container">
  <h2><i class="fa-solid fa-video"></i> Manage Practical Videos</h2>

  <form method="POST" enctype="multipart/form-data" class="upload-box">
    <div>
      <select name="stream" required>
        <option value="">Select Stream</option>
        <option value="BSC IT">BSC IT</option>
        <option value="BSC CS">BSC CS</option>
        <option value="DSAI">DSAI</option>
      </select>
      <select name="year" required>
        <option value="">Select Year</option>
        <option value="FY">FY</option>
        <option value="SY">SY</option>
        <option value="TY">TY</option>
      </select>
    </div>
    <input type="file" name="video" accept="video/*" required>
    <button type="submit" class="upload-btn"><i class="fa-solid fa-upload"></i> Upload Video</button>
  </form>

  <?php
  if (isset($_GET['success'])) echo "<p class='message' style='color:green;'>✅ Video uploaded successfully!</p>";
  if (isset($_GET['updated'])) echo "<p class='message' style='color:#007bff;'>✅ Video updated successfully!</p>";
  if (isset($_GET['deleted'])) echo "<p class='message' style='color:red;'>🗑️ Video deleted successfully!</p>";
  if (isset($_GET['error'])) echo "<p class='message' style='color:red;'>❌ Upload failed!</p>";
  ?>

  <div class="gallery">
    <?php if ($videos && $videos->num_rows > 0): ?>
      <?php while($row = $videos->fetch_assoc()): ?>
        <div class="card">
          <video src="<?= htmlspecialchars($row['path']) ?>" controls></video>
          <div class="card-info">
            <?= htmlspecialchars($row['stream']) ?> — <?= htmlspecialchars($row['year']) ?>
          </div>
          <div class="action-buttons">
            <form method="POST" enctype="multipart/form-data">
              <label class="action-btn btn-edit">
                <i class="fa-solid fa-pen"></i> Edit
                <input type="file" name="update_video" accept="video/*" class="update-input" onchange="this.form.submit()">
                <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
              </label>
            </form>
            <a href="?delete=<?= $row['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Delete this video?')">
              <i class="fa-solid fa-trash"></i> Delete
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; font-weight:600; color:#555;">No videos uploaded yet!</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

<?php ob_end_flush(); ?>
