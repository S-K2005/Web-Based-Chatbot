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
:root {
  --primary:#ff69b4; /* Pink */
  --hover:#ff85c1;
  --sidebar-bg:rgba(255,255,255,0.25);
  --card-bg:#ffffff;
  --dark-bg:#000;
  --dark-color:#fff;
}

body {
  margin:0;
  font-family:'Poppins',sans-serif;
  background:#fff5f9;
  color:#333;
  overflow-x:hidden;
  transition: background 0.4s, color 0.4s;
}

body.dark-mode {
  background:var(--dark-bg);
  color:var(--dark-color);
}

/* HEADER */
.header {
  background:#ff69b4;
  color:#fff;
  padding:10px 20px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  top:0;
  z-index:1000;
  box-shadow:0 2px 6px rgba(0,0,0,0.2);
}

/* LEFT SIDE: menu + text */
.header-left {
  display:flex;
  align-items:flex-start;
  gap:12px;
  flex:1;
}

.header-text {
  display:flex;
  flex-direction:column;
  line-height:1.3;
}

.header-title {
  font-size:16px;
  font-weight:700;
  color:#fff;
}

.header-subtitle {
  font-size:11px;
  font-weight:500;
  color:#ffe6f0;
}

/* ICONS RIGHT */
.header-right {
  display:flex;
  align-items:center;
  gap:15px;
}

.menu-toggle {
  font-size:26px;
  cursor:pointer;
  color:#fff;
  transition:0.3s;
}
.menu-toggle:hover { color:#000; }

.mode-toggle, .profile-icon {
  cursor:pointer;
  font-size:22px;
  color:#fff;
  transition:0.3s;
}
.mode-toggle:hover, .profile-icon:hover { color:#000; }

/* PROFILE MENU */
.profile-menu {
  position:absolute;
  right:20px;
  top:65px;
  background:#fff;
  border-radius:10px;
  box-shadow:0 6px 15px rgba(0,0,0,0.2);
  padding:10px;
  width:180px;
  display:none;
  z-index:9999;
}
.profile-menu.active { display:block; }
.profile-menu button {
  width:100%;
  padding:8px;
  border:none;
  background:#ff69b4;
  color:#fff;
  font-weight:500;
  border-radius:6px;
  margin-top:5px;
  cursor:pointer;
  transition:0.3s;
}
.profile-menu button:hover { background:#ff85c1; }
.profile-menu .cancel { background:#ccc; color:#000; }
.profile-menu .cancel:hover { background:#bbb; }

/* DASHBOARD */
.dashboard { display:flex; min-height:100vh; transition:0.4s; }

/* SIDEBAR */
.sidebar {
  width:250px;
  background:var(--sidebar-bg);
  backdrop-filter:blur(15px);
  color:#000;
  display:flex;
  flex-direction:column;
  padding:25px 15px;
  box-shadow:2px 0 12px rgba(0,0,0,0.1);
  transition:width 0.4s, transform 0.4s;
}

.sidebar h3 {
  text-align:center;
  font-weight:600;
  font-size:20px;
  margin-bottom:25px;
  color:#ff69b4;
}

.sidebar a {
  display:flex;
  align-items:center;
  padding:12px 14px;
  color:#444;
  margin-bottom:10px;
  border-radius:10px;
  font-weight:500;
  transition:0.3s;
  cursor:pointer;
}

.sidebar a i { margin-right:12px; font-size:22px; color:#ff69b4; }

.sidebar a:hover, .sidebar a.active {
  background:#ff69b4;
  color:#fff;
  transform:translateX(5px);
}

.sidebar a.logout {
  margin-top:auto;
  background:#e53935;
  color:#fff;
  text-align:center;
}
.sidebar a.logout:hover { background:#c62828; }

.sidebar.collapsed { width:70px; align-items:center; }
.sidebar.collapsed h3, .sidebar.collapsed a span { display:none; }
.sidebar.collapsed a { justify-content:center; }
.sidebar.collapsed a i { margin:0; }

/* MAIN CONTENT */
.main-content { flex:1; padding:25px; transition:0.4s; }
.main-content h2 { font-size:24px; color:#ff69b4; margin-bottom:20px; }

/* DASHBOARD CARDS */
.dashboard-cards {
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:20px;
}
.card {
  background:#fff;
  border-radius:15px;
  padding:25px;
  text-align:center;
  box-shadow:0 3px 12px rgba(0,0,0,0.1);
  transition:0.3s;
}
.card i { font-size:38px; color:#ff69b4; margin-bottom:10px; }
.card:hover { transform:translateY(-10px); box-shadow:0 10px 25px rgba(0,0,0,0.15); }
.card button {
  margin-top:10px;
  padding:8px 16px;
  border:none;
  border-radius:6px;
  background:#ff69b4;
  color:#fff;
  cursor:pointer;
  transition:0.3s;
}
.card button:hover { background:#ff85c1; }

iframe { width:100%; height:550px; border:none; border-radius:12px; }

/* DARK MODE PURE BW */
body.dark-mode {
  background:#000;
  color:#fff;
}
body.dark-mode .header {
  background:#111;
}
body.dark-mode .sidebar {
  background:#000;
  color:#fff;
}
body.dark-mode .sidebar a {
  color:#ddd;
}
body.dark-mode .sidebar a.active,
body.dark-mode .sidebar a:hover {
  background:#fff;
  color:#000;
}
body.dark-mode .card {
  background:#111;
  color:#fff;
  box-shadow:0 0 10px rgba(255,255,255,0.1);
}
body.dark-mode .card i { color:#fff; }

/* RESPONSIVE */
@media(max-width:900px){
  .sidebar { position:fixed; left:0; top:0; height:100%; transform:translateX(-100%); }
  .sidebar.active { transform:translateX(0); }
  .header-title { font-size:12px; }
  .header-subtitle { font-size:9px; }
}
</style>
</head>
<body>

<header class="header">
  <div class="header-left">
    <i class="ri-menu-line menu-toggle" onclick="toggleSidebar()"></i>
    <div class="header-text">
      <div class="header-title">HVPS RAMNIRANJAN JHUNJHUNWALA COLLEGE</div>
      <div class="header-subtitle">Opp. Ghatkopar Station, Mumbai-400086</div>
    </div>
  </div>

  <div class="header-right">
    <div class="mode-toggle" onclick="toggleMode()" title="Toggle Dark/Light Mode"><i class="ri-moon-line" id="mode-icon"></i></div>
    <div class="profile-icon" onclick="toggleProfileMenu()" title="Profile Settings"><i class="ri-user-3-line"></i></div>
  </div>

  <div class="profile-menu" id="profileMenu">
    <button onclick="uploadImage()">Upload Image</button>
    <button onclick="removeImage()">Remove Image</button>
    <button class="cancel" onclick="toggleProfileMenu()">Cancel</button>
  </div>
</header>

<div class="dashboard">
  <aside class="sidebar" id="sidebar">
    <h3>Student Panel</h3>
    <a class="nav-item active" data-section="dashboard"><i class="ri-dashboard-line"></i><span>Dashboard</span></a>
    <a class="nav-item" data-section="chatbot"><i class="ri-menu-3-line"></i><span>Chatbot</span></a>
    <a class="nav-item" data-section="notice"><i class="ri-notification-3-line"></i><span>Notice</span></a>
    <a class="nav-item" data-section="profile"><i class="ri-user-line"></i><span>Profile</span></a>
    <a class="nav-item" data-section="courses"><i class="ri-book-open-line"></i><span>Courses</span></a>
    <a class="nav-item" data-section="calendar"><i class="ri-calendar-line"></i><span>Calendar</span></a>
    <a class="nav-item" data-section="feedback"><i class="ri-feedback-line"></i><span>Feedback</span></a>
    <a href="logout.php" class="logout"><i class="ri-logout-box-r-line"></i><span>Logout</span></a>
  </aside>

  <main class="main-content" id="main-content"></main>
</div>

<script>
// Sidebar toggle
function toggleSidebar(){
  const sidebar=document.getElementById('sidebar');
  if(window.innerWidth<=900){
    sidebar.classList.toggle('active');
  }else{
    sidebar.classList.toggle('collapsed');
  }
}

// Sections
const sections={
  dashboard:`<h2>Dashboard</h2>
  <div class="dashboard-cards">
    <div class="card"><i class="ri-menu-3-line"></i><h3>Chatbot</h3><p>Ask your queries anytime.</p><button onclick="loadSection('chatbot')">Open</button></div>
    <div class="card"><i class="ri-notification-3-line"></i><h3>Notice</h3><p>See latest announcements.</p><button onclick="loadSection('notice')">Open</button></div>
    <div class="card"><i class="ri-user-line"></i><h3>Profile</h3><p>Manage your details.</p><button onclick="loadSection('profile')">Open</button></div>
  </div>`,
  chatbot:`<iframe src="chatbot.php"></iframe>`,
  notice:`<iframe src="notice.php"></iframe>`,
  profile:`<iframe src="profile.php"></iframe>`,
  courses:`<iframe src="courses_list.php"></iframe>`,
  calendar:`<iframe src="https://calendar.google.com/calendar/embed?src=en.indian%23holiday%40group.v.calendar.google.com"></iframe>`,
  feedback:`<iframe src="submit_feedback.php"></iframe>`
};

function loadSection(section){
  document.getElementById('main-content').innerHTML=sections[section];
  document.querySelectorAll('.nav-item').forEach(el=>el.classList.remove('active'));
  const nav=document.querySelector(`[data-section="${section}"]`);
  if(nav) nav.classList.add('active');
  document.getElementById('sidebar').classList.remove('active');
}

// Mode toggle
function toggleMode(){
  document.body.classList.toggle('dark-mode');
  const icon=document.getElementById('mode-icon');
  icon.className=icon.className==='ri-moon-line'?'ri-sun-line':'ri-moon-line';
}

// Profile menu
function toggleProfileMenu(){
  document.getElementById('profileMenu').classList.toggle('active');
}

function uploadImage(){
  alert("Upload image feature coming soon!");
}

function removeImage(){
  alert("Profile image removed!");
  toggleProfileMenu();
}

window.onload=()=>loadSection('dashboard');
</script>

</body>
</html>
