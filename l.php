<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "chatbots";

// --- Database Connection ---
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Check if form submitted properly ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- Collect form data safely ---
    $psid = isset($_POST['psid']) ? trim($_POST['psid']) : '';
    $password = isset($_POST['pass']) ? trim($_POST['pass']) : '';

    // --- Check if both fields filled ---
    if (empty($psid) || empty($password)) {
        echo "<p style='color:red;text-align:center;font-weight:600;'>⚠️ Please enter both PSID and Password.</p>";
        exit;
    }

    // --- SQL query (secure version) ---
    $stmt = $conn->prepare("SELECT * FROM user WHERE psid = ? AND Pass = ?");
    $stmt->bind_param("ss", $psid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // --- Check login success ---
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // --- Create session variables ---
        $_SESSION['fullname'] = $row['Fullname'];
        $_SESSION['email']    = $row['Email'];
        $_SESSION['psid']     = $row['psid'];

        // --- Redirect to home/dashboard ---
        header("Location: user.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center;font-weight:600;'>❌ Invalid PSID or Password.</p>";
    }

    $stmt->close();
} else {
    echo "<p style='color:red;text-align:center;font-weight:600;'>⚠️ Unauthorized Access. Please use the login form.</p>";
}

$conn->close();
?>