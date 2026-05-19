<?php
include 'db_connect.php';

$id = $_GET['id'] ?? 0;
if ($id == 0) die("Invalid ID");

// Fetch existing data
$result = $conn->query("SELECT * FROM announcements WHERE id=$id");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = $_POST['text'];
    $imagePath = $row['image_path'];

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE announcements SET text=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssi", $text, $imagePath, $id);
    $stmt->execute();
    header("Location: announcement.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Announcement</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<style>
body {
  background: #f8fbff;
  font-family: 'Poppins', sans-serif;
  color: #333;
  padding: 40px;
}
.container {
  background: white;
  padding: 25px;
  max-width: 600px;
  margin: auto;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
input[type="text"], input[type="file"], textarea {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
}
button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
}
button:hover {
  background-color: #0056b3;
}
</style>
</head>
<body>
<div class="container">
  <h2>Edit Announcement</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Text:</label>
    <textarea name="text" required><?= htmlspecialchars($row['text']) ?></textarea>

    <label>Replace Image (optional):</label>
    <input type="file" name="image">

    <?php if (!empty($row['image_path'])): ?>
      <p>Current Image:</p>
      <img src="<?= $row['image_path'] ?>" width="100">
    <?php endif; ?>

    <br><br>
    <button type="submit">Update</button>
  </form>
</div>
</body>
</html>
