<?php
include('db_connect.php');

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password match check
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Check if email exists
        $check = "SELECT * FROM students WHERE email='$email'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already registered!');</script>";
        } else {
            // Save hashed password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO students (name, email, course, password) VALUES ('$name', '$email', '$course', '$hashedPassword')";
            if ($conn->query($query)) {
                echo "<script>alert('Registration Successful! You can now login.');</script>";
                echo "<script>window.location='login.php';</script>";
            } else {
                echo "<script>alert('Error: Unable to Register');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | College Portal</title>
  <link rel="stylesheet" href="register.css">
</head>
<body>

<header class="college-header">
  <h2>RAMNIRANJAN JHUNJHUNWALA COLLEGE OF ARTS, COMMERCE AND SCIENCE (AUTONOMOUS)</h2>
  <p>Opposite Ghatkopar Railway Station West Mumbai-400086, Maharashtra, India</p>
</header>

<div class="register-container">
  <div class="register-box">
    <h2>Student Registration</h2>
    <form method="POST">
      <div class="input-group">
        <input type="text" name="name" placeholder="Full Name" required>
      </div>
      <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
      </div>
      <div class="input-group">
        <input type="text" name="course" placeholder="Course" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="input-group">
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      </div>
      <button type="submit" name="register">Register</button>
      <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </form>
  </div>
</div>

</body>
</html>