<?php
session_start();
include('db_connect.php');

if(!isset($_SESSION['id'])) exit("Not logged in");

$user_id = $_SESSION['id'];
$name  = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// ✅ Update existing user details
$stmt = $conn->prepare("UPDATE user SET Fullname=?, Emaill=?, Phone=? WHERE id=?");
$stmt->bind_param("sssi", $name, $email, $phone, $user_id);

if($stmt->execute()){
    // Update session values too
    $_SESSION['Fullname'] = $name;
    $_SESSION['Emaill'] = $email;
    $_SESSION['Phone'] = $phone;

    echo "✅ Profile updated successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}
?>
