<?php
include('db_connect.php');
error_reporting(0);

// ✅ Get ID
if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid Request');window.location='manage_questions.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// ✅ Fetch existing question
$result = $conn->query("SELECT * FROM questions WHERE id = $id");
if ($result->num_rows == 0) {
    echo "<script>alert('Question not found!');window.location='manage_questions.php';</script>";
    exit;
}

$row = $result->fetch_assoc();

// ✅ Update Question
if (isset($_POST['update'])) {
    $stream = $_POST['stream'];
    $year = $_POST['year'];
    $question_text = $_POST['question_text'];

    $update = $conn->query("UPDATE questions SET stream='$stream', year='$year', question_text='$question_text' WHERE id=$id");

    if ($update) {
        echo "<script>alert('✅ Question Updated Successfully!');window.location='manage_questions.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Failed to update. Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Question</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<style>
body{
  margin:0;
  padding:0;
  background:white;
  font-family:'Poppins',sans-serif;
}

.container{
  max-width:600px;
  margin:60px auto;
  background:#fff;
  padding:25px 30px;
  border-radius:12px;
  box-shadow:0 8px 25px rgba(0,0,0,0.15);
}

h2{
  text-align:center;
  color:#004aad;
  margin-bottom:20px;
}

label{
  font-weight:600;
  display:block;
  margin:10px 0 5px;
}

input, select, textarea{
  width:100%;
  padding:10px;
  border:1px solid #bbb;
  border-radius:8px;
  font-size:15px;
  outline:none;
}

textarea{
  height:120px;
  resize:none;
}

.btn{
  margin-top:20px;
  width:100%;
  background:#007bff;
  color:white;
  border:none;
  padding:12px;
  font-size:16px;
  border-radius:8px;
  cursor:pointer;
  transition:0.3s;
}

.btn:hover{
  background:#0056b3;
}
</style>
</head>
<body>

<div class="container">
  <h2><i class="ri-edit-line"></i> Edit Question</h2>

  <form method="POST">
    
    <label>Stream</label>
    <select name="stream" required>
      <option value="<?= $row['stream'] ?>"><?= $row['stream'] ?> (Current)</option>
      <option>BSC IT</option>
      <option>BSC CS</option>
      <option>DSAI</option>
    </select>

    <label>Year</label>
    <select name="year" required>
      <option value="<?= $row['year'] ?>"><?= $row['year'] ?> (Current)</option>
      <option>FY</option>
      <option>SY</option>
      <option>TY</option>
    </select>

    <label>Question Text</label>
    <textarea name="question_text" required><?= $row['question_text'] ?></textarea>

    <button type="submit" name="update" class="btn">Update Question</button>
  </form>
</div>

</body>
</html>
