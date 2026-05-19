<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  if ($username && $email && $password) {
    $exists = $conn->query("SELECT * FROM admin WHERE email='$email'");
    if ($exists->num_rows > 0) {
      echo "<script>alert('Email already exists!');</script>";
    } else {
      $sql = "INSERT INTO admin (username, email, password) VALUES ('$username', '$email', '$password')";
      if ($conn->query($sql)) {
        echo "<script>alert('Registration successful! Please login now.'); window.location='admins_auth.php';</script>";
      } else {
        echo "<script>alert('Error while registering. Try again.');</script>";
      }
    }
  } else {
    echo "<script>alert('Please fill all fields!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Register | RJ College</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #e8f0ff 0%, #ffffff 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      min-height: 100vh;
      overflow-x: hidden;
    }

    header {
      width: 100%;
      background: linear-gradient(90deg, #004de6, #007bff);
      color: #fff;
      text-align: center;
      padding: 18px 12px;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 10;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    header h1 {
      font-size: clamp(16px, 2vw, 22px);
      font-weight: 700;
      margin-bottom: 4px;
    }

    header p {
      font-size: clamp(10px, 1.5vw, 13px);
      opacity: 0.95;
    }

    .container {
      margin-top: 150px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,77,230,0.2);
      padding: 45px 40px;
      width: 100%;
      max-width: 420px;
      animation: slideIn 0.8s ease-in-out;
      border: 1px solid rgba(0,77,230,0.1);
    }

    @keyframes slideIn {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    h2 {
      text-align: center;
      font-size: clamp(20px, 3vw, 26px);
      font-weight: 700;
      color: #004de6;
      margin-bottom: 25px;
      position: relative;
    }

    h2::after {
      content: '';
      position: absolute;
      width: 60px;
      height: 3px;
      background: #004de6;
      left: 50%;
      bottom: -8px;
      transform: translateX(-50%);
      border-radius: 3px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input {
      width: 100%;
      padding: 13px 15px;
      margin: 10px 0;
      border: 2px solid #ccd9ff;
      border-radius: 30px;
      background: #f9faff;
      color: #333;
      font-size: 15px;
      transition: 0.3s;
    }

    input:focus {
      border-color: #004de6;
      box-shadow: 0 0 10px rgba(0,77,230,0.2);
      background: #fff;
    }

    button {
      width: 100%;
      background: linear-gradient(90deg, #004de6, #007bff);
      border: none;
      padding: 13px;
      border-radius: 30px;
      color: white;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      margin-top: 15px;
      box-shadow: 0 6px 15px rgba(0,77,230,0.3);
      transition: 0.3s;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,77,230,0.4);
    }

    a {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #004de6;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color 0.3s;
    }

    a:hover {
      text-decoration: underline;
      color: #002a88;
    }

    /* ===== Responsive Magic ===== */
    @media (max-width: 768px) {
      .container {
        width: 85%;
        padding: 35px 25px;
        margin-top: 130px;
      }
      h2 { margin-bottom: 20px; }
      input, button { font-size: 14px; }
    }

    @media (max-width: 480px) {
      header h1 { line-height: 1.4; }
      header p { line-height: 1.3; }
      .container {
        width: 90%;
        padding: 30px 20px;
        margin-top: 120px;
      }
      input, button {
        padding: 12px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Ramniranjan Jhunjhunwala College of Arts, Commerce and Science (Autonomous)</h1>
    <p>Opp. Ghatkopar Railway Station (W), Mumbai-400086</p>
  </header>

  <div class="container">
    <h2>Admin Registration</h2>
    <form method="POST">
      <input type="text" name="username" autocomplete="off" placeholder="Username" required>
      <input type="email" name="email" autocomplete="off" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
      <a href="admins_auth.php">Already have an account? Login</a>
    </form>
  </div>
</body>
</html>
