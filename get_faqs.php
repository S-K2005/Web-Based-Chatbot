<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "chatbots");
if ($conn->connect_error) {
    die(json_encode([]));
}

$stream = $_GET['stream'] ?? '';
$year = $_GET['year'] ?? '';

$sql = "SELECT question FROM faq WHERE stream='$stream' AND year='$year'";
$result = $conn->query($sql);

$faqs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faqs[] = $row;
    }
}

echo json_encode($faqs);
?>
