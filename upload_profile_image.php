<?php
include 'db_connect.php';
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $conn->query("DELETE FROM profile_images");
        $stmt = $conn->prepare("INSERT INTO profile_images (image_path) VALUES (?)");
        $stmt->bind_param("s", $targetFile);
        $response['success'] = $stmt->execute();
    }
}
echo json_encode($response);
?>
