<?php
include 'db_connect.php'; // DB connection

// Counts
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM user")->fetch_assoc()['count'] ?? 0;
$totalFaqs = $conn->query("SELECT COUNT(*) AS count FROM faq")->fetch_assoc()['count'] ?? 0;
$totalFeedback = $conn->query("SELECT COUNT(*) AS count FROM feedback")->fetch_assoc()['count'] ?? 0;
$totalInternships = $conn->query("SELECT COUNT(*) AS count FROM internship_uploads")->fetch_assoc()['count'] ?? 0;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Admin Dashboard — White Premium UI</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --primary: #3b82f6;
  --secondary: #1e40af;
  --bg: #f8fafc;
  --card-bg: #fff;
  --border: #e2e8f0;
  --text-dark: #1e293b;
  --text-light: #64748b;
  --hover: #eff6ff;
  --shadow: 0 8px 25px rgba(0,0,0,0.08);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Poppins', sans-serif;
  background:white;
  color: var(--text-dark);
  padding: 30px 20px 80px;
  min-height: 100vh;
  display: flex;
  justify-content: center;
}

.dashboard {
  width: 100%;
  max-width: 1250px;
}

/* Heading */
h1 {
  text-align: center;
  font-size: 2.3rem;
  margin-bottom: 45px;
  color: var(--primary);
  font-weight: 700;
  letter-spacing: 0.5px;
}

/* Stats Cards */
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 30px;
  margin-bottom: 50px;
}

/* Smooth animation */
@keyframes slideIn {
  0% {
    opacity: 0;
    transform: translateY(40px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 35px 25px;
  text-align: center;
  box-shadow: var(--shadow);
  cursor: pointer;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(40px);
}

/* Each card delay */
.card:nth-child(1) { animation: slideIn 0.8s ease forwards 0.1s; }
.card:nth-child(2) { animation: slideIn 0.8s ease forwards 0.3s; }
.card:nth-child(3) { animation: slideIn 0.8s ease forwards 0.5s; }
.card:nth-child(4) { animation: slideIn 0.8s ease forwards 0.7s; }

.card:hover {
  transform: translateY(-6px);
  border-color: var(--primary);
  box-shadow: 0 10px 30px rgba(59,130,246,0.2);
  background: var(--hover);
}

.card i {
  font-size: 44px;
  color: var(--primary);
  margin-bottom: 15px;
}

.card h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-light);
  margin: 8px 0;
}

.card p {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-dark);
  margin-top: 8px;
}

/* Recent Section */
.recent {
  background: var(--card-bg);
  padding: 28px;
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  animation: slideIn 0.8s ease forwards 1s;
  opacity: 0;
  transform: translateY(40px);
}

.recent h2 {
  font-size: 1.5rem;
  color: var(--primary);
  margin-bottom: 22px;
  display: flex;
  align-items: center;
  gap: 10px;
}

/* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

th, td {
  padding: 15px 12px;
  text-align: left;
  border-bottom: 1px solid var(--border);
}

th {
  color: var(--secondary);
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 0.5px;
}

td a {
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
}

td a:hover {
  text-decoration: underline;
  color: var(--secondary);
}

tr:hover {
  background: var(--hover);
  transition: 0.3s ease;
}

.no-data {
  text-align: center;
  color: var(--text-light);
  padding: 18px 0;
}

/* Responsive */
@media (max-width: 768px) {
  body { padding: 15px; }
  h1 { font-size: 1.9rem; }
  .card p { font-size: 1.6rem; }
  .recent h2 { font-size: 1.2rem; }
}
</style>
</head>

<body>
<div class="dashboard">
  <h1><i class="fa-solid fa-gauge-high"></i> Admin Dashboard</h1>

  <div class="stats">
    <div class="card" onclick="window.location.href='manage_users.php'">
      <i class="fa-solid fa-users"></i>
      <h3>Total Users</h3>
      <p><?php echo $totalUsers; ?></p>
    </div>

    <div class="card" onclick="window.location.href='faq_manager.php'">
      <i class="fa-solid fa-question-circle"></i>
      <h3>Total FAQs</h3>
      <p><?php echo $totalFaqs; ?></p>
    </div>

    <div class="card" onclick="window.location.href='internship.php'">
      <i class="fa-solid fa-briefcase"></i>
      <h3>Internships</h3>
      <p><?php echo $totalInternships; ?></p>
    </div>

    <div class="card" onclick="window.location.href='manage_feedback.php'">
      <i class="fa-solid fa-comments"></i>
      <h3>Feedbacks</h3>
      <p><?php echo $totalFeedback; ?></p>
    </div>
  </div>

  <div class="recent">
    <h2><i class="fa-solid fa-clock-rotate-left"></i> Recent Internships</h2>
    <table>
      <tr>
        <th>Title</th>
        <th>Stream</th>
        <th>Year</th>
        <th>Link</th>
      </tr>
      <?php
      $recent = $conn->query("SELECT title, stream, year, link FROM internship_uploads ORDER BY id DESC LIMIT 5");
      if ($recent && $recent->num_rows > 0) {
          while ($r = $recent->fetch_assoc()) {
              echo "<tr>
                      <td>{$r['title']}</td>
                      <td>{$r['stream']}</td>
                      <td>{$r['year']}</td>
                      <td><a href='{$r['link']}' target='_blank'>View</a></td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='4' class='no-data'>No recent internships found.</td></tr>";
      }
      ?>
    </table>
  </div>
</div>
</body>
</html>
