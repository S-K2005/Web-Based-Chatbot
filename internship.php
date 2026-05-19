<?php
include 'db_connect.php';

// ---------- DELETE FUNCTION ----------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "SELECT file_path FROM internship_uploads WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $row['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $conn->query("DELETE FROM internship_uploads WHERE id = $id");
    }
    header("Location: internship.php");
    exit();
}

// ---------- UPLOAD FUNCTION ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $link = $conn->real_escape_string($_POST['link']);
    $stream = $conn->real_escape_string($_POST['stream']);
    $year = $conn->real_escape_string($_POST['year']);
    $targetDir = "uploads/";

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES['image']['name']);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        $conn->query("INSERT INTO internship_uploads (title, link, stream, year, file_path) 
                      VALUES ('$title', '$link', '$stream', '$year', '$targetFilePath')");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Internship Upload Manager</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<style>
/* ===== GLOBAL ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

body {
  background: linear-gradient(135deg, #eef2ff, #dbeafe);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 40px 20px;
}

/* ===== CONTAINER ===== */
.container {
  width: 100%;
  max-width: 1200px;
  background: #fff;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  animation: fadeIn 0.8s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(25px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== HEADER ===== */
h1 {
  text-align: center;
  color: #2563eb;
  font-size: 2.2rem;
  margin-bottom: 25px;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
}
h1 i {
  color: #1e40af;
}

/* ===== FORM ===== */
form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 18px;
  margin-bottom: 50px;
  background: #f9fafb;
  padding: 25px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  animation: slideIn 0.8s ease forwards;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

input[type="text"],
input[type="url"],
input[type="file"],
select {
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 15px;
  outline: none;
  transition: all 0.3s ease;
}
input:focus, select:focus {
  border-color: #2563eb;
  box-shadow: 0 0 6px rgba(37, 99, 235, 0.3);
}

button {
  grid-column: 1 / -1;
  background: linear-gradient(135deg, #2563eb, #1e3a8a);
  color: #fff;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
}
button:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 18px rgba(37, 99, 235, 0.3);
}

/* ===== CARDS ===== */
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 30px;
}

.card {
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
  position: relative;
  transform: scale(0.98);
  opacity: 0;
  animation: cardPop 0.7s ease forwards;
}

@keyframes cardPop {
  to { transform: scale(1); opacity: 1; }
}

.card:hover {
  transform: translateY(-8px) scale(1.02);
  transition: 0.3s ease;
}

.card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-bottom: 1px solid #e5e7eb;
}

/* ===== OVERLAY ===== */
.overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  opacity: 0;
  transition: all 0.3s ease;
}
.card:hover .overlay {
  opacity: 1;
}

.overlay a {
  color: white;
  background: rgba(37, 99, 235, 0.9);
  padding: 10px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 15px;
  transition: 0.3s;
}
.overlay a.delete {
  background: rgba(220, 38, 38, 0.9);
}
.overlay a:hover {
  transform: scale(1.1);
}

/* ===== DETAILS ===== */
.details {
  padding: 15px;
}
.details h3 {
  font-size: 18px;
  color: #1e3a8a;
  margin-bottom: 6px;
}
.details p {
  font-size: 14px;
  color: #555;
  margin: 3px 0;
}
.details a {
  color: #2563eb;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  word-break: break-all;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  form {
    grid-template-columns: 1fr;
  }
  h1 {
    font-size: 1.6rem;
  }
}
</style>
</head>
<body>

<div class="container">
  <h1><i class="fa-solid fa-briefcase"></i> Internship Upload Manager</h1>

  <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Internship Title" required>
    <input type="url" name="link" placeholder="Internship Link (e.g. https://...)" required>
    <select name="stream" required>
      <option value="">Select Stream</option>
      <option value="BSC IT">BSC IT</option>
      <option value="BSC CS">BSC CS</option>
      <option value="DSAI">DSAI</option>
    </select>
    <select name="year" required>
      <option value="">Select Year</option>
      <option value="FY">First Year</option>
      <option value="SY">Second Year</option>
      <option value="TY">Third Year</option>
    </select>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit"><i class="fa-solid fa-upload"></i> Upload Internship</button>
  </form>

  <div class="cards">
    <?php
    $res = $conn->query("SELECT * FROM internship_uploads ORDER BY id DESC");
    if ($res && $res->num_rows > 0) {
        $delay = 0;
        while ($row = $res->fetch_assoc()) {
            echo '
            <div class="card" style="animation-delay: '.$delay.'s;">
              <img src="'.$row['file_path'].'" alt="Internship Image">
              <div class="overlay">
                <a href="?delete='.$row['id'].'" class="delete"><i class="fa-solid fa-trash"></i></a>
                <a href="'.$row['link'].'" target="_blank"><i class="fa-solid fa-link"></i></a>
              </div>
              <div class="details">
                <h3>'.$row['title'].'</h3>
                <p><strong>Stream:</strong> '.$row['stream'].'</p>
                <p><strong>Year:</strong> '.$row['year'].'</p>
                <a href="'.$row['link'].'" target="_blank">'.$row['link'].'</a>
              </div>
            </div>';
            $delay += 0.1;
        }
    } else {
        echo "<p>No internships uploaded yet.</p>";
    }
    ?>
  </div>
</div>

</body>
</html>
