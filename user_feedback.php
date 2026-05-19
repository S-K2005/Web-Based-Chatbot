<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Feedback</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body {
  font-family: "Poppins", sans-serif;
  background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* ✅ Bigger, Centered, Responsive Container */
.feedback-container {
  background: #fff6f3;
  padding: 40px 35px;
  border-radius: 20px;
  width: 90%;
  max-width: 750px;
  box-shadow: 0 10px 35px rgba(0,0,0,0.25);
  text-align: center;
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.feedback-container h2 {
  margin-bottom: 15px;
  font-size: 26px;
  color: #8a3737;
  font-weight: 600;
}

/* ⭐ Stars */
.stars {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 18px 0;
}

.stars i {
  font-size: 34px;
  color: #d0a19c;
  cursor: pointer;
  transition: transform 0.2s, color 0.25s;
}

.stars i:hover, .stars i.active {
  color: #f6a973;
  transform: scale(1.15);
}

/* ✏ Feedback Box */
textarea {
  width: 100%;
  height: 130px;
  padding: 14px;
  border: 1px solid #f5bfb6;
  outline: none;
  border-radius: 12px;
  resize: none;
  background: #fff0eb;
  color: #6A1E1E;
  font-size: 15px;
  transition: 0.3s;
}

textarea:focus {
  border-color: #e69082;
}

/* ✅ Submit Button */
button {
  margin-top: 18px;
  width: 100%;
  background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
  border: none;
  padding: 14px 25px;
  border-radius: 12px;
  cursor: pointer;
  color: #6A1E1E;
  font-weight: 600;
  font-size: 17px;
  transition: 0.3s ease;
}

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
}

/* ✅ Feedback Message */
#feedback-response {
  margin-top: 15px;
  font-size: 16px;
  color: #8a3737;
  font-weight: 500;
}

/* 📱 Responsive */
@media (max-width: 600px) {
  .feedback-container {
    padding: 30px 20px;
  }
  .stars i { font-size: 30px; }
}
</style>
</head>

<body>

<div class="feedback-container">
  <h2>We Value Your Feedback 💬</h2>
  
  <div class="stars" id="rating-stars">
    <i class="fa-solid fa-star" data-star="1"></i>
    <i class="fa-solid fa-star" data-star="2"></i>
    <i class="fa-solid fa-star" data-star="3"></i>
    <i class="fa-solid fa-star" data-star="4"></i>
    <i class="fa-solid fa-star" data-star="5"></i>
  </div>

  <form id="feedback-form">
    <textarea id="feedback_message" placeholder="Write your feedback here..."></textarea>
    <input type="hidden" id="rating" value="0">
    <button type="submit">Submit Feedback</button>
  </form>

  <div id="feedback-response"></div>
</div>

<script>
let stars = document.querySelectorAll(".stars i");
let ratingInput = document.getElementById("rating");

stars.forEach((star, index) => {
  star.addEventListener("click", () => {
    ratingInput.value = index + 1;
    stars.forEach((s, i) => {
      s.classList.toggle("active", i <= index);
    });
  });
});

document.getElementById("feedback-form").onsubmit = function(e) {
  e.preventDefault();
  let msg = document.getElementById("feedback_message").value.trim();
  let rating = document.getElementById("rating").value;

  if (msg === "") { alert("Please enter your feedback!"); return; }
  if (rating == 0) { alert("Please select a rating!"); return; }

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "save_feedback.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function() {
    if (xhr.status === 200) {
      document.getElementById("feedback-response").innerText = xhr.responseText;
      document.getElementById("feedback_message").value = "";
      ratingInput.value = 0;
      stars.forEach(s => s.classList.remove("active"));
    }
  };
  xhr.send("feedback_message=" + encodeURIComponent(msg) + "&rating=" + rating);
};
</script>

</body>
</html>
