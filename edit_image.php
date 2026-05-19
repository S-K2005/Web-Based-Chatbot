<?php include 'db_connect.php'; ?>

<?php
$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM images WHERE id=$id");
$row = $res->fetch_assoc();

if (!$row) {
    die("Invalid ID!");
}

if (isset($_POST['update'])) {
    $image = $_FILES['image']['name'];
    $target = "upload/" . basename($image);

    // Remove old file
    if (file_exists($row['path'])) {
        unlink($row['path']);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $conn->query("UPDATE images SET path='$target' WHERE id=$id");
        header("Location: admin_images.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>Update failed!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Image</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f9f9f9;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.container {
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  text-align: center;
}
button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
}
img {
  width: 200px;
  border-radius: 10px;
  margin-bottom: 20px;
}
</style>
</head>
<body>
<div class="container">
    <h2>Edit Image</h2>
    <img src="<?php echo $row['path']; ?>" alt="Old Image">
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required><br><br>
        <button type="submit" name="update">Update Image</button>
    </form>
</div>
</body>
</html>
