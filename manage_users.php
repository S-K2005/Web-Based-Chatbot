<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "chatbots";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Delete User ---
if (isset($_GET['delete'])) {
    $psid = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM user WHERE psid = ?");
    $stmt->bind_param("i", $psid);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_user.php");
    exit;
}

// --- Download CSV ---
if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="user.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Full Name', 'Email', 'Phone', 'Password', 'PS ID']);
    $res = $conn->query("SELECT Fullname, Email, Phone, Pass, psid FROM user");
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// --- Fetch Users ---
$result = $conn->query("SELECT * FROM user");
$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary-indigo: #4f46e5;
    --primary-dark: #3730a3;
    --success: #10b981;
    --danger: #ef4444;
    --light-bg: #f9fafb;
    --card-bg: #ffffff;
    --text-color: #1f2937;
    --border-color: #e5e7eb;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Inter', sans-serif;
    background: var(--light-bg);
    color: var(--text-color);
    overflow-x: hidden;
}

/* --- Animations --- */
@keyframes fadeSlideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeDrop {
  from { opacity: 0; transform: translateY(-25px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes glowPulse {
  0%, 100% { box-shadow: 0 0 0px rgba(79,70,229,0.0); }
  50% { box-shadow: 0 0 20px rgba(79,70,229,0.25); }
}

/* --- Layout --- */
.main { padding: clamp(15px, 5vw, 40px); animation: fadeDrop 0.8s ease both; }
.container {
    background: var(--card-bg);
    border-radius: 16px;
    padding: clamp(20px, 5vw, 40px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border: 1px solid var(--border-color);
    animation: fadeSlideUp 1s ease both;
}
h2 {
    font-weight: 800;
    color: var(--primary-indigo);
    margin-bottom: 25px;
    border-bottom: 4px solid var(--primary-indigo);
    padding-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: fadeDrop 1.2s ease both;
}

/* --- Buttons --- */
.button-container {
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}
.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn:hover {
    transform: scale(1.05);
    animation: glowPulse 1.5s infinite ease-in-out;
}
.btn.download { background: #4b5563; color: white; }
.btn.download:hover { background: var(--primary-dark); }
#toggleBtn { background: var(--text-color); color: white; }
#toggleBtn:hover { background: #4b5563; }

/* --- Table --- */
.table-wrapper {
    overflow-x: auto;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    animation: fadeSlideUp 1.3s ease both;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 900px;
}
th, td {
    padding: 15px 20px;
    text-align: left;
}
th {
    background: var(--primary-indigo);
    color: white;
    font-weight: 700;
    position: sticky;
    top: 0;
}
tbody tr {
    transition: all 0.3s ease;
    animation: fadeSlideUp 0.6s ease both;
}
tbody tr:hover {
    background: #eef2ff;
    transform: scale(1.01);
}
tbody tr:nth-child(even){ background:#fcfdff; }

/* --- Action Buttons --- */
.action-group {
    display: flex;
    gap: 10px;
}
.btn.edit, .btn.delete {
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 6px;
}
.btn.edit { background: var(--success); color: white; }
.btn.delete { background: var(--danger); color: white; }
.btn.edit:hover { background:#059669; }
.btn.delete:hover { background:#dc2626; }

/* --- Responsive Mobile Cards --- */
@media screen and (max-width: 900px) {
    table thead { display: none; }
    table, tbody, tr, td { display: block; width: 100%; }
    tr {
        margin-bottom: 25px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: white;
        animation: fadeSlideUp 0.8s ease both;
    }
    td {
        padding: 12px 20px;
        text-align: right;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    td:before {
        content: attr(data-label);
        font-weight: 700;
        color: var(--primary-dark);
    }
    .action-group { justify-content: space-evenly; }
}
</style>
</head>
<body>

<div class="main">
<div class="container">
    <h2><i class="fa-solid fa-users-gear"></i> User Management Panel</h2>
    <div class="button-container">
        <button id="toggleBtn" class="btn"><i class="fa-solid fa-eye"></i> Show All</button>
        <a href="?download=1" class="btn download"><i class="fa-solid fa-file-csv"></i> Download CSV</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Password</th>
                    <th>PS ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTable">
            <?php
            $i = 1;
            foreach ($users as $index => $row):
            ?>
            <tr class="userRow" <?= $index >=5 ? 'style="display:none;"' : '' ?>>
                <td data-label="#"><?= $i++; ?></td>
                <td data-label="Full Name"><?= htmlspecialchars($row['Fullname']); ?></td>
                <td data-label="Email"><?= htmlspecialchars($row['Email']); ?></td>
                <td data-label="Phone"><?= htmlspecialchars($row['Phone']); ?></td>
                <td data-label="Password"><?= htmlspecialchars($row['Pass']); ?></td>
                <td data-label="PS ID"><?= htmlspecialchars($row['psid']); ?></td>
                <td data-label="Actions">
                    <div class="action-group">
                        <a href="edit_users.php?psid=<?= $row['psid']; ?>" class="btn edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                        <a href="delete_user.php?psid=<?= $row['psid']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn delete">
    <i class="fa-solid fa-trash-can"></i> Delete
</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; 
            if(count($users) == 0): ?>
            <tr><td colspan="7" style="text-align:center; padding:30px; font-weight:600;">No users found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
let showingAll = false;
const toggleBtn = document.getElementById('toggleBtn');
const totalRows = document.querySelectorAll('.userRow').length;
const initialViewLimit = 5;
if (totalRows <= initialViewLimit) toggleBtn.style.display = 'none';

toggleBtn.addEventListener('click', () => {
  showingAll = !showingAll;
  document.querySelectorAll('.userRow').forEach((row, i) => {
    if(i >= initialViewLimit) row.style.display = showingAll ? '' : 'none';
  });
  toggleBtn.innerHTML = showingAll
    ? '<i class="fa-solid fa-eye-slash"></i> Show Less'
    : '<i class="fa-solid fa-eye"></i> Show All';
});
</script>

</body>
</html>