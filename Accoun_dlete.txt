<?php
include 'db_connect.php';

$id = $_GET['id'] ?? 0;
if ($id == 0) die("Invalid ID");

$conn->query("DELETE FROM announcements WHERE id=$id");
header("Location: announcement.php");
exit();
?>
