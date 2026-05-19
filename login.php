<?php
session_start();
include('db_connect.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM students WHERE email='$email'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_course'] = $row['course'];
            
            echo "<script>alert('Login Successful!');</script>";
            echo "<script>window.location='user.php';</script>";
        } else {
            echo "<script>alert('Incorrect Password!');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email!');</script>";
    }
}

// Forgot password form submit
if (isset($_POST['reset'])) {
    $email = $_POST['reset_email'];
    $check = "SELECT * FROM students WHERE email='$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "<script>alert('Password reset link sent to your registered email!');</script>";
    } else {
        echo "<script>alert('No account found with this email!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | College Portal</title>
  <link rel="stylesheet" href="login.css">
  <style>
    /* Popup Modal for Forgot Password */
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center; align-items: center;
    }

    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      width: 350px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .modal-content input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .close-btn {
      float: right;
      font-size: 20px;
      color: #333;
      cursor: pointer;
    }
  </style>
</head>
<body>

<header class="college-header">
  <h2>RAMNIRANJAN JHUNJHUNWALA COLLEGE OF ARTS, COMMERCE AND SCIENCE (AUTONOMOUS)</h2>
  <p>Opposite Ghatkopar Railway Station West Mumbai-400086, Maharashtra, India</p>
</header>

<div class="main-container">
  <!-- Left Login Section -->
  <div class="login-section">
    <h2>Login</h2>
    <p class="version">version 5.0.27</p>

    <form method="POST">
      <div class="input-group">
        <input type="email" name="email" placeholder="Enter Email" required>
      </div>

      <div class="input-group">
        <input type="password" name="password" placeholder="Enter Password" required>
      </div>

      <div class="input-group captcha-box">
        <div class="captcha">
          <span class="captcha-text">6407</span>
        </div>
        <input type="text" name="captcha" placeholder="Enter Captcha" required>
      </div>

      <button type="submit" name="login">Login</button>
      <p class="forgot"><a href="#" id="forgotLink">Forgot password?</a></p>
    </form>
  </div>

  <!-- Right Signup Section -->
  <div class="signup-section">
    <h2>Sign up</h2>
    <p>Students from other colleges seeking<br>admission!</p>
    <a href="user_register.php" class="signup-btn">New Registration!</a>
  </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal" id="forgotModal">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <h3>Forgot Password</h3>
    <form method="POST">
      <input type="email" name="reset_email" placeholder="Enter Registered Email" required>
      <button type="submit" name="reset">Send Reset Link</button>
    </form>
  </div>
</div>

<script>
  const modal = document.getElementById("forgotModal");
  const openBtn = document.getElementById("forgotLink");
  const closeBtn = document.getElementById("closeModal");

  openBtn.addEventListener("click", (e) => {
    e.preventDefault();
    modal.style.display = "flex";
  });

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  window.onclick = function(e) {
    if (e.target == modal) modal.style.display = "none";
  };
</script>

</body>
</html>