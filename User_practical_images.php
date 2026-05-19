<?php
include('db_connect.php');

$stream = $_GET['stream'] ?? '';
$year = $_GET['year'] ?? '';

$query = "SELECT * FROM images WHERE 1";
if (!empty($stream)) $query .= " AND stream='$stream'";
if (!empty($year)) $query .= " AND year='$year'";
$query .= " ORDER BY id DESC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery | Student Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
  margin: 0;
  padding: 30px;
  color: #6A1E1E;
}
.container {
  max-width: 1200px;
  margin: auto;
  background: #fff6f3;
  border-radius: 25px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25);
}
h2 {
  text-align: center;
  color: #8a3737;
  margin-bottom: 25px;
}
.filter-box {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 25px;
}
select, button {
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ffb7ac;
  font-size: 15px;
  background: #fff0eb;
  color: #6A1E1E;
  transition: 0.3s ease;
}
button {
  background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
  font-weight: bold;
  cursor: pointer;
  border: none;
}
button:hover { transform: translateY(-2px); opacity: 0.9; }

.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}
.card {
  background: #ffffff;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.25);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
  border: 1px solid #f7c4b7;
}
.card:hover { transform: translateY(-6px); }
.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  border-bottom: 3px solid #ebb9b0;
}
.card-info {
  text-align: center;
  padding: 10px;
  font-weight: 600;
  color: #8a3737;
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.88);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal img {
  max-width: 90%;
  max-height: 80%;
  border-radius: 15px;
  box-shadow: 0 0 20px rgba(255,255,255,0.3);
}
.modal .close {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 35px;
  cursor: pointer;
  color: #ffe0d1;
}
@media (max-width: 768px) {
  .container { padding: 20px; }
  .card img { height: 180px; }
}
</style>
</head>

<body>
<div class="container">
  <h2><i class="fa-solid fa-images"></i> Practical Gallery</h2>

  <form method="GET" class="filter-box">
    <select name="stream">
      <option value="">All Streams</option>
      <option value="BSc IT" <?= $stream=='BSc IT'?'selected':'' ?>>BSc IT</option>
      <option value="BSC CS" <?= $stream=='BSC CS'?'selected':'' ?>>BSC CS</option>
      <option value="DSAI" <?= $stream=='DSAI'?'selected':'' ?>>DSAI</option>
    </select>

    <select name="year">
      <option value="">All Years</option>
      <option value="FY" <?= $year=='FY'?'selected':'' ?>>FY</option>
      <option value="SY" <?= $year=='SY'?'selected':'' ?>>SY</option>
      <option value="TY" <?= $year=='TY'?'selected':'' ?>>TY</option>
    </select>

    <button type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
  </form>

  <div class="gallery">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="card" onclick="openModal('<?= htmlspecialchars($row['path']) ?>')">
          <img src="<?= htmlspecialchars($row['path']) ?>" alt="Image">
          <div class="card-info">
            <?= htmlspecialchars($row['stream']) ?> — <?= htmlspecialchars($row['year']) ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; font-weight:600; color:#8a3737;">No images found!</p>
    <?php endif; ?>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="imageModal">
  <span class="close" onclick="closeModal()">&times;</span>
  <img id="modalImage" src="">
</div>

<script>
function openModal(src) {
  document.getElementById('modalImage').src = src;
  document.getElementById('imageModal').style.display = 'flex';
}
function closeModal() {
  document.getElementById('imageModal').style.display = 'none';
}
window.onclick = e => { if (e.target == document.getElementById('imageModal')) closeModal(); }
</script>
</body>
</html>
