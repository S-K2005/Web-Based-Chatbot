<?php
include('db_connect.php');

if(isset($_POST['feedback_message']) && isset($_POST['rating'])){
    $msg = $conn->real_escape_string($_POST['feedback_message']);
    $rating = intval($_POST['rating']);

    $sql = "INSERT INTO feedback (message, rating, created_at) VALUES ('$msg', '$rating', NOW())";
    if($conn->query($sql)){
        echo "✅ Thank you for your feedback!";
    } else {
        echo "❌ Error saving feedback.";
    }
}
?>
