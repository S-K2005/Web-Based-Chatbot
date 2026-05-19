<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login | RJ College Chatbot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
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
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Header */
    .header {
      width: 100%;
      background: linear-gradient(90deg, #0052d4, #4364f7, #6fb1fc);
      padding: 20px 40px;
      color: white;
      text-align: center;
      border-bottom: 3px solid rgba(255,255,255,0.4);
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      animation: fadeInDown 1s ease-in-out;
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .header h3 {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .header p {
      font-size: 13px;
      color: #f0f0f0;
      margin: 0;
    }

    /* Container */
    .container {
      margin-top: 60px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      width: 850px;
      border-radius: 20px;
      overflow: hidden;
      display: flex;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      animation: fadeIn 1.2s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .left, .right {
      flex: 1;
      padding: 50px 40px;
    }

    .left {
      background: #ffffff;
    }

    .right {
      background: linear-gradient(180deg, #0052d4, #4364f7, #6fb1fc);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    /* Left form */
    .left h2 {
      text-align: center;
      color: #0052d4;
      margin-bottom: 25px;
      font-size: 22px;
      font-weight: 600;
    }

    .input-group {
      margin-bottom: 20px;
      position: relative;
    }

    .input-group input {
      width: 100%;
      padding: 12px 50px 12px 20px;
      border-radius: 30px;
      border: 2px solid #cbd5e1;
      background: #f9fbff;
      font-size: 14px;
      outline: none;
    }

    .input-group input:focus {
      border-color: #407bff;
      background: #fff;
      box-shadow: 0 0 6px rgba(64, 123, 255, 0.4);
    }

    .input-group i {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #555;
      cursor: pointer;
    }

    .forgot {
      text-align: right;
      font-size: 13px;
      color: #0052d4;
      cursor: pointer;
      margin-top: -10px;
      margin-bottom: 20px;
    }

    .forgot:hover {
      text-decoration: underline;
      color: #0039a6;
    }

    .login-btn {
      width: 100%;
      background: linear-gradient(90deg, #0052d4, #4364f7);
      color: white;
      border: none;
      padding: 12px;
      font-weight: 600;
      border-radius: 30px;
      cursor: pointer;
      font-size: 15px;
      box-shadow: 0 5px 15px rgba(0, 82, 212, 0.3);
    }

    .login-btn:hover {
      background: linear-gradient(90deg, #0040aa, #3455e5);
      transform: scale(1.03);
    }

    /* Right Section */
    .right h2 {
      margin-bottom: 10px;
      font-size: 22px;
      font-weight: 600;
      color: #fff;
    }

    .right p {
      font-size: 14px;
      text-align: center;
      color: #f0f8ff;
      margin-bottom: 25px;
      line-height: 1.5;
    }

    .right button {
      background: white;
      color: #0052d4;
      border: none;
      padding: 12px 25px;
      border-radius: 25px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(255,255,255,0.3);
    }

    .right button:hover {
      background: #f0f0f0;
      transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 850px) {
      .container {
        width: 90%;
        flex-direction: column;
        margin-top: 40px;
      }

      .left, .right {
        padding: 35px 25px;
      }

      .right {
        border-top: 2px solid rgba(255,255,255,0.2);
      }
    }

  </style>
</head>
<body>

  <div class="header">
    <h3>Ramniranjan Jhunjhunwala College of Arts, Commerce and Science (Autonomous)</h3>
    <p>Opposite Ghatkopar Railway Station West, Mumbai-400086, Maharashtra, India</p>
  </div>

  <div class="container">
    <div class="left">
      <h2>Admin Login</h2>
      <form method="POST" action="admin_dashboard.php" onsubmit="return validateForm()">
        <div class="input-group">
          <input type="text" name="username" id="username" placeholder=" Username" autocomplete="username">
        </div>

        <div class="input-group">
          <input type="password" name="password" id="password" placeholder=" Password" autocomplete="current-password">
          <i class="fa fa-eye" id="togglePassword"></i>
        </div>

        <div class="forgot" onclick="window.location.href='admin_forgot.php'">Forgot Password?</div>

        <button class="login-btn" name="login">Login</button>
      </form>
    </div>

    <div class="right">
      <h2>New Admin?</h2>
      <p>Register yourself to manage announcements, chatbot data, and much more.</p>
      <button onclick="window.location.href='admin_register.php'">Create Account</button>
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    togglePassword.addEventListener("click", () => {
      const isPassword = passwordInput.getAttribute("type") === "password";
      passwordInput.setAttribute("type", isPassword ? "text" : "password");
      togglePassword.classList.toggle("fa-eye-slash");
    });

    function validateForm() {
      const user = document.getElementById("username").value.trim();
      const pass = document.getElementById("password").value.trim();
      if (user === "" || pass === "") {
        alert("Please enter both username and password.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
