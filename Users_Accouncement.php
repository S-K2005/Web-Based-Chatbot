<?php
include 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);

$announcements = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
            color: #6A1E1E;
            min-height: 100vh;
        }

        /* 🔍 Filter Box */
        .filter-box {
            background: rgba(255, 240, 236, 0.75);
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            width: 90%;
            max-width: 1000px;
            margin: 30px auto 20px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        select {
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid #ffb7ac;
            font-size: 15px;
            outline: none;
            background: #fff5f2;
            color: #6A1E1E;
            transition: 0.3s ease;
        }

        select:hover {
            border-color: #d86b6b;
        }

        /* 📢 Announcement Cards */
        .container {
            max-width: 1100px;
            margin: 20px auto 50px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .card {
            background: #fff6f3;
            border-radius: 18px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.25);
            overflow: hidden;
            transition: 0.3s ease;
            border: 1px solid #f7c4b7;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 22px rgba(0,0,0,0.35);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
        }

        .card-content {
            padding: 18px;
        }

        .card-content h3 {
            font-size: 18px;
            color: #8a3737;
            margin-bottom: 8px;
        }

        .meta {
            font-size: 13px;
            color: #b45a5a;
            margin-bottom: 10px;
        }

        .desc {
            font-size: 14px;
            color: #6A1E1E;
            line-height: 1.6;
        }

        /* 🖼️ Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
        }

        .modal img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 12px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .close {
            position: fixed;
            top: 18px;
            right: 25px;
            color: #fff;
            font-size: 34px;
            cursor: pointer;
            background: rgba(0,0,0,0.55);
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @media(max-width: 500px) {
            .filter-box { flex-direction: column; }
            select { width: 80%; }
        }
    </style>
</head>

<body>
    <!-- 🔍 Filter Box -->
    <div class="filter-box">
        <select id="streamFilter">
            <option value="">Select Stream</option>
            <option value="BSC IT">BSC IT</option>
            <option value="BSC CS">BSC CS</option>
            <option value="DSAI">DSAI</option>
        </select>

        <select id="yearFilter">
            <option value="">Select Year</option>
            <option value="FY">FY</option>
            <option value="SY">SY</option>
            <option value="TY">TY</option>
        </select>
    </div>

    <!-- 📢 Announcement Cards -->
    <div class="container" id="announcementContainer">
        <?php if (count($announcements) > 0): ?>
            <?php foreach ($announcements as $row): ?>
                <div class="card"
                     data-stream="<?php echo $row['stream']; ?>"
                     data-year="<?php echo $row['year']; ?>">

                    <img src="<?php echo !empty($row['image_path']) ? $row['image_path'] : 'https://via.placeholder.com/400x200/ecc4ba/ffffff?text=No+Image'; ?>" class="popup-img">

                    <div class="card-content">
                        <h3><?php echo $row['title']; ?></h3>
                        <div class="meta">
                            <?php echo $row['stream']; ?> |
                            <?php echo $row['year']; ?> |
                            <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                        </div>
                        <p class="desc"><?php echo $row['description']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;opacity:0.6;color:#6A1E1E;">No announcements found.</p>
        <?php endif; ?>
    </div>

    <!-- 🖼️ Modal -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img id="modalImage" src="">
    </div>

    <script>
        const streamFilter = document.getElementById('streamFilter');
        const yearFilter = document.getElementById('yearFilter');
        const cards = document.querySelectorAll('.card');

        function filterAnnouncements() {
            const stream = streamFilter.value.toLowerCase();
            const year = yearFilter.value.toLowerCase();

            cards.forEach(card => {
                const match = 
                    (stream === '' || card.dataset.stream.toLowerCase() === stream) &&
                    (year === '' || card.dataset.year.toLowerCase() === year);

                card.style.display = match ? 'block' : 'none';
            });
        }

        streamFilter.addEventListener('change', filterAnnouncements);
        yearFilter.addEventListener('change', filterAnnouncements);

        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.querySelector(".close");

        document.querySelectorAll(".popup-img").forEach(img => {
            img.addEventListener("click", () => {
                modal.style.display = "flex";
                modalImg.src = img.src;
            });
        });

        closeBtn.onclick = () => modal.style.display = "none";
        modal.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };
    </script>
</body>
</html>
