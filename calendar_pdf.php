<?php
// ---------- DATABASE CONNECTION ----------
$host = "localhost";
$user = "root";  
$pass = "";      
$db   = "chatbots"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ---------- DELETE FILE ----------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT file_path FROM pdf_files WHERE id=$id");
    if ($result && $row = $result->fetch_assoc()) {
        $path = $row['file_path'];
        if (file_exists($path)) unlink($path);
    }
    $conn->query("DELETE FROM pdf_files WHERE id=$id");
    echo "<script>alert('PDF deleted successfully!');window.location='calendar_pdf.php';</script>";
}

// ---------- UPLOAD FILE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES['pdfFile']['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    if ($fileType !== "pdf") {
        echo "<script>alert('Only PDF files are allowed.');</script>";
    } else {
        if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $targetFilePath)) {
            $fileSize = round($_FILES['pdfFile']['size'] / 1024, 2) . " KB";
            $stmt = $conn->prepare("INSERT INTO pdf_files (file_name, file_path, file_size) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fileName, $targetFilePath, $fileSize);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('PDF uploaded successfully!');window.location='calendar_pdf.php';</script>";
        } else {
            echo "<script>alert('Failed to upload PDF.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PDF Uploader and Viewer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    /* Smooth Fade + Slide Animation */
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .animate-slide {
      opacity: 0;
      animation: fadeSlideUp 0.8s ease forwards;
    }

    .animate-delay-1 { animation-delay: 0.2s; }
    .animate-delay-2 { animation-delay: 0.4s; }
    .animate-delay-3 { animation-delay: 0.6s; }

    .pdf-card {
      transition: all 0.3s ease;
    }
    .pdf-card:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .pdf-viewer-container {
      height: 70vh;
      min-height: 400px;
      width: 100%;
    }
  </style>
</head>

<body class="bg-gray-100 font-sans p-4 md:p-8">
  <div class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-2xl border border-gray-200 animate-slide">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6 border-b pb-2 flex items-center animate-delay-1 animate-slide">
      <i class="fas fa-file-pdf mr-3 text-red-500"></i> PDF Uploader and Viewer
    </h1>

    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data" class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg animate-delay-2 animate-slide">
      <label for="pdfFile" class="block text-lg font-medium text-gray-700 mb-2">
        Select PDF File to Upload:
      </label>
      <input 
        type="file" 
        id="pdfFile" 
        name="pdfFile" 
        accept=".pdf" 
        class="block w-full text-sm text-gray-900 border border-indigo-300 rounded-lg cursor-pointer bg-white p-2.5 mb-4"
        required
        onchange="previewPDF(event)"
      />
      <button 
        type="submit"
        class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-md"
      >
        <i class="fas fa-upload mr-2"></i> Upload PDF
      </button>
    </form>

    <!-- Preview -->
    <div class="mt-8 animate-delay-3 animate-slide">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-700">Preview</h2>
        <button 
          id="clearBtn"
          onclick="clearPDF()"
          class="bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 transition-all duration-200 shadow-md flex items-center disabled:bg-gray-400"
          disabled
        >
          <i class="fas fa-trash-alt mr-2"></i> Clear Preview
        </button>
      </div>
      <div id="pdfDisplayArea" class="pdf-viewer-container border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
        <p class="text-gray-500">Upload a PDF file using the 'Select File' button above.</p>
      </div>
    </div>

    <!-- Uploaded Files -->
    <div class="mt-10 border-t pt-6 animate-delay-3 animate-slide">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Uploaded PDFs:</h2>

      <?php
        $result = $conn->query("SELECT * FROM pdf_files ORDER BY uploaded_on DESC");
        if ($result->num_rows > 0) {
            echo '<div class="grid md:grid-cols-2 gap-6">';
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='pdf-card bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm animate-slide'>
                    <h3 class='font-semibold text-indigo-700 text-lg mb-2 flex items-center'>
                        <i class=\"fas fa-file-pdf mr-2 text-red-500\"></i> {$row['file_name']}
                    </h3>
                    <p class='text-sm text-gray-600 mb-2'>Size: {$row['file_size']}</p>
                    <p class='text-sm text-gray-500 mb-3'>Uploaded: {$row['uploaded_on']}</p>
                    <div class='flex gap-3'>
                        <a href='{$row['file_path']}' target='_blank' class='bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded-lg text-sm shadow-md'>
                            <i class='fas fa-eye mr-1'></i> View
                        </a>
                        <a href='?delete={$row['id']}' onclick='return confirm(\"Delete this file?\")' class='bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded-lg text-sm shadow-md'>
                            <i class='fas fa-trash-alt mr-1'></i> Delete
                        </a>
                    </div>
                </div>";
            }
            echo '</div>';
        } else {
            echo "<p class='text-gray-500'>No PDFs uploaded yet.</p>";
        }
      ?>
    </div>
  </div>

  <script>
    function previewPDF(event) {
      const file = event.target.files[0];
      const pdfDisplayArea = document.getElementById('pdfDisplayArea');
      const clearBtn = document.getElementById('clearBtn');

      if (!file || file.type !== 'application/pdf') {
        pdfDisplayArea.innerHTML = '<p class="text-red-500">Please select a valid PDF file.</p>';
        clearBtn.disabled = true;
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        pdfDisplayArea.innerHTML = `
          <iframe src="${e.target.result}" width="100%" height="100%" style="border: none;" title="Preview PDF"></iframe>
        `;
        clearBtn.disabled = false;
      };
      reader.readAsDataURL(file);
    }

    function clearPDF() {
      const pdfDisplayArea = document.getElementById('pdfDisplayArea');
      const fileInput = document.getElementById('pdfFile');
      const clearBtn = document.getElementById('clearBtn');
      fileInput.value = '';
      pdfDisplayArea.innerHTML = '<p class="text-gray-500">Upload a PDF file using the \'Select File\' button above.</p>';
      clearBtn.disabled = true;
    }
  </script>
</body>
</html>
