<?php
include 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT * FROM pdf_files ORDER BY uploaded_on DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Academic PDFs</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<style>
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        overflow-x: hidden; /* Side scroll remove */
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
        color: #333;
        min-height: 100vh;
    }

    .wrapper {
        max-width: 900px;
        margin: 20px auto; /* Spacing perfectly fit */
        padding: 0 20px;
    }

    h1 {
        text-align: center;
        color: #6a2c24;
        margin-bottom: 30px;
        font-size: 26px;
        font-weight: 600;
        text-shadow: 0 0 6px rgba(255, 200, 180, 0.6);
    }

    .pdf-card {
        background: #fff;
        border: 1px solid rgba(120, 60, 50, 0.2);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 4px 12px rgba(120, 60, 50, 0.15);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.3s ease;
        color: #333;
    }

    .pdf-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(120, 60, 50, 0.3);
    }

    .info h3 {
        margin: 0;
        color: #8b3a2d;
        font-size: 18px;
    }

    .meta {
        font-size: 14px;
        color: #6a4a46;
    }

    .actions a {
        background: #8b3a2d;
        color: #fff;
        text-decoration: none;
        padding: 8px 14px;
        border-radius: 8px;
        margin-left: 8px;
        transition: 0.3s;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 2px 10px rgba(120, 60, 50, 0.25);
    }

    .actions a:hover {
        background: #5e241b;
        transform: scale(1.05);
    }

    .no-data {
        text-align: center;
        font-size: 16px;
        color: #8b3a2d;
        margin-top: 40px;
    }

    @media(max-width:600px) {
        .pdf-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .actions {
            margin-top: 10px;
        }
    }
</style>
</head>
<body>

<div class="wrapper">
    <h1><i class="ri-file-text-line"></i> Academic Documents</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="pdf-card">
                <div class="info">
                    <h3><?php echo htmlspecialchars($row['file_name']); ?></h3>
                    <div class="meta">
                        <i class="ri-file-2-line"></i> Size: <?php echo htmlspecialchars($row['file_size']); ?> |
                        <i class="ri-calendar-line"></i> Uploaded: <?php echo date('d M Y', strtotime($row['uploaded_on'])); ?>
                    </div>
                </div>
                <div class="actions">
                    <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">
                        <i class="ri-eye-line"></i> View
                    </a>
                    <a href="<?php echo htmlspecialchars($row['file_path']); ?>" download>
                        <i class="ri-download-2-line"></i> Download
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-data">No PDFs uploaded yet.</div>
    <?php endif; ?>
</div>

</body>
</html>
