<?php
session_start();
include('db_connect.php');

$student_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Panel | Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" href="User_Dashbord.css">
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="header">
  <div class="header-left">
    <h2 class="header-title">
      Hindi Vidya Prachar Samiti's RAMNIRANJAN JHUNJHUNWALA COLLEGE OF ARTS, SCIENCE & COMMERCE (EMPOWERED AUTONOMOUS)
    </h2>
  </div>
  <div class="header-right">
    <i class="ri-menu-line menu-toggle" onclick="toggleSidebar()"></i>
  </div>
</header>

<!-- ===== DASHBOARD ===== -->
<div class="dashboard">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <h3>Student Panel</h3>
    <a class="nav-item active" data-section="dashboard"><i class="ri-dashboard-line"></i><span>Dashboard</span></a>
    <a class="nav-item" data-section="chatbot"><i class="ri-menu-3-line"></i><span>Chatbot</span></a>
    <a class="nav-item" data-section="notice"><i class="ri-notification-3-line"></i><span>Notice</span></a>
    <a class="nav-item" data-section="profile"><i class="ri-user-line"></i><span>Profile</span></a>
    <a class="nav-item" data-section="courses"><i class="ri-book-open-line"></i><span>Courses</span></a>
    <a class="nav-item" data-section="practical_image"><i class="ri-image-line"></i><span>Practical Image</span></a>
    <a class="nav-item" data-section="practical_video"><i class="ri-video-line"></i><span>Practical Video</span></a>
    <a class="nav-item" data-section="calendar"><i class="ri-calendar-line"></i><span>Calendar</span></a>
    <a class="nav-item" data-section="feedback"><i class="ri-feedback-line"></i><span>Feedback</span></a>
    <a href="logout.php" class="logout"><i class="ri-logout-box-r-line"></i><span>Logout</span></a>
  </aside>

  <!-- Main Content -->
  <main class="main-content" id="main-content"></main>
</div>

<!-- ===== JAVASCRIPT ===== -->
<script>
// ===== SIDEBAR TOGGLE =====
function toggleSidebar(){
  const sidebar = document.getElementById('sidebar');
  if(window.innerWidth <= 900){
    sidebar.classList.toggle('active');
  } else {
    sidebar.classList.toggle('collapsed');
  }
}

// ===== SECTIONS CONTENT =====
const sections = {
  dashboard: `
    <h2>Dashboard</h2>
    <div class="dashboard-cards">
      <div class="card"><i class="ri-menu-3-line"></i><h3>Chatbot</h3><p>Ask academic questions anytime.</p><button onclick="loadSection('chatbot')">Open</button></div>
      <div class="card"><i class="ri-notification-3-line"></i><h3>Notice</h3><p>Stay updated with college notices.</p><button onclick="loadSection('notice')">Open</button></div>
      <div class="card"><i class="ri-user-line"></i><h3>Profile</h3><p>View or update your profile.</p><button onclick="loadSection('profile')">Open</button></div>
      <div class="card"><i class="ri-book-open-line"></i><h3>Courses</h3><p>View enrolled subjects and grades.</p><button onclick="loadSection('courses')">Open</button></div>
      <div class="card"><i class="ri-image-line"></i><h3>Practical Image</h3><p>Upload or view lab images.</p><button onclick="loadSection('practical_image')">Open</button></div>
      <div class="card"><i class="ri-video-line"></i><h3>Practical Video</h3><p>Watch submitted lab videos.</p><button onclick="loadSection('practical_video')">Open</button></div>
      <div class="card"><i class="ri-calendar-line"></i><h3>Calendar</h3><p>Academic events & holidays.</p><button onclick="loadSection('calendar')">Open</button></div>
      <div class="card"><i class="ri-feedback-line"></i><h3>Feedback</h3><p>Share your opinions.</p><button onclick="loadSection('feedback')">Open</button></div>
    </div>
  `,
  chatbot: `<iframe src="chatbot.php"></iframe>`,
  notice: `<iframe src="notice.php"></iframe>`,
  profile: `<iframe src="profile.php"></iframe>`,
  courses: `<iframe src="courses_list.php"></iframe>`,
  practical_image: `<iframe src="practical_image.php"></iframe>`,
  practical_video: `<iframe src="practical_video.php"></iframe>`,
  calendar: `<iframe src="https://calendar.google.com/calendar/embed?src=en.indian%23holiday%40group.v.calendar.google.com&ctz=Asia/Kolkata"></iframe>`,
  feedback: `<iframe src="submit_feedback.php"></iframe>`
};

// ===== LOAD SECTION FUNCTION =====
function loadSection(section){
  document.getElementById('main-content').innerHTML = sections[section];
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  const nav = document.querySelector(`[data-section="${section}"]`);
  if(nav) nav.classList.add('active');
  document.getElementById('sidebar').classList.remove('active'); // close on mobile
}

// ===== ATTACH CLICK EVENTS TO SIDEBAR LINKS =====
document.querySelectorAll('.sidebar .nav-item').forEach(link => {
  link.addEventListener('click', function(){
    const section = this.getAttribute('data-section');
    loadSection(section);
  });
});

// ===== DEFAULT LOAD =====
window.onload = () => loadSection('dashboard');
</script>

</body>
</html>