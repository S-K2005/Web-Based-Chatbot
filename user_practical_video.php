<?php
include 'db_connect.php';

// --- FETCH FILTERS ---
$streamFilter = $_GET['stream'] ?? '';
$yearFilter = $_GET['year'] ?? '';

$query = "SELECT * FROM videos WHERE 1";
if ($streamFilter) $query .= " AND stream='$streamFilter'";
if ($yearFilter) $query .= " AND year='$yearFilter'";
$query .= " ORDER BY id DESC";

$videos = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student | Practical Videos</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
  color: #6A1E1E;
  padding: 30px 15px;
}

.container {
  max-width: 1200px;
  margin: auto;
  background:#fff6f3;
  padding: 40px 30px;
  border-radius: 16px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.25);
}

h2 {
  text-align: center;
  color: #8a3737;
  margin-bottom: 25px;
  font-size: 1.9rem;
}

.filter-box {
  text-align: center;
  margin-bottom: 25px;
}

select, button {
  padding: 12px 15px;
  border-radius: 10px;
  border: 1px solid #ffb7ac;
  font-size: 0.95rem;
  background: #fff0eb;
  color: #6A1E1E;
  margin: 0 5px;
  transition: 0.3s;
}

button {
  background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
  color: #6A1E1E;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

button:hover { transform: translateY(-2px); }

.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.card {
  border-radius: 14px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.25);
  background: #fff;
  overflow: hidden;
  transition: 0.3s;
  border: 1px solid #f7c4b7;
}

.card:hover { transform: translateY(-4px); }

.card video {
  width: 100%;
  height: 210px;
  object-fit: cover;
  display: block;
  border-bottom: 3px solid #ebb9b0;
  cursor: pointer;
}

.card-info {
  text-align: center;
  padding: 10px 0;
  font-size: 1rem;
  font-weight: 600;
  color: #8a3737;
}

/* Fullscreen popup */
#popup {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.88);
  justify-content: center;
  align-items: center;
  z-index: 999;
}

#popup video {
  width: 90%;
  max-width: 800px;
  border-radius: 10px;
}

#popup span {
  position: absolute;
  top: 20px; right: 30px;
  font-size: 2rem;
  color: #ffe0d1;
  cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
  .container { padding: 25px 15px; }
  select, button { width: 100%; margin: 8px 0; }
  .card video { height: 200px; }
}
</style>
</head>

<body>
<div class="container">
  <h2><i class="fa-solid fa-video"></i> Practical Videos</h2>

  <form method="GET" class="filter-box">
    <select name="stream">
      <option value="">All Streams</option>
      <option value="BSc IT" <?= $streamFilter=='BSc IT'?'selected':'' ?>>BSc IT</option>
      <option value="BCom" <?= $streamFilter=='BCom'?'selected':'' ?>>BCom</option>
      <option value="BAF" <?= $streamFilter=='BAF'?'selected':'' ?>>BAF</option>
      <option value="BBI" <?= $streamFilter=='BBI'?'selected':'' ?>>BBI</option>
      <option value="BMS" <?= $streamFilter=='BMS'?'selected':'' ?>>BMS</option>
    </select>

    <select name="year">
      <option value="">All Years</option>
      <option value="FY" <?= $yearFilter=='FY'?'selected':'' ?>>FY</option>
      <option value="SY" <?= $yearFilter=='SY'?'selected':'' ?>>SY</option>
      <option value="TY" <?= $yearFilter=='TY'?'selected':'' ?>>TY</option>
    </select>

    <button type="submit"><i class="fa-solid fa-filter"></i> Apply Filter</button>
  </form>

  <div class="gallery">
    <?php if ($videos && $videos->num_rows > 0): ?>
      <?php while($row = $videos->fetch_assoc()): ?>
        <div class="card">
          <video src="<?= htmlspecialchars($row['path']) ?>" onclick="openPopup(this.src)" muted></video>
          <div class="card-info">
            <?= htmlspecialchars($row['stream']) ?> — <?= htmlspecialchars($row['year']) ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; font-weight:600; color:#8a3737;">No videos available!</p>
    <?php endif; ?>
  </div>
</div>

<!-- Video Popup -->
<div id="popup">
  <span onclick="closePopup()">&times;</span>
  <video id="popupVideo" controls autoplay></video>
</div>

<script>
function openPopup(src) {
  document.getElementById('popupVideo').src = src;
  document.getElementById('popup').style.display = 'flex';
}
function closePopup() {
  document.getElementById('popup').style.display = 'none';
  document.getElementById('popupVideo').pause();
}
</script>

</body>
</html>
