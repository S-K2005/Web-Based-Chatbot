<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// DB Connection
$conn = new mysqli("localhost", "root", "", "chatbots");
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$status = "";

// Step 1: Send OTP
if (isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);

    $check = $conn->prepare("SELECT * FROM user WHERE Email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $status = "❌ Email not found.";
    } else {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;

        $your_email = "seinu1307@gmail.com";
        $your_app_password = "owww zqai qres xkvg";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $your_email;
            $mail->Password = $your_app_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($your_email, 'Ramniranjhan jhunjhunwala college');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "<h3>Your OTP is: <strong>$otp</strong></h3><p>Valid for 10 minutes only.</p>";

            $mail->send();
            $status = "✅ OTP sent to $email";
            $_SESSION['otp_stage'] = "sent";
        } catch (Exception $e) {
            $status = "❌ Email sending failed.";
        }
    }
}

// Step 2: Verify OTP
if (isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp'] ?? "");
    if (isset($_SESSION['otp']) && $entered_otp === strval($_SESSION['otp'])) {
        $status = "✅ OTP Verified. Please change your password.";
        $_SESSION['otp_verified'] = true;
        $_SESSION['otp_stage'] = "verified";
    } else {
        $status = "❌ Invalid OTP. Try again.";
    }
}

// Step 3: Change Password (PLAIN 6 DIGITS)
if (isset($_POST['change_pass'])) {
    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        $status = "❌ Unauthorized access.";
    } else {
        $newpass = $_POST['password'] ?? "";
        $conf    = $_POST['confirm_password'] ?? "";

        if (!preg_match("/^[0-9]{6}$/", $newpass)) {
            $status = "❌ Password must be exactly 6 digits.";
        } elseif ($newpass !== $conf) {
            $status = "❌ Passwords do not match.";
        } else {
            $email = $_SESSION['email'];

            $update = $conn->prepare("UPDATE user SET Pass=? WHERE Email=?");
            $update->bind_param("ss", $newpass, $email);
            if ($update->execute()) {
                $status = "✅ Password changed successfully!";
                session_destroy();
            } else {
                $status = "❌ Error updating password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Reset Password</title>
<link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">


<style>

  *{
box-sizing:border-box;
 font-family: 'Inter', sans-serif;
}
   body {
      margin: 0;
      background: linear-gradient(to right, #ece9e6, #ffffff);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      animation: fadeIn 1s ease-in;
    }

.card {
    width: min(92vw, 560px);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01));
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 20px;
    padding: 28px;
    margin: 50px auto;   /* center horizontally */
    box-shadow: 0 30px 60px rgba(0, 0, 0, .35);
    backdrop-filter: blur(6px);
}

/* Mobile view adjust */
@media (max-width: 768px) {
  .card {
    margin: 20px auto;   /* thoda upar niche */
    width: 95%;          /* full screen ke kareeb */
  }
}
  .header{
    display:flex;
     align-items:center; 
    gap:12px; 
    margin-bottom:14px;
    justify-content: center; 
    align-items: center;
  }

  h1{
font-size:1.2rem;
 margin:0;
text-align: center; 
color:red;
 font-family: 'Inter', sans-serif;
}


  p.lead{
margin:6px 0 18px; 
color:var(--muted);
 font-size:.96rem
}

  .stepper{
      display:flex; 
      gap:8px; 
      margin:12px 17px 20px
}
  .chip{
    padding:8px 12px; 
    border:2px solid rgba(255,255,255,.1);
    border-radius:999px; 
    font-size:.82rem;
    color:var(--muted);
  }
  .chip.active{ 
    color:blue; 
    border-color: 
    var(--ring);
    box-shadow: 0 0 0 3px rgba(165,180,252,.15) inset;
 }

  form{ 
    display:grid; 
    gap:12px; 
    margin-top:6px

 }
 

  .input{
    width:100%;
     padding:12px 14px;
    color:black;
     margin-top: 8px;
    outline:none; 
    transition: 0.3s ease;
    transition: border .15s, box-shadow .15s, transform .1s;
      border: 2px solid #ccc;
      border-radius: 25px;
      background: #f9f9f9;
      font-size: 14px;


  }


   .input:focus {
      border-color: #407BFF;
      outline: none;
      background: #fff;
    }

  .input::placeholder{ 
   color: gray;
 }

  


  .btn{
    padding:12px 10px; 
     border-radius:25px; 
      border:1px blue;
      background: #407BFF;
      color:white; 
      font-weight:400; 
     cursor:pointer;
     transition: all 0.3s ease;
  
  }
  .btn:hover{ 
     background:#e53935;
      transform: scale(1.05);
 }
 

  .status{
    margin:8px 0 2px;
      padding:10px 12px;
     border-radius:12px; 
     font-size:.92rem;
    border:1px solid rgba(255,255,255,.1);
    background: rgba(255,255,255,.04);
  }
  .status.ok{
 border-color: rgba(34,197,94,.35);
 background: rgba(34,197,94,.10)
 }
  .status.err{
 border-color: rgba(239,68,68,.35); 
 background: rgba(239,68,68,.10)
 }

  .hint{ 
color:var(--muted);
 font-size:.8rem; 
margin-top:6px
 }

  .row{ 
display:grid; 
gap:10px 
}
  @media (min-width:520px){
    .row{ grid-template-columns: 1fr auto }
  }

  .footer-note{ 
margin-top:14px; 
color:var(--muted); 
font-size:.8rem;
 text-align:center
 }

  @keyframes fadeIn {
      from { 
                  opacity: 0; transform: translateY(20px);
               }
      to{
        opacity: 1; transform: translateY(0);
        }
    }

    .headers {
      width: 100%;
      background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
      padding: 15px 30px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
     margin-top: 0;  
     flex-wrap: wrap;
    }

    .headers h3 {
      margin: 0;
      font-size: 16px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .headers p {
      margin: 5px 0 0;
      font-size: 13px;
    }
.login-btn{
    text-decoration:none; 
    text-align:center;
     padding:12px 10px; 
     border-radius:25px; 
      border:1px blue;
      background: #407BFF;
      color:white; 
      font-weight:300; 
      font-size: 13px;
     cursor:pointer;
     transition: all 0.3s ease;
  
  }
.login-btn:hover{ 
     background:#e53935;
      transform: scale(1.05);
 }
</style>
</head>
<body>

<div class="headers">
  <div>
    <h3>Ramniranjan Jhunjhunwala College of Arts , Commerce and Science (Autonomous)</h3>
    <p>Opposite Ghatkopar Railway Station West Mumbai-400086, Maharashtra, India</p>
  </div>
</div>

<div class="card">
  <div class="header">
    <div>
      <h1>Password Reset</h1>
      <p class="lead">OTP verification & password update</p>
    </div>
  </div>

  <div class="stepper">
    <span class="chip <?php echo !isset($_SESSION['otp_stage']) ? 'active':''; ?>">Enter Email</span>
    <span class="chip <?php echo (($_SESSION['otp_stage'] ?? '')==='sent') ? 'active':''; ?>">Verify OTP</span>
    <span class="chip <?php echo (($_SESSION['otp_stage'] ?? '')==='verified') ? 'active':''; ?>">Change Password</span>
  </div>

  <?php
    $statusTrim = trim($status);
    $isOk = str_starts_with($statusTrim, '✅');
    $isErr = str_starts_with($statusTrim, '❌');
    if ($statusTrim !== '') {
      echo '<div class="status '.($isOk?'ok':($isErr?'err':'' )).'">'.$statusTrim.'</div>';
    }
  ?>

  <?php if (!isset($_SESSION['otp_stage'])): ?>
    <!-- Step 1 -->
    <form method="POST" autocomplete="off">
      <input class="input" type="email" name="email" placeholder="Enter Registered Email" >
      <button class="btn" type="submit" name="send_otp">Send</button>
    </form>

  <?php elseif (($_SESSION['otp_stage'] ?? '') === "sent"): ?>
    <!-- Step 2 -->
    <form method="POST" autocomplete="off">
      <input class="input" type="text" name="otp" pattern="[0-9]{6}" maxlength="6" placeholder="Enter OTP" >
      <button class="btn" type="submit" name="verify_otp">Verify OTP</button>
    </form>

  <?php elseif (($_SESSION['otp_stage'] ?? '') === "verified"): ?>
    <!-- Step 3 -->
    <form method="POST" autocomplete="off">
      <input class="input" type="password" name="password" pattern="[0-9]{6}" maxlength="6" placeholder="Enter 6-digit Password" >
      <input class="input" type="password" name="confirm_password" maxlength="6" placeholder="Re-enter Password" required>
      <button class="btn" type="submit" name="change_pass">Change Password</button>
      <a class="login-btn" href="l.html">Back To Login</a>
    </form>
  <?php endif; ?>

  <div class="footer-note">Tip: Never share your OTP with anyone.</div>
</div>


</body>
</html>