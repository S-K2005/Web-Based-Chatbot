<?php
header('Content-Type: application/json');
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "chatbots");
if ($conn->connect_error) {
    die(json_encode(['answer' => 'DB Connection Failed']));
}

$input = json_decode(file_get_contents("php://input"), true);

// Set context
if (isset($input['set_context'])) {
    $_SESSION['stream'] = $input['stream'];
    $_SESSION['year'] = $input['year'];
    echo json_encode(['answer' => "Context set to {$_SESSION['stream']} - {$_SESSION['year']}"]);
    exit;
}

// Handle message
if (isset($input['message'])) {
    $msg = $conn->real_escape_string($input['message']);
    $stream = $_SESSION['stream'] ?? '';
    $year = $_SESSION['year'] ?? '';

    $sql = "SELECT answer FROM faq 
            WHERE stream='$stream' AND year='$year' 
            AND question LIKE '%$msg%' LIMIT 1";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo json_encode(['answer' => $row['answer']]);
    } else {
        echo json_encode(['answer' => "Sorry, no answer found for this stream/year."]);
    }
    exit;
}

echo json_encode(['answer' => 'Invalid request']);




?>
