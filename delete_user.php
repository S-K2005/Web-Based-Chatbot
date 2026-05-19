<?php
include 'db_connect.php';

if (isset($_GET['psid'])) {
    $psid = intval($_GET['psid']);

    // Check if user exists before deleting
    $check = $conn->prepare("SELECT * FROM user WHERE psid = ?");
    $check->bind_param("i", $psid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Delete the user
        $delete = $conn->prepare("DELETE FROM user WHERE psid = ?");
        $delete->bind_param("i", $psid);
        $delete->execute();

        echo "
        <script>
            alert('User deleted successfully!');
            window.location.href='manage_users.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('User not found!');
            window.location.href='manage_users.php';
        </script>
        ";
    }

    $check->close();
}
else {
    echo "
    <script>
        alert('Invalid request!');
        window.location.href='manage_users.php';
    </script>
    ";
}
?>
