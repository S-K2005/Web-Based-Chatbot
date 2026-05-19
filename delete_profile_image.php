<?php
include 'db_connect.php';
$response = ['success' => false];

$q = $conn->query("SELECT image_path FROM profile_images LIMIT 1");
if ($q && $q->num_rows > 0) {
    $r = $q->fetch_assoc();
    if (!empty($r['image_path']) && file_exists($r['image_path'])) unlink($r['image_path']);
    $conn->query("DELETE FROM profile_images");
    $response['success'] = true;
}
echo json_encode($response);
?>
