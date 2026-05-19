<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stream = $_POST['stream'];
    $year = $_POST['year'];
    $question_text = $_POST['question_text'];

    $stmt = $conn->prepare("INSERT INTO questions (stream, year, question_text) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $stream, $year, $question_text);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Question added successfully!');</script>";
    } else {
        echo "<script>alert('❌ Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Question</title>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    margin: 0;
    padding: 0;
}

/* ✅ Main Form Wrapper Without Box */
form {
    max-width: 750px;
    margin: 60px auto;
    padding: 0 20px;
}

/* Heading */
h2 {
    text-align: center;
    color: #6A1E1E;
    margin-bottom: 30px;
}

/* Labels */
label {
    font-weight: 600;
    color: #6A1E1E;
    display: block;
    margin-top: 12px;
}

/* Inputs */
select, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ffbcae;
    border-radius: 10px;
    margin-top: 6px;
    background: #fff5f2;
    font-size: 15px;
    color: #6A1E1E;
}

select:focus, textarea:focus {
    border-color: #e48a8a;
    outline: none;
    box-shadow: 0 0 6px rgba(255, 165, 150, 0.5);
}

/* Submit Button */
button {
    width: 100%;
    margin-top: 22px;
    padding: 12px;
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    color: #6A1E1E;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.25);
}
</style>
</head>

<body>

<h2>Add New Question</h2>

<form method="POST">
    <label>Stream</label>
    <select name="stream" required>
        <option value="">-- Select Stream --</option>
        <option value="BSc IT">BSc IT</option>
        <option value="BSc CS">BSc CS</option>
        <option value="DSAI">DSAI</option>
    </select>

    <label>Year</label>
    <select name="year" required>
        <option value="">-- Select Year --</option>
        <option value="FY">FY</option>
        <option value="SY">SY</option>
        <option value="TY">TY</option>
    </select>

    <label>Question</label>
    <textarea name="question_text" rows="4" placeholder="Write your question here..." required></textarea>

    <button type="submit">➕ Add Question</button>
</form>

</body>
</html>
