<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "chatbots";

// Connect
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Collect form data
$fullname = $_POST['Fullname'] ?? '';
$email = $_POST['Email'] ?? '';
$phone = $_POST['Phone'] ?? '';
$psid = $_POST['psid'] ?? '';  // FIXED HERE
$pass = $_POST['Pass'] ?? '';
$confirmPass = $_POST['ConfirmPass'] ?? '';

// Check password match
if($pass !== $confirmPass){
    echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
    exit;
}

// Check for existing phone
$checkPhone = $conn->prepare("SELECT Fullname FROM user WHERE Phone = ?");
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();
$checkPhone->store_result();
if ($checkPhone->num_rows > 0) {
    echo "<script>alert('Phone number already registered. Please use another.'); window.history.back();</script>";
    exit;
}

// If psid is empty, generate random psid
if(empty($psid)) {
    $psid = rand(100000, 999999);  // 6-digit random
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO user (Fullname, Email, Phone, Pass, psid) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $fullname, $email, $phone, $pass, $psid);

if ($stmt->execute()) {
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<style>
body {
 margin:0;
 padding:0;
 background:#FFF5EE;
 font-family: 'Segoe UI', sans-serif;
 height:100vh; 
display:flex;
 justify-content:center;
 align-items:center;
 overflow:hidden;
}

.card {
 background:#FFEFD5; 
backdrop-filter:blur(12px);
 border:2px solid rgba(255,255,255,0.2);
 border-radius:24px; 
padding:60px 50px; 
text-align:center; 
color:#fff; 
box-shadow:0 0 30px rgba(255,255,255,0.1); 
max-width:550px;
 width:90%; 
}

.emoji {
 font-size:80px;
 animation:pop 0.5s ease; 
}

h2 {
 color:green; 
font-size:32px;
 margin:20px 0 10px; 
}

p {
 font-size:18px; 
color:red;
 margin:10px 0 30px; 
}

.btn { 
display:inline-block; 
background:#407BFF; 
color:#fff; 
padding:14px 36px; 
border-radius:200px; 
font-size:18px;
 text-decoration:none;
 transition:0.3s ease; 
}

.btn:hover { 
transform:scale(1.07); 
background:red; 
}

@keyframes pop {
 0%{
transform:scale(0);
opacity:0;

} 100%{
transform:scale(1);
opacity:1;
}

 }
</style>
</head>
<body>
<div class="card">
<div class="emoji">🎉</div>
<h2>Registration Successful!</h2>
<p>Welcome to R.J.C now! 💫</p>
<a href="l.html" class="btn">🔐 Back to Login</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
confetti({ particleCount:180, spread:70, origin:{ y:0.6 } });
</script>
</body>
</html>
HTML;
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>