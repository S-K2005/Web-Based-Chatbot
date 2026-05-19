<?php
session_start();
include('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Panel | Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  
  color: #ffffff;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  transition: background 0.5s ease;
}

/* ===== HEADER ===== */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
    background: linear-gradient(145deg, #f4c3b3, #ffb9ab);
  padding: 12px 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.5);
  top: 0;
  z-index: 1000;
}

.header-left {display: flex; align-items: center; gap: 10px;}
.header-title {font-size: 16px; font-weight: 600; color:white;}
.header-subtitle {font-size: 12px; opacity: 0.8; color:white;}

.mode-toggle, .menu-toggle {cursor: pointer; font-size: 1.6rem; color: ;white}

/* ===== DASHBOARD LAYOUT ===== */
.dashboard {
  display: flex;
  flex: 1;
  overflow: hidden;
  transition: 0.3s;
}

/* ===== SIDEBAR ===== */
.sidebar {
    background: linear-gradient(145deg, #f4c3b3, #ffb9ab);
  width: 250px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: all 0.4s ease;
  z-index: 999;
  overflow: hidden;
}
.sidebar h3 {
  text-align: center;
  color: white;
  margin-bottom: 20px;
  white-space: nowrap;
  transition: opacity 0.3s ease;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  color:white;
  padding: 12px 20px;
  text-decoration: none;
  cursor: pointer;
  transition: 0.3s;
  white-space: nowrap;
}
.nav-item:hover, .nav-item.active {background: rgba(0,0,0,0.08); transform: translateX(6px);}

/* ===== COLLAPSED SIDEBAR ===== */
.sidebar.collapsed {
  width: 80px;
}
.sidebar.collapsed h3,
.sidebar.collapsed .nav-item span,
.sidebar.collapsed .logout span {
  opacity: 0;
  pointer-events: none;
}

/* ===== MAIN CONTENT ===== */
.main-content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background:white;
  transition: all 0.4s ease;
}

/* ===== CARDS ===== */
.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
}
.card {
  background: linear-gradient(145deg, #f4c3b3, #ffb9ab);
  border-radius: 15px;
  padding: 25px;
  text-align: center;
  color: white;
  box-shadow: 0 5px 20px rgba(0,0,0,0.3);
  transition: transform 0.5s ease, box-shadow 0.5s ease;
  animation: slideUp 0.7s ease;
}
.card:hover {transform: translateY(-8px) scale(1.05); box-shadow: 0 10px 30px rgba(0,0,0,0.5);}
.card i {font-size: 42px; margin-bottom: 10px;   color: white;}
.card button {
  background: #ffffff;
  color: #AD4A4A;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s ease;
}
.card button:hover {background: #FFCBC0; color: #6A1E1E;}

iframe {
  width: 100%;
  height: 80vh;
  border: none;
  border-radius: 10px;
  animation: fadeIn 0.6s ease;
}

/* ANIMATIONS */
@keyframes fadeIn {from {opacity: 0;} to {opacity: 1;}}
@keyframes slideUp {from {opacity: 0; transform: translateY(20px);} to {opacity: 1; transform: translateY(0);}}

/* MOBILE RESPONSIVE */
@media (max-width: 900px) {
  .dashboard {flex-direction: column;}
  .sidebar {position: fixed; left: -260px; top: 0; height: 100%; width: 250px;}
  .sidebar.active {left: 0;}
}


.logout {
  margin-top: auto;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: white;
  text-decoration: none;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  margin: 20px;
  transition: 0.3s ease-in-out;
}

.logout i {
  font-size: 20px;
}

.logout:hover {
  background: #ffffff;
  color: #C54747;
  transform: translateY(-5px) scale(1.05);
  box-shadow: 0 8px 25px rgba(0,0,0,0.25);
}


</style>
</head>
<body>

<header class="header">
  <div class="header-left">
    <i class="ri-menu-line menu-toggle" onclick="toggleSidebar()"></i>
    <div class="header-text">
      <div class="header-title">Ramniranjan Jhunjhunwala College of Arts , Commerce and Science (Autonomous)</div>
      <div class="header-subtitle">Opposite Ghatkopar Railway Station West Mumbai-400086, Maharashtra, India</div>
    </div>
  </div>
</header>

<div class="dashboard">
  <aside class="sidebar" id="sidebar">
    <h3>Student Dashboard</h3>

    <a class="nav-item active" data-section="dashboard" href="#"><i class="ri-dashboard-line"></i><span>Dashboard</span></a>
    <a class="nav-item" data-section="chatbot" href="#"><i class="ri-robot-line"></i><span>Chatbot</span></a>
    <a class="nav-item" data-section="Users_Accouncement" href="#"><i class="ri-notification-3-line"></i><span>Notice</span></a>
    <a class="nav-item" data-section="user_add_Question" href="#"><i class="ri-book-open-line"></i><span>Add Question</span></a>
    <a class="nav-item" data-section="User_practical_images" href="#"><i class="ri-image-line"></i><span>Practical Image</span></a>
    <a class="nav-item" data-section="user_practical_video" href="#"><i class="ri-video-line"></i><span>Practical Video</span></a>
    <a class="nav-item" data-section="calendar" href="#"><i class="ri-calendar-line"></i><span>Calendar</span></a>
    <a class="nav-item" data-section="user_feedback" href="#"><i class="ri-feedback-line"></i><span>Feedback</span></a>
   <a class="nav-item" data-section="user_internship" href="#"><i class="ri-briefcase-line"></i><span>Internship </span></a>
    <a class="nav-item" data-section="more" href="#"><i class="ri-more-fill"></i><span>More</span></a>

    <a href="user_logout.php" class="logout"><i class="ri-logout-box-r-line"></i><span>Logout</span></a>
  </aside>

  <main class="main-content" id="main-content"></main>
</div>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (window.innerWidth <= 900) sidebar.classList.toggle('active');
  else sidebar.classList.toggle('collapsed');
}

const sections = {
  dashboard: `<h2 style="color:#ffb9ab;">Dashboard</h2><div class='dashboard-cards'>
  <div class='card'><i class='ri-robot-line'></i><h3>Chatbot</h3><p>Ask academic questions anytime.</p><button onclick="loadSection('chatbot')">Open</button></div>
  <div class='card'><i class='ri-notification-3-line'></i><h3>Notice</h3><p>Stay updated with college notices.</p><button onclick="loadSection('Users_Accouncement')">Open</button></div>
  <div class='card'><i class='ri-image-line'></i><h3>Practical Image</h3><p>Upload or view lab images.</p><button onclick="loadSection('User_practical_images')">Open</button></div>
  <div class='card'><i class='ri-video-line'></i><h3>Practical Video</h3><p>Watch submitted lab videos.</p><button onclick="loadSection('user_practical_video')">Open</button></div>
  <div class='card'><i class='ri-calendar-line'></i><h3>Calendar</h3><p>View academic events.</p><button onclick="loadSection('calendar')">Open</button></div>
  <div class='card'><i class='ri-feedback-line'></i><h3>Feedback</h3><p>Share your opinions.</p><button onclick="loadSection('user_feedback')">Open</button></div>
  <div class='card'><i class="ri-briefcase-line"></i><h3>Internship</h3><p>Share your opinions.</p><button onclick="loadSection('user_internship')">Open</button></div>
  <div class='card'><i class='ri-more-fill'></i><h3>More</h3><p>View or update your profile.</p><button onclick="loadSection('more')">Open</button></div>`,
  
  chatbot:`<iframe src='chatbot.php'></iframe>`,
  Users_Accouncement:`<iframe src='Users_Accouncement.php'></iframe>`,
  user_add_Question:`<iframe src='user_add_Question.php'></iframe>`,
  User_practical_images:`<iframe src='User_practical_images.php'></iframe>`,
  user_practical_video:`<iframe src='user_practical_video.php'></iframe>`,
  calendar:`<iframe src='https://calendar.google.com/calendar/embed?src=en.indian%23holiday%40group.v.calendar.google.com&ctz=Asia/Kolkata'></iframe>`,
  user_feedback:`<iframe src='user_feedback.php'></iframe>`,
  user_internship:`<iframe src='user_internship.php'></iframe>`,
  more:`<iframe src='user_calender.php'></iframe>`
};

document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', e => {
    e.preventDefault();
    loadSection(item.getAttribute('data-section'));
  });
});

function loadSection(section) {
  const main = document.getElementById('main-content');
  main.style.opacity = '0';
  setTimeout(() => {
    main.innerHTML = sections[section];
    main.style.opacity = '1';
  }, 300);

  document.querySelectorAll('.nav-item').forEach(e=>e.classList.remove('active'));
  document.querySelector(`[data-section="${section}"]`).classList.add('active');
  document.getElementById('sidebar').classList.remove('active');
}

window.onload = () => loadSection('dashboard');
</script>

</body>
</html>
