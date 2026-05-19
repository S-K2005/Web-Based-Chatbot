<?php
include 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Add Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stream = $_POST['stream'];
    $year = $_POST['year'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $imgPath = '';

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
        $imgPath = $targetFile;
    }

    $stmt = $conn->prepare("INSERT INTO announcements (stream, year, title, description, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $stream, $year, $title, $desc, $imgPath);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF'] . "?added=1");
    exit();
}

// ✅ Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT image_path FROM announcements WHERE id=$id");
    if ($res && $r = $res->fetch_assoc()) {
        if (!empty($r['image_path']) && file_exists($r['image_path'])) unlink($r['image_path']);
    }
    $conn->query("DELETE FROM announcements WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
    exit();
}

// ✅ Update
if (isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $title = $_POST['title'];
    $desc = $_POST['description'];

    if (!empty($_FILES['update_image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $fileName = time() . "_" . basename($_FILES["update_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["update_image"]["tmp_name"], $targetFile);
        $conn->query("UPDATE announcements SET title='$title', description='$desc', image_path='$targetFile' WHERE id=$id");
    } else {
        $conn->query("UPDATE announcements SET title='$title', description='$desc' WHERE id=$id");
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1");
    exit();
}

$result = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>College Announcements</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
body {
  margin: 0;
  font-family: "Poppins", sans-serif;
  background: linear-gradient(135deg, #e3f2fd, #f5faff);
  color: #333;
  overflow-x: hidden;
  animation: fadeIn 1.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.container {
  max-width: 1000px;
  margin: 40px auto;
  background: #fff;
  padding: 35px 30px;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  animation: zoomIn 0.8s ease;
}
@keyframes zoomIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

h2 {
  text-align: center;
  color: #007bff;
  margin-bottom: 25px;
  font-size: 2rem;
  letter-spacing: 0.5px;
  animation: slideDown 1s ease;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

form {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  justify-content: center;
  margin-bottom: 35px;
  animation: fadeInUp 1.2s ease;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

form select, form input[type="text"], textarea {
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #ccc;
  font-size: 1rem;
  width: 220px;
  transition: 0.3s;
}
form select:focus, form input:focus, textarea:focus {
  border-color: #007bff;
  outline: none;
  box-shadow: 0 0 8px rgba(0,123,255,0.25);
}
textarea {
  width: 100%;
  height: 100px;
  resize: none;
}
input[type="file"] {
  border: none;
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
  box-shadow: 0 3px 8px rgba(0,123,255,0.3);
}
.upload-btn:hover {
  background: #0056b3;
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,123,255,0.4);
}
.message {
  text-align: center;
  font-weight: 600;
  margin-bottom: 15px;
}
.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  animation: fadeInUp 1.5s ease;
}
.card {
  border-radius: 14px;
  box-shadow: 0 5px 18px rgba(0,0,0,0.1);
  background: #fff;
  overflow: hidden;
  opacity: 0;
  transform: translateY(30px);
  animation: fadeUp 0.8s ease forwards;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
.card:hover { transform: scale(1.03); transition: 0.3s; }
.card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  background: #f0f0f0;
  transition: 0.4s ease;
}
.card img:hover {
  transform: scale(1.05);
  filter: brightness(1.1);
}
.card-content {
  padding: 15px;
}
.card-content h4 {
  color: #007bff;
  font-size: 1.1rem;
  margin-bottom: 6px;
}
.card-content p {
  font-size: 0.95rem;
  color: #555;
  line-height: 1.4;
}
.card-footer {
  padding: 10px 15px;
  border-top: 1px solid #eee;
  font-size: 0.9rem;
  color: #555;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 6px;
}
.actions {
  text-align: center;
  padding: 12px 0;
}
.actions form {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 8px;
  background: #f9f9f9;
  border-radius: 10px;
}
.action-btn {
  border: none;
  padding: 9px 14px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  color: #fff;
  font-size: 0.9rem;
  margin: 3px;
  transition: 0.3s;
}
.btn-edit { background: #28a745; }
.btn-edit:hover { background: #1f8e3d; transform: scale(1.05); }
.btn-delete { background: #dc3545; }
.btn-delete:hover { background: #c82333; transform: scale(1.05); }

@media (max-width: 900px) {
  form { flex-direction: column; align-items: stretch; }
  form select, form input[type="text"], textarea { width: 100%; }
  .upload-btn { width: 100%; }
}
</style>
</head>
<body>
<div class="container">
  <h2><i class="fa-solid fa-bullhorn"></i> College Announcements</h2>

  <form method="POST" enctype="multipart/form-data">
    <select name="stream" required>
      <option value="">Select Stream</option>
      <option value="BSc IT">BSc IT</option>
      <option value="BSc CS">BSc CS</option>
      <option value="DSAI">DSAI</option>
    </select>
    <select name="year" required>
      <option value="">Select Year</option>
      <option value="FY">FY</option>
      <option value="SY">SY</option>
      <option value="TY">TY</option>
    </select>
    <input type="text" name="title" placeholder="Announcement Title" required>
    <textarea name="description" placeholder="Write details..." required></textarea>
    <input type="file" name="image" accept="image/*">
    <button type="submit" name="add" class="upload-btn"><i class="fa-solid fa-plus"></i> Post</button>
  </form>

  <?php
  if (isset($_GET['added'])) echo "<p class='message' style='color:green;'>✅ Announcement added successfully!</p>";
  if (isset($_GET['updated'])) echo "<p class='message' style='color:#007bff;'>✅ Updated successfully!</p>";
  if (isset($_GET['deleted'])) echo "<p class='message' style='color:red;'>🗑️ Deleted successfully!</p>";
  ?>

  <div class="gallery">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
          <?php if (!empty($row['image_path'])): ?>
            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="">
          <?php else: ?>
            <img src="https://via.placeholder.com/400x200?text=No+Image" alt="">
          <?php endif; ?>
          <div class="card-content">
            <h4><?= htmlspecialchars($row['title']) ?></h4>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
          </div>
          <div class="card-footer">
            <span><?= htmlspecialchars($row['stream']) ?> - <?= htmlspecialchars($row['year']) ?></span>
            <span><?= date("d M Y", strtotime($row['created_at'])) ?></span>
          </div>
          <div class="actions">
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
              <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
              <textarea name="description"><?= htmlspecialchars($row['description']) ?></textarea>
              <input type="file" name="update_image" accept="image/*">
              <button type="submit" class="action-btn btn-edit"><i class="fa-solid fa-pen"></i> Update</button>
            </form>
            <a href="?delete=<?= $row['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Delete this announcement?')"><i class="fa-solid fa-trash"></i> Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;color:#666;font-weight:600;">No announcements posted yet!</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
