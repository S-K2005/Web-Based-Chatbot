<?php
include 'db_connect.php';

// Filter values
$streamFilter = isset($_GET['stream']) ? $_GET['stream'] : '';
$yearFilter = isset($_GET['year']) ? $_GET['year'] : '';

// Query Build
$sql = "SELECT * FROM internship_uploads WHERE 1";

if ($streamFilter != "") {
    $sql .= " AND stream = '$streamFilter'";
}
if ($yearFilter != "") {
    $sql .= " AND year = '$yearFilter'";
}

$sql .= " ORDER BY id DESC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Internships</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins,sans-serif;}

body{
  background: linear-gradient(135deg, #FFBCB0, #FFE0D1);
  min-height:100vh;display:flex;justify-content:center;align-items:flex-start;
  padding:40px 20px;
}

.container{
  width:100%;max-width:1200px;background:#fff;border-radius:20px;
  padding:30px;box-shadow:0 8px 30px rgba(0,0,0,0.1);
}

h1{text-align:center;color:#d9480f;margin-bottom:25px;font-size:2rem;}

/* FILTER BAR */
.filter-box{
  display:flex;gap:15px;justify-content:center;margin-bottom:25px;
}
.filter-box select, .filter-box button{
  padding:10px 14px;border-radius:10px;border:1px solid #c9a39a;font-size:15px;
}
.filter-box button{
  background:#d9480f;color:#fff;border:none;cursor:pointer;
}
.filter-box button:hover{
  background:#7a341f;
}

.cards{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;
}

.card{
  background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 5px 16px rgba(0,0,0,0.1);
  transition:0.3s;
}
.card:hover{transform:translateY(-6px);}
.card img{
  width:100%;height:180px;object-fit:cover;cursor:pointer;
}

.details{padding:15px;}
.details h3{font-size:18px;color:#7a341f;margin-bottom:6px;}
.details p{font-size:14px;color:#444;margin:3px 0;}
.details a{color:#d9480f;font-size:14px;word-break:break-all;text-decoration:none;font-weight:600;}

/* MODAL */
#imgModal{
  display:none; position:fixed; inset:0;
  background:rgba(0,0,0,0.8); justify-content:center; align-items:center;
  z-index:9999;
}
#imgModal img{
  max-width:90%; max-height:90%; border-radius:14px;
  animation: zoom .3s ease;
}
@keyframes zoom{
  from{transform:scale(0.6);}
  to{transform:scale(1);}
}
</style>
</head>
<body>

<div class="container">
<h1><i class="fa-solid fa-briefcase"></i> Internship Opportunities</h1>

<!-- FILTER FORM -->
<form method="GET" class="filter-box">
  <select name="stream">
    <option value="">All Streams</option>
    <option value="B.Sc IT" <?php if($streamFilter=="B.Sc IT") echo "selected"; ?>>B.Sc IT</option>
    <option value="B.Com" <?php if($streamFilter=="B.Com") echo "selected"; ?>>B.Com</option>
    <option value="BBA" <?php if($streamFilter=="BBA") echo "selected"; ?>>BBA</option>
    <option value="BAF" <?php if($streamFilter=="BAF") echo "selected"; ?>>BAF</option>
    <option value="CS" <?php if($streamFilter=="CS") echo "selected"; ?>>CS</option>
  </select>

  <select name="year">
    <option value="">All Years</option>
    <option value="FY" <?php if($yearFilter=="FY") echo "selected"; ?>>FY</option>
    <option value="SY" <?php if($yearFilter=="SY") echo "selected"; ?>>SY</option>
    <option value="TY" <?php if($yearFilter=="TY") echo "selected"; ?>>TY</option>
  </select>

  <button type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
</form>

<div class="cards">
<?php
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo '
        <div class="card">
          <img src="'.$row['file_path'].'" alt="Internship Image">
          <div class="details">
            <h3>'.$row['title'].'</h3>
            <p><strong>Stream:</strong> '.$row['stream'].'</p>
            <p><strong>Year:</strong> '.$row['year'].'</p>
            <a href="'.$row['link'].'" target="_blank"><i class="fa-solid fa-link"></i> Open Link</a>
          </div>
        </div>';
    }
} else {
    echo "<p style='text-align:center;'>No internships found for selected filters.</p>";
}
?>
</div>
</div>

<!-- IMAGE VIEW MODAL -->
<div id="imgModal">
  <img id="modalImg" src="">
</div>

<script>
document.querySelectorAll(".card img").forEach(img => {
  img.addEventListener("click", function() {
    document.getElementById("modalImg").src = this.src;
    document.getElementById("imgModal").style.display = "flex";
  });
});
document.getElementById("imgModal").addEventListener("click", function() {
  this.style.display = "none";
});
</script>

</body>
</html>
