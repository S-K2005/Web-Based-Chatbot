<?php 
include 'db_connect.php'; 

$id = 0;
$question = '';
$answer = '';
$stream = '';
$year = '';
$update = false;
$page_title = 'Add New FAQ';

// --- 1. Fetching Existing Data for Edit ---
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM faq WHERE id=$id");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $question = $row['question'];
        $answer = $row['answer'];
        $stream = $row['stream'];
        $year = $row['year'];
        $update = true;
        $page_title = 'Edit FAQ (ID: ' . $id . ')';
    }
}

// --- 2. Save/Update Logic ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question = $conn->real_escape_string($_POST['question']);
    $answer = $conn->real_escape_string($_POST['answer']);
    $stream = $conn->real_escape_string($_POST['stream']);
    $year = $conn->real_escape_string($_POST['year']);
    $id = intval($_POST['id']);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE faq SET question=?, answer=?, stream=?, year=? WHERE id=?");
        $stmt->bind_param("ssssi", $question, $answer, $stream, $year, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $result = $conn->query("SELECT MAX(id) AS max_id FROM faq");
        $row = $result->fetch_assoc();
        $next_id = (is_null($row['max_id']) ? 1 : $row['max_id'] + 1); 
        $stmt = $conn->prepare("INSERT INTO faq (id, question, answer, stream, year) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $next_id, $question, $answer, $stream, $year);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: faq_manager.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --gradient-bg: linear-gradient(135deg, #1abc9c, #16a085);
    --primary: #1abc9c;
    --primary-hover: #16a085;
    --text-dark: #2c3e50;
    --bg: #ecf0f3;
    --card-bg: rgba(255, 255, 255, 0.75);
    --shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
* {
    box-sizing: border-box;
}
body {
    font-family: 'Poppins', sans-serif;
    background: white;
    color: var(--text-dark);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
}

/* Glassmorphism Container */
.container {
    background: var(--card-bg);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 700px;
    padding: 40px;
    animation: fadeIn 0.6s ease-in-out;
    border: 1px solid rgba(255,255,255,0.3);
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

/* Header */
h2 {
    color: var(--primary);
    font-size: 1.9rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
h2 i {
    color: var(--primary);
    font-size: 1.5rem;
}

/* Form Styles */
form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
label {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
}
input, textarea, select {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid #bdc3c7;
    background: rgba(255,255,255,0.8);
    transition: all 0.3s ease;
    font-size: 15px;
}
input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 8px rgba(26, 188, 156, 0.3);
}
textarea {
    min-height: 120px;
    resize: vertical;
}

/* Smaller Dropdown Row */
.form-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.form-group.half {
    flex: 1;
    min-width: 200px;
}
select {
    font-size: 14px;
    padding: 10px 12px;
}

/* Buttons */
.btn-group {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content:center;
    margin-top: 15px;
}
.btn {
    border: none;
    border-radius: 10px;
    padding: 12px 25px;
    font-size: 15px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-primary {
    background: var(--primary);
    color: #fff;
}
.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(26, 188, 156, 0.4);
}
.btn-secondary {
    background: #95a5a6;
    color: #fff;
}
.btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
}

/* Input ID read-only */
input[readonly] {
    background: #f3f3f3;
    font-weight: 600;
    color: #7f8c8d;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding: 30px 25px;
    }
    .btn-group {
        flex-direction: column;
        align-items: stretch;

    }
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-pen-to-square"></i> <?= $page_title ?></h2>
    
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <?php if ($update): ?>
        <div class="form-group">
            <label><i class="fa-solid fa-hashtag"></i> FAQ ID</label>
            <input type="number" value="<?= htmlspecialchars($id) ?>" readonly>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label><i class="fa-solid fa-question-circle"></i> Question</label>
            <input type="text" name="question" value="<?= htmlspecialchars($question) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-lightbulb"></i> Answer</label>
            <textarea name="answer" required><?= htmlspecialchars($answer) ?></textarea>
        </div>

        <!-- Compact Stream & Year Dropdowns -->
        <div class="form-row">
            <div class="form-group half">
                <label><i class="fa-solid fa-chalkboard"></i> Stream</label>
                <select name="stream" required>
                    <option value="" disabled <?= empty($stream) ? 'selected' : '' ?>>Select Stream</option>
                    <option value="BSC IT" <?= $stream == 'BSC IT' ? 'selected' : '' ?>>BSC IT</option>
                    <option value="BSC CS" <?= $stream == 'BSC CS' ? 'selected' : '' ?>>BSC CS</option>
                    <option value="DSAI" <?= $stream == 'DSAI' ? 'selected' : '' ?>>DSAI</option>
                </select>
            </div>

            <div class="form-group half">
                <label><i class="fa-solid fa-user-graduate"></i> Year</label>
                <select name="year" required>
                    <option value="" disabled <?= empty($year) ? 'selected' : '' ?>>Select Year</option>
                    <option value="First Year" <?= $year == 'First Year' ? 'selected' : '' ?>>FY</option>
                    <option value="Second Year" <?= $year == 'Second Year' ? 'selected' : '' ?>>SY</option>
                    <option value="Third Year" <?= $year == 'Third Year' ? 'selected' : '' ?>>TY</option>
                </select>
            </div>
        </div>

        <div class="btn-group">
            <a href="faq_manager.php" class="btn btn-secondary"><i class="fa-solid fa-times"></i> Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane"></i> <?= $update ? 'Update FAQ' : 'Save FAQ' ?>
            </button>
        </div>
    </form>
</div>

</body>
</html>
