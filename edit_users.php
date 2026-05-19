<?php
include 'db_connect.php';

if (!isset($_GET['psid'])) {
    die("No PS ID provided!");
}

$psid = intval($_GET['psid']);

// --- UPDATE USER ---
if (isset($_POST['update'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = $_POST['pass'];

    $stmt = $conn->prepare("UPDATE user SET Fullname=?, Email=?, Phone=?, Pass=? WHERE psid=?");
    $stmt->bind_param("ssssi", $fullname, $email, $phone, $pass, $psid);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php?updated=1");
    exit;
}

// --- FETCH USER ---
$result = $conn->query("SELECT * FROM user WHERE psid=$psid");
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: "Poppins", sans-serif;
}

body {
  background: white;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 20px;
  overflow-x: hidden;
}

.container {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  width: 100%;
  max-width: 460px;
  padding: 35px 25px;
  opacity: 0;
  transform: translateY(30px);
  animation: slideIn 0.8s ease forwards;
}

/* ✅ Animation */
@keyframes slideIn {
  0% { opacity: 0; transform: translateY(40px); }
  100% { opacity: 1; transform: translateY(0); }
}

h2 {
  text-align: center;
  color: #333;
  font-weight: 600;
  margin-bottom: 25px;
  letter-spacing: 0.5px;
}

.form-group {
  margin-bottom: 15px;
}

label {
  font-weight: 500;
  color: #555;
  display: block;
  margin-bottom: 6px;
}

input {
  width: 100%;
  padding: 11px 13px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s ease;
}

input:focus {
  border-color: #2575fc;
  outline: none;
  box-shadow: 0 0 0 3px rgba(37,117,252,0.15);
}

button {
  background: linear-gradient(90deg, #2575fc, #6a11cb);
  color: #fff;
  border: none;
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s ease;
}

button:hover {
  transform: scale(1.03);
  box-shadow: 0 6px 15px rgba(37,117,252,0.3);
}

a.back {
  display: block;
  text-align: center;
  color: #2575fc;
  text-decoration: none;
  font-size: 15px;
  margin-top: 15px;
  transition: 0.3s;
}

a.back:hover {
  text-decoration: underline;
}

/* ✅ Responsive */
@media (max-width: 480px) {
  .container { padding: 25px 18px; }
  h2 { font-size: 22px; }
  input, button { font-size: 14px; }
}
</style>

<script>
function limitPhone(input) {
  input.value = input.value.replace(/[^0-9]/g, '').slice(0, 10);
}
function limitPassword(input) {
  input.value = input.value.replace(/[^0-9]/g, '').slice(0, 6);
}
</script>

</head>

<body>

<div class="container">
  <h2><i class="fa-solid fa-user-pen"></i> Edit User</h2>
  <form method="POST">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="fullname" value="<?= htmlspecialchars($user['Fullname']) ?>" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
    </div>

    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>" required 
             maxlength="10" oninput="limitPhone(this)" placeholder="Enter 10-digit phone number">
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="text" name="pass" value="<?= htmlspecialchars($user['Pass']) ?>" required 
             maxlength="6" pattern="[0-9]{6}" oninput="limitPassword(this)" 
             placeholder="Enter 6-digit numeric password">
    </div>

    <button type="submit" name="update"><i class="fa-solid fa-save"></i> Update User</button>
  </form>

  <a href="manage_users.php" class="back"><i class="fa-solid fa-arrow-left"></i> Back to Users</a>
</div>

</body>
</html>
