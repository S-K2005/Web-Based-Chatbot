<?php
include 'db_connect.php';
$response = ['success' => false, 'image' => ''];

$q = $conn->query("SELECT image_path FROM profile_images LIMIT 1");
if ($q && $q->num_rows > 0) {
    $r = $q->fetch_assoc();
    if (!empty($r['image_path']) && file_exists($r['image_path'])) {
        $response = ['success' => true, 'image' => $r['image_path']];
    }
}
echo json_encode($response);
?>
