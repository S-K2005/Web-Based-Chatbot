<?php
include('db_connect.php');
error_reporting(0);

// ✅ Delete Question
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM questions WHERE id=$id");
    echo "<script>alert('🗑️ Question deleted successfully!');window.location='manage_questions.php';</script>";
    exit;
}

// ✅ Fetch Questions
$result = $conn->query("SELECT * FROM questions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View & Delete Questions</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background:white;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  color: #333;
}

.container {
  max-width: 900px;
  margin: 40px auto;
  background: #fff;
  border-radius: 16px;
  padding: 25px 30px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

h2 {
  text-align: center;
  color: #004aad;
  margin-bottom: 25px;
  font-size: 1.8rem;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
}

th, td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

th {
  background-color: #007bff;
  color: white;
  text-transform: uppercase;
  font-size: 14px;
}

td {
  background: #f9f9f9;
  word-wrap: break-word;
}

td i {
  cursor: pointer;
  font-size: 18px;
  transition: 0.3s;
}

.edit-icon {
  color: #007bff;
}

.edit-icon:hover {
  color: #0056b3;
}

.delete-icon {
  color: #d11a2a;
}

.delete-icon:hover {
  color: #a00;
}

.no-data {
  text-align: center;
  padding: 20px;
  color: #777;
  font-style: italic;
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
    border: none;
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background: #fdfdfd;
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
  <h2><i class="ri-question-line"></i> View & Manage Questions</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Stream</th>
        <th>Year</th>
        <th>Question</th>
        <th>Created At</th>
        <th colspan="2">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td data-label="ID"><?= htmlspecialchars($row['id']) ?></td>
            <td data-label="Stream"><?= htmlspecialchars($row['stream']) ?></td>
            <td data-label="Year"><?= htmlspecialchars($row['year']) ?></td>
            <td data-label="Question"><?= htmlspecialchars($row['question_text']) ?></td>
            <td data-label="Created"><?= htmlspecialchars($row['created_at']) ?></td>

            <!-- ✅ Edit Button Added -->
            <td data-label="Edit">
              <a href="admin_edit_question.php?id=<?= $row['id'] ?>">
                <i class="ri-edit-2-line edit-icon"></i>
              </a>
            </td>

            <td data-label="Delete">
              <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this question?');">
                <i class="ri-delete-bin-6-line delete-icon"></i>
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="no-data">No questions found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
