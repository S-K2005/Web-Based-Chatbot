<?php
// admin_login.php
// Self-contained admin login page + simple dashboard.
// USAGE:
// 1) Edit the DB connection settings below to match your environment.
// 2) Upload this file to your PHP server (e.g., XAMPP htdocs or a hosting account).
// 3) Visit this file in your browser (e.g., http://localhost/admin_login.php).
// This script will auto-create the `admins` table (if missing) and insert a default
// user (username: admin, password: Admin@123). Change the password immediately.

session_start();

// ----------------- CONFIG -----------------
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = ''; // set your MySQL password
$dbName = 'chatbots'; // change or create this database
$tableName = 'admins';
// ------------------------------------------

// Create connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    // If database doesn't exist, attempt to create it
    if ($conn->connect_errno) {
        die('DB Connection failed: ' . htmlspecialchars($conn->connect_error));
    }
}

// Ensure database exists (try to create if not)
$dbSelected = $conn->select_db($dbName);
if (!$dbSelected) {
    $tmp = new mysqli($dbHost, $dbUser, $dbPass);
    if ($tmp->connect_error) die('Could not connect to MySQL to create DB: ' . htmlspecialchars($tmp->connect_error));
    $created = $tmp->query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    if (!$created) die('Failed creating database: ' . htmlspecialchars($tmp->error));
    $tmp->close();
    // reconnect to the created db
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) die('DB reconnect failed: ' . htmlspecialchars($conn->connect_error));
}

// Create admins table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($createTableSQL);

// Ensure a default admin exists (only insert if table is empty)
$res = $conn->query("SELECT COUNT(*) AS cnt FROM `{$tableName}`");
$row = $res->fetch_assoc();
if (intval($row['cnt']) === 0) {
    $defaultUser = 'admin';
    // strong default password — change after first login
    $defaultPass = password_hash('Admin@123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO `{$tableName}` (username, password) VALUES (?, ?)");
    $stmt->bind_param('ss', $defaultUser, $defaultPass);
    $stmt->execute();
    $stmt->close();
    $notice = "Default admin created: username=admin password=Admin@123 — please change immediately.";
} else {
    $notice = '';
}

// ----------------- Helpers -----------------
function generate_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}

function check_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_logged_in() {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user']);
}

// ----------------- Handle POST (Login) -----------------
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    // Basic rate limiting (session-based)
    if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
    if ($_SESSION['login_attempts'] >= 10) {
        $errors[] = 'Too many attempts. Try again later.';
    } else {
        $csrf = $_POST['csrf'] ?? '';
        if (!check_csrf($csrf)) {
            $errors[] = 'Invalid form submission.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            if ($username === '' || $password === '') {
                $errors[] = 'Please enter username and password.';
            } else {
                $stmt = $conn->prepare("SELECT id, username, password FROM `{$tableName}` WHERE username = ? LIMIT 1");
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        // success
                        session_regenerate_id(true);
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_user'] = $row['username'];
                        // reset attempts
                        $_SESSION['login_attempts'] = 0;
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    } else {
                        $_SESSION['login_attempts']++;
                        $errors[] = 'Invalid username or password.';
                    }
                } else {
                    $_SESSION['login_attempts']++;
                    $errors[] = 'Invalid username or password.';
                }
                $stmt->close();
            }
        }
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$csrf_token = generate_csrf();

// ---------- Render HTML (login form or dashboard) ----------
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login</title>
  <style>
    :root{--bg:#0f1724;--card:#0b1220;--accent:#06b6d4;--muted:#94a3b8}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,'Helvetica Neue',Arial;background:linear-gradient(135deg,#071422 0%, #09212f 100%);color:#e6eef6;display:flex;align-items:center;justify-content:center;height:100vh}
    .card{background:rgba(255,255,255,0.03);padding:28px;border-radius:12px;width:360px;box-shadow:0 10px 30px rgba(2,6,23,0.6)}
    h2{margin:0 0 12px;font-size:20px}
    label{display:block;margin-top:12px;font-size:13px;color:var(--muted)}
    input[type=text],input[type=password]{width:100%;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit}
    button{margin-top:16px;width:100%;padding:10px;border-radius:8px;border:0;background:linear-gradient(90deg,var(--accent),#7c3aed);color:#041022;font-weight:600}
    .error{background:#3b0b0b;color:#ffdada;padding:8px;border-radius:6px;margin-top:12px}
    .notice{background:#0b3b2a;color:#cfffe3;padding:8px;border-radius:6px;margin-top:12px}
    .small{font-size:13px;color:var(--muted);margin-top:8px}
    .top-right{position:absolute;right:18px;top:18px;font-size:13px}
    .dashboard{width:900px;max-width:95%;height:70vh;background:rgba(255,255,255,0.02);padding:22px;border-radius:12px}
    .btn-logout{background:transparent;border:1px solid rgba(255,255,255,0.04);padding:8px 12px;border-radius:8px;color:var(--muted);cursor:pointer}
  </style>
</head>
<body>
  <?php if (is_logged_in()): ?>
    <div class="dashboard">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
        <div>
          <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_user']); ?></h2>
          <div class="small">This is a simple admin dashboard placeholder.</div>
        </div>
        <div>
          <a href="?action=logout" class="btn-logout">Log out</a>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:10px">Dashboard Card 1</div>
        <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:10px">Dashboard Card 2</div>
        <div style="background:rgba(255,255,255,0.02);padding:14px;border-radius:10px">Dashboard Card 3</div>
      </div>

      <div style="margin-top:18px;color:var(--muted)">
        Use this page as a starting point: add pages for user management, settings, logs, etc.
      </div>
    </div>
  <?php else: ?>
    <div class="card">
      <h2>Admin Login</h2>
      <?php if (!empty($notice)): ?>
        <div class="notice"><?php echo htmlspecialchars($notice); ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $e): ?>
          <div class="error"><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      <?php endif; ?>

      <form method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" value="login">

        <label for="username">Username</label>
        <input id="username" name="username" type="text" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Sign in</button>
      </form>

      <div class="small">Default username: <strong>admin</strong> | default password: <strong>Admin@123</strong></div>
      <div class="small">Tip: change the default password right away. To update the password manually, run a PHP script that uses password_hash() or change it from a secure admin panel.</div>
    </div>
  <?php endif; ?>
</body>
</html>
<?php
// Close DB connection
$conn->close();
