<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $new_pass = $_POST['new_pass'] ?? '';

    if (!empty($email) && !empty($new_pass)) {
        $check = $conn->query("SELECT * FROM admin WHERE email='$email'");
        if ($check && $check->num_rows > 0) {
            $update = $conn->query("UPDATE admin SET password='$new_pass' WHERE email='$email'");
            if ($update) {
                echo "<script>alert('Password updated successfully! You can now log in.'); window.location.href='admins_auth.php';</script>";
            } else {
                echo "<script>alert('Something went wrong. Try again!');</script>";
            }
        } else {
            echo "<script>alert('Email not found!');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all fields!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password | RJ College Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
    }

    body {
      margin: 0;
      background: linear-gradient(135deg, #e8f0ff, #ffffff);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .container {
      width: 400px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 20px;
      padding: 40px 35px;
      box-shadow: 0 10px 25px rgba(0, 82, 212, 0.2);
      text-align: center;
      animation: fadeIn 0.8s ease-in;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    h2 {
      color: #0052d4;
      font-weight: 600;
      margin-bottom: 20px;
    }

    p {
      font-size: 14px;
      color: #555;
      margin-bottom: 30px;
    }

    input {
      width: 100%;
      padding: 12px;
      border-radius: 30px;
      border: 2px solid #cbd5e1;
      background: #f9fbff;
      margin-bottom: 15px;
      outline: none;
      font-size: 14px;
    }

    input:focus {
      border-color: #407bff;
      box-shadow: 0 0 8px rgba(64, 123, 255, 0.3);
      background: #fff;
    }

    button {
      width: 100%;
      background: linear-gradient(90deg, #0052d4, #4364f7);
      color: white;
      border: none;
      padding: 12px;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(0,82,212,0.25);
      font-size: 15px;
    }

    button:hover {
      background: linear-gradient(90deg, #0040aa, #3455e5);
      transform: scale(1.03);
    }

    a {
      display: block;
      margin-top: 15px;
      color: #0052d4;
      text-decoration: none;
      font-weight: 500;
      font-size: 14px;
    }

    a:hover {
      text-decoration: underline;
      color: #0039a6;
    }

    @media (max-width: 450px) {
      .container {
        width: 90%;
        padding: 30px 25px;
      }
      h2 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Forgot Password</h2>
    <p>Enter your registered email and your new password below.</p>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your Email" required>
      <input type="password" name="new_pass" placeholder="Enter New Password" required>
      <button type="submit">Reset Password</button>
      <a href="admins_auth.php">Back to Login</a>
    </form>
  </div>

</body>
</html>
