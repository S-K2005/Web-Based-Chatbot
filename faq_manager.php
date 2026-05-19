<?php 
include 'db_connect.php'; 

// --- DELETE FUNCTIONALITY ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM faq WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: faq_manager.php"); 
    exit();
}

// --- FILTER LOGIC ---
$where = [];
$stream_selected = '';
$year_selected = '';

if (!empty($_GET['stream'])) {
    $stream_selected = $conn->real_escape_string($_GET['stream']);
    $where[] = "stream = '$stream_selected'";
}
if (!empty($_GET['year'])) {
    $year_selected = $conn->real_escape_string($_GET['year']);
    $where[] = "year = '$year_selected'";
}

$sql = "SELECT * FROM faq";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
if (!$result) die("Query Failed: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Super Admin Panel - Manage FAQs</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Inter:wght@600;800&display=swap" rel="stylesheet">

<style>
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --bg-light: #f4f7fa;
    --card-bg: #ffffff;
    --text-dark: #1f2a40;
    --header-bg: #343a40;
    --header-text: #ffffff;
}

/* BASE */
body {
    font-family: 'Roboto', sans-serif;
    background: var(--bg-light);
    margin: 0;
    color: var(--text-dark);
    overflow-x: hidden;
    opacity: 0;
    transform: translateY(40px);
    animation: fadeUpPage 1s ease forwards;
}
@keyframes fadeUpPage {
    0% { opacity: 0; transform: translateY(40px); }
    100% { opacity: 1; transform: translateY(0); }
}

.container {
    width: 98%;
    max-width: 1400px;
    margin: 20px auto;
    padding: 0 15px;
}

/* HEADER */
.dashboard-header {
    background: var(--header-bg);
    color: var(--header-text);
    padding: 20px 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    animation: fadeSlideDown 1s ease;
}
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.dashboard-header h2 {
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    font-size: 1.8rem;
    margin: 0;
}

/* BUTTONS */
.btn {
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 14px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    border: none;
}
.btn-success { background: var(--success-color); color: #fff; }
.btn-success:hover { background: #1e7e34; transform: translateY(-2px); }
.btn-primary { background: var(--primary-color); color: #fff; }
.btn-primary:hover { background: #0056b3; transform: translateY(-2px); }
.btn-danger { background: var(--danger-color); color: #fff; }
.btn-danger:hover { background: #bd2130; transform: translateY(-2px); }
.btn-secondary { background: var(--secondary-color); color: #fff; }

/* FILTER SECTION */
.filter-section {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    animation: fadeSlideUp 1.1s ease;
}
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.filter-form {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: flex-end;
}
.filter-form label {
    font-weight: 500;
    color: #555;
    font-size: 0.9rem;
    margin-bottom: 5px;
}
select {
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 14px;
}
select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    outline: none;
}

/* TABLE */
.table-card {
    background: var(--card-bg);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    animation: fadeSlideUp 1.3s ease;
}
.table-wrapper {
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
    min-width: 900px;
}
thead th {
    padding: 15px 20px;
    text-align: left;
    font-size: 0.9rem;
    font-weight: 700;
    background: var(--header-bg);
    color: var(--header-text);
    text-transform: uppercase;
}
thead th:first-child { border-top-left-radius: 8px; }
thead th:last-child { border-top-right-radius: 8px; }

tbody tr {
    background: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.05);
    opacity: 0;
    transform: translateY(15px);
    animation: fadeSlide 0.6s ease forwards;
}
tbody tr:nth-child(odd) { animation-delay: 0.1s; }
tbody tr:nth-child(even) { animation-delay: 0.2s; }

@keyframes fadeSlide {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

tbody tr:hover {
    transform: scale(1.01);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

td {
    padding: 15px 20px;
    font-size: 0.9rem;
    border: none;
    vertical-align: top;
}
.action-btns {
    display: flex;
    gap: 10px;
}
.action-btns a i { font-size: 14px; }
.action-btns a span { font-weight: 500; }

/* --- Stream + Year Matching Colors --- */
.stream-bsc-it, .year-first-year {
    background-color: #ffe4ec;
    color: #e91e63;
}

.stream-bsc-cs, .year-second-year {
    background-color: #e3f2fd;
    color: #2196f3;
}

.stream-dsai, .year-third-year {
    background-color: #fff3e0;
    color: #ff9800;
}

.no-results {
    text-align: center;
    padding: 40px;
    color: var(--secondary-color);
    font-size: 1.1rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
</style>
</head>
<body>

<div class="container">
    <div class="dashboard-header">
        <h2><i class="fas fa-list-alt"></i> FAQ Management Dashboard</h2>
        <a href="faq_form.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Add New FAQ
        </a>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div>
                <label>Stream:</label>
                <select name="stream">
                    <option value="">-- All Streams --</option>
                    <option value="BSC IT" <?= ($stream_selected=="BSC IT")?'selected':'' ?>>BSC IT</option>
                    <option value="BSC CS" <?= ($stream_selected=="BSC CS")?'selected':'' ?>>BSC CS</option>
                    <option value="DSAI" <?= ($stream_selected=="DSAI")?'selected':'' ?>>DSAI</option>
                </select>
            </div>
            <div>
                <label>Year:</label>
                <select name="year">
                    <option value="">-- All Years --</option>
                    <option value="First Year" <?= ($year_selected=="First Year")?'selected':'' ?>>First Year</option>
                    <option value="Second Year" <?= ($year_selected=="Second Year")?'selected':'' ?>>Second Year</option>
                    <option value="Third Year" <?= ($year_selected=="Third Year")?'selected':'' ?>>Third Year</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filter</button>
            <?php if (!empty($stream_selected) || !empty($year_selected)): ?>
            <a href="faq_manager.php" class="btn btn-secondary"><i class="fa fa-times"></i> Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Stream</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            $stream_class = 'stream-' . strtolower(str_replace(' ', '-', $row['stream']));
                            $year_class = 'year-' . strtolower(str_replace(' ', '-', $row['year']));
                        ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars(substr($row['question'], 0, 70)) . (strlen($row['question']) > 70 ? '...' : '') ?></td>
                                <td><?= htmlspecialchars(substr($row['answer'], 0, 100)) . (strlen($row['answer']) > 100 ? '...' : '') ?></td>
                                <td><span class="badge <?= $stream_class ?>"><?= $row['stream'] ?></span></td>
                                <td><span class="badge <?= $year_class ?>"><?= $row['year'] ?></span></td>
                                <td class="action-btns">
                                    <a href="faq_form.php?id=<?= $row['id'] ?>" class="btn btn-primary" title="Edit">
                                        <i class="fa fa-edit"></i> <span>Edit</span>
                                    </a>
                                    <a href="faq_manager.php?delete=<?= $row['id'] ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Delete FAQ ID: <?= $row['id'] ?>?');">
                                        <i class="fa fa-trash"></i> <span>Delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="no-results">😔 No FAQs found matching the criteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
