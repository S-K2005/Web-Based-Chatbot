<?php
include('db_connect.php');
error_reporting(0);

// ✅ Delete Feedback
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM feedback WHERE id=$id");
    echo "<script>alert('🗑️ Feedback deleted successfully!');window.location='manage_feedback.php';</script>";
    exit;
}

// ✅ Fetch Feedback Data
$result = $conn->query("SELECT * FROM feedback ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Feedback</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #007bff, #00a6ff);
  margin: 0;
  padding: 0;
  min-height: 100vh;
  color: #333;
}

.container {
  max-width: 900px;
  margin: 50px auto;
  background: #fff;
  border-radius: 16px;
  padding: 30px 25px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
  color: #004aad;
  font-size: 1.8rem;
  margin-bottom: 25px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background: #007bff;
  color: white;
  text-transform: uppercase;
  font-size: 14px;
}

td {
  background: #f9f9f9;
  font-size: 15px;
  color: #333;
}

td i {
  cursor: pointer;
  color: #d11a2a;
  font-size: 18px;
  transition: 0.3s;
}

td i:hover {
  color: #a00;
}

.no-data {
  text-align: center;
  padding: 20px;
  font-style: italic;
  color: #777;
}

/* ⭐ Rating Stars */
.rating {
  color: #ffc107;
  font-size: 16px;
}

/* Responsive */
@media (max-width: 700px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  th {
    display: none;
  }
  td {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border: none;
  }
  td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #004aad;
  }
}
</style>
</head>
<body>

<div class="container">
  <h2><i class="ri-feedback-line"></i> User Feedback</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Message</th>
        <th>Rating</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td data-label="ID"><?= htmlspecialchars($row['id']) ?></td>
            <td data-label="Message"><?= htmlspecialchars($row['message']) ?></td>
            <td data-label="Rating" class="rating">
              <?= str_repeat("★", $row['rating']) ?>
            </td>
            <td data-label="Created"><?= htmlspecialchars($row['created_at']) ?></td>
            <td data-label="Delete">
              <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this feedback?');">
                <i class="ri-delete-bin-6-line"></i>
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="no-data">No feedback yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
