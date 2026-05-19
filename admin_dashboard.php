<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

// --- FETCH CURRENT PROFILE IMAGE ---
$defaultImg = 'https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png';
$profileImg = $defaultImg;

$imgRow = $conn->query("SELECT image_path FROM profile_images LIMIT 1");
if ($imgRow && $imgRow->num_rows > 0) {
    $r = $imgRow->fetch_assoc();
    if (!empty($r['image_path']) && file_exists($r['image_path'])) {
        $profileImg = $r['image_path'] . '?v=' . filemtime($r['image_path']);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Admin Dashboard — Premium UI</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<style>
:root{
  --primary:#407BFF;
  --primary-dark:#1a57e0;
  --panel:#0C1635;
  --muted-light:rgba(255,255,255,0.6);
  --transition:all 0.3s ease;
}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{
display:flex;
min-height:100vh;
overflow:hidden;
background:var(--panel);
transition:var(--transition);
}
::-webkit-scrollbar{
display:none;
}

/* SIDEBAR */
.sidebar{position:fixed;left:0;top:0;bottom:0;width:90px;min-width:90px;background:linear-gradient(180deg,var(--primary) 0%,var(--primary-dark) 100%);color:#fff;display:flex;flex-direction:column;align-items:center;padding:2px 10px;border-right:1px solid rgba(255,255,255,0.1);box-shadow:0 0 20px rgba(0,0,0,0.25);transition:var(--transition);}
.sidebar.expanded{width:260px;min-width:260px;}
.brand{display:flex;align-items:center;gap:12px;width:100%;padding:8px;border-radius:10px;cursor:pointer;}
.logo-box{width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff;box-shadow:0 3px 10px rgba(0,0,0,0.25);}
.logo-box img{border-radius:15px;border:2px solid white;width:48px;height:48px;}
.brand-name{display:none;}
.sidebar.expanded .brand-name{display:block;}
.brand-name .title{font-size:17px;font-weight:600;}
.brand-name .sub{font-size:12px;color:var(--muted-light);}

/* NAV */
.nav{width:100%;display:flex;flex-direction:column;margin-top:15px;gap:10px;}
.nav-item{display:flex;align-items:center;gap:16px;width:100%;padding:12px 14px;border-radius:15px;color:#fff;cursor:pointer;position:relative;transition:var(--transition);background:rgba(255,255,255,0.1);box-shadow:0 3px 10px rgba(0,0,0,0.25);}
.nav-item:hover{background:rgba(255,255,255,0.18);transform:translateY(-2px) scale(1.02);}
.nav-item.active{background:rgba(255,255,255,0.25);box-shadow:inset 0 0 10px rgba(255,255,255,0.3),0 0 12px rgba(0,0,0,0.4);transform:scale(1.03);}
.nav-item i{font-size:18px;width:38px;text-align:center;}
.nav-item span{display:none;}
.sidebar.expanded .nav-item span{display:inline-block;}

/* 🌙 FOOTER BASE */
.sidebar-footer {
  margin-top: auto;
  width: 100%;
  padding: 10px 0;
  display: flex;
  justify-content: center;
  align-items: center;

}

/* ⚡ FOOTER CONTAINER */
.footer-card {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 14px;
  width: 100%;
}

/* 🎯 FOOTER BUTTONS */
.footer-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
  box-shadow: inset 0 0 6px rgba(255, 255, 255, 0.05),
              0 2px 6px rgba(0, 0, 0, 0.3);
}

/* 💫 HOVER EFFECT (Neon Blue Glow) */
.footer-btn:hover {
  background: rgba(64, 123, 255, 0.2);
  box-shadow: 0 0 10px rgba(64, 123, 255, 0.7),
              inset 0 0 6px rgba(64, 123, 255, 0.4);
  transform: translateY(-2px) scale(1.1);
}

/* 🧠 ICON STYLING */
.footer-btn i {
  pointer-events: none;
}
/* MAIN */
.main{margin-left:90px;flex:1;display:flex;flex-direction:column;background:var(--panel);transition:var(--transition);}
.sidebar.expanded ~ .main{margin-left:260px;}
.topbar{height:70px;display:flex;align-items:center;justify-content:space-between;padding:0 20px;background:#407BFF;backdrop-filter:blur(8px);border-bottom:1px solid rgba(0,0,0,0.1);}
.topbar-left{display:flex;align-items:center;gap:10px;}
.hambtn{width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;color:#000;cursor:pointer;box-shadow:inset 0 0 6px rgba(255,255,255,0.15),0 4px 10px rgba(0,0,0,0.2);transition:all 0.35s ease;}
.hambtn i{font-size:20px;transition:transform 0.3s ease;color:white;}
.hambtn:hover i{transform:rotate(180deg);}
.page-title{font-weight:600;color:#f8f8f8;}
.page-sub{font-size:12px;color:#f8f8f8;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.profile{display:flex;align-items:center;gap:8px;padding:5px 8px;background:rgba(255,255,255,0.12);border-radius:10px;cursor:pointer;}
.profile img{width:38px;height:38px;border-radius:50%;object-fit:cover;transition:opacity 0.3s ease;}
iframe#contentFrame{flex:1;border:none;width:100%;height:calc(100vh - 70px);}

/* 🌟 PROFILE PANEL */
.profile-panel {
  position: fixed;
  top: 80px;
  right: -420px;
  width: 360px;
  height: calc(100vh - 120px);
  background: rgba(30, 35, 45, 0.9);
  color: #fff;
  border-left: 1px solid rgba(255, 255, 255, 0.05);
  box-shadow: -8px 0 25px rgba(0, 0, 0, 0.6);
  z-index: 1200;
  transition: right 0.4s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px 24px;
  backdrop-filter: blur(10px);
  border-radius: 20px 0 0 20px;
}
.profile-panel.open { right: 0; }
.profile-panel .avatar {
  width: 150px; height: 150px; border-radius: 50%; border: 3px solid #407BFF;
  overflow: hidden; box-shadow: 0 8px 25px rgba(64, 123, 255, 0.4);
  margin-bottom: 22px; transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.profile-panel .avatar:hover { transform: scale(1.05); box-shadow: 0 10px 35px rgba(64, 123, 255, 0.6); }
.profile-panel .avatar img { width:100%; height:100%; object-fit:cover; transition:opacity 0.3s ease, transform 0.3s ease; }
.profile-panel .avatar img:hover { opacity:0.9; transform:scale(1.02); }
.panel-btn {
  width: 100%; padding: 13px 15px; border-radius: 12px; border: none;
  font-weight: 600; font-size: 15px; cursor: pointer; margin-bottom: 14px;
  transition: all 0.3s ease; letter-spacing: 0.3px;
  display:flex; justify-content:center; align-items:center; gap:8px;
}
.panel-btn.upload {
  background: linear-gradient(135deg, #407BFF, #0056ff);
  color:#fff; box-shadow:0 4px 15px rgba(64, 123, 255, 0.4);
}
.panel-btn.upload:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(64,123,255,0.5); }
.panel-btn.remove {
  background: linear-gradient(135deg, #ff4757, #d41e31);
  color:#fff; box-shadow:0 4px 15px rgba(255,71,87,0.4);
}
.panel-btn.remove:hover { transform: translateY(-3px); box-shadow:0 6px 20px rgba(255,71,87,0.5); }
.panel-btn.close {
  background: rgba(255,255,255,0.08); color:#fff; border:1px solid rgba(255,255,255,0.05);
}
.panel-btn.close:hover { background:rgba(255,255,255,0.15); transform:translateY(-3px); }
#fileInput { display:none; }

/* 📱 RESPONSIVE BEHAVIOR */
@media (max-width: 992px) {
  .sidebar {
    position: fixed; left: -260px; width: 260px; min-width:260px; z-index:2000;
    height: 100vh; transition: left 0.4s ease;
  }
  .sidebar.expanded { left:0; }
  .main { margin-left:0 !important; }
  body.menu-open::after {
    content:""; position:fixed; inset:0; background:rgba(0,0,0,0.5);
    z-index:1500; backdrop-filter:blur(2px);
  }
  .profile-panel { width:100%; right:-100%; border-radius:0; height:calc(100vh - 70px); }
  .profile-panel.open { right:0; }
}
@media (max-width:480px){
  .nav-item span{display:inline-block!important;font-size:14px;}
  .nav-item i{font-size:16px;}
  .profile img{width:32px;height:32px;}
  .panel-btn{font-size:14px;padding:12px;}
  .avatar{width:120px;height:120px;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="brand" data-src="dashboard.php">
    <div class="logo-box"><img src="https://thumbs2.imgbox.com/92/11/BtnGRltu_t.jpg" alt="logo"/></div>
    <div class="brand-name"><div class="title">Admin Panel</div><div class="sub">R J College</div></div>
  </div>

  <nav class="nav">
    <div class="nav-item active" data-src="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></div>
   <div class="nav-item active" data-src="accouncement.php"><i class="fas fa-bullhorn"></i><span>Announcements</span></div>
    <div class="nav-item" data-src="calendar_pdf.php"><i class="fas fa-calendar-alt"></i><span>Academic Calendar</span></div>
    <div class="nav-item" data-src="internship.php"><i class="fas fa-briefcase"></i><span>Internship</span></div>
    <div class="nav-item" data-src="manage_users.php"><i class="fas fa-users"></i><span>Manage Users</span></div>
    <div class="nav-item" data-src="faq_manager.php"><i class="fas fa-question-circle"></i><span>FAQs</span></div>
    <div class="nav-item" data-src="manage_feedback.php"><i class="fas fa-comments"></i><span>User Feedback</span></div>
    <div class="nav-item" data-src="manage_questions.php"><i class="fas fa-bell"></i><span>Notification</span></div>
    <div class="nav-item" data-src="admin_video.php"><i class="fas fa-video"></i><span>Practical Video</span></div>
    <div class="nav-item" data-src="admin_images.php"><i class="fas fa-image"></i><span>Practical Image</span></div>
  </nav>

  <div class="sidebar-footer">
    <div class="footer-card">
      <div class="footer-btn" id="profileBtn" title="Profile"><i class="fas fa-user"></i></div>
      <div class="footer-btn" id="expandBtn" title="Expand/Collapse"><i class="fas fa-angles-right"></i></div>
      <div class="footer-btn" onclick="logoutUser()" title="Logout"><i class="fas fa-sign-out-alt"></i></div>
    </div>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <header class="topbar">
    <div class="topbar-left">
      <div class="hambtn" id="hambtn"><i class="fas fa-bars"></i></div>
      <div>
        <div class="page-title" id="pageTitle">Dashboard</div>
        <div class="page-sub" id="pageSubtitle">Overview & Analytics</div>
      </div>
    </div>
    <div class="topbar-right">
      <div class="profile" id="topProfile" onclick="toggleProfilePanel()">
        <img id="topProfileImg" src="<?= htmlspecialchars($profileImg) ?>" onerror="this.src='<?= $defaultImg ?>'">
        <div><div style="font-weight:600">Admin</div><div style="font-size:12px;color:var(--muted-light)">Super User</div></div>
      </div>
    </div>
  </header>
  <iframe id="contentFrame" src="dashboard.php"></iframe>
</main>

<!-- PROFILE PANEL -->
<div class="profile-panel" id="profilePanel">
  <div class="avatar"><img id="panelImg" src="<?= htmlspecialchars($profileImg) ?>" onerror="this.src='<?= $defaultImg ?>'"></div>
  <h3>Manage Profile Image</h3>
  <p style="color:rgba(255,255,255,0.7);font-size:13px;margin-bottom:18px;">Upload or remove your profile picture</p>

  <form id="uploadForm" action="upload_profile_image.php" method="POST" enctype="multipart/form-data" style="width:100%;">
    <label for="fileInput" class="panel-btn upload"><i class="fa-solid fa-upload"></i> Upload / Change</label>
    <input type="file" id="fileInput" name="image" accept="image/*">
  </form>

  <button class="panel-btn remove" onclick="confirmDelete()"><i class="fa-solid fa-trash"></i> Remove</button>
  <button class="panel-btn close" onclick="toggleProfilePanel()">Close</button>
</div>

<script>
const sidebar=document.getElementById('sidebar');
const expandBtn=document.getElementById('expandBtn');
const hambtn=document.getElementById('hambtn');
const navItems=document.querySelectorAll('.nav-item');
const contentFrame=document.getElementById('contentFrame');
const pageTitle=document.getElementById('pageTitle');
const pageSubtitle=document.getElementById('pageSubtitle');
const profilePanel=document.getElementById('profilePanel');
const topProfileImg=document.getElementById('topProfileImg');
const panelImg=document.getElementById('panelImg');

// Sidebar Toggle
expandBtn.onclick=()=>toggleSidebar();
hambtn.onclick=()=>toggleSidebar();
function toggleSidebar(){
  sidebar.classList.toggle('expanded');
  document.body.classList.toggle('menu-open');
}

// Close sidebar on background click (mobile)
document.body.addEventListener('click',e=>{
  if(window.innerWidth<=992 && !sidebar.contains(e.target) && !hambtn.contains(e.target)){
    sidebar.classList.remove('expanded');
    document.body.classList.remove('menu-open');
  }
});

// Nav click
navItems.forEach(item=>{
  item.addEventListener('click',()=>{
    navItems.forEach(i=>i.classList.remove('active'));
    item.classList.add('active');
    const src=item.getAttribute('data-src');
    contentFrame.src=src;
    pageTitle.textContent=item.querySelector('span')?.textContent || 'Page';
    pageSubtitle.textContent='Manage '+(item.querySelector('span')?.textContent || '');
    if(window.innerWidth<=992) toggleSidebar();
  });
});

function logoutUser(){if(confirm("Logout?"))location.href='admins_auth.php';}
function toggleProfilePanel(){profilePanel.classList.toggle('open');}
function refreshProfileImage(){
  fetch('get_profile_image.php')
  .then(r=>r.json())
  .then(d=>{
    if(d.success){
      const newSrc=d.image+'?v='+Date.now();
      topProfileImg.src=newSrc;
      panelImg.src=newSrc;
    }
  });
}
document.getElementById('fileInput').addEventListener('change',()=>{
  const fd=new FormData(document.getElementById('uploadForm'));
  fetch('upload_profile_image.php',{method:'POST',body:fd})
  .then(r=>r.json())
  .then(d=>{if(d.success)refreshProfileImage();else alert('Upload failed');});
});
function confirmDelete(){
  if(!confirm('Remove current image?'))return;
  fetch('delete_profile_image.php')
  .then(r=>r.json())
  .then(d=>{if(d.success)refreshProfileImage();else alert('Delete failed');});
}


  // Profile panel toggle
  function toggleProfilePanel() {
    profilePanel.classList.toggle('open');
    profilePanel.setAttribute('aria-hidden', !profilePanel.classList.contains('open'));
  }
  profileBtn && profileBtn.addEventListener('click', toggleProfilePanel);
  openProfile && openProfile.addEventListener('click', toggleProfilePanel);
  closePanel.addEventListener('click', toggleProfilePanel);

  // Upload via AJAX to same file (?action=upload)
  fileInput.addEventListener('change', () => {
    const f = fileInput.files[0];
    if (!f) return;
    const fd = new FormData();
    fd.append('image', f);
    fetch('?action=upload', { method: 'POST', body: fd })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          const newSrc = json.image + '?v=' + Date.now();
          topProfileImg.src = newSrc;
          panelImg.src = newSrc;
          toggleProfilePanel();
          toast('Profile updated');
          loadStats();
        } else {
          alert('Upload failed: ' + (json.message || 'unknown'));
        }
      })
      .catch(err => alert('Upload failed: ' + err.message));
  });

  // Remove via AJAX ?action=delete
  removeBtn.addEventListener('click', () => {
    if (!confirm('Remove profile image?')) return;
    fetch('?action=delete', { method: 'POST' })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          const defaultUrl = 'https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png';
          topProfileImg.src = defaultUrl;
          panelImg.src = defaultUrl;
          toggleProfilePanel();
          toast('Profile removed');
          loadStats();
        } else {
          alert('Delete failed');
        }
      })
      .catch(() => alert('Delete failed'));
  });

  // small toast helper
  function toast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {
      position: 'fixed', right: '20px', bottom: '20px', background: 'linear-gradient(90deg,#ff6fb5,#ffd1e8)',
      color: '#fff', padding: '10px 14px', borderRadius: '10px', boxShadow: '0 10px 30px rgba(107,43,74,0.12)', zIndex: 9999
    });
    document.body.appendChild(t);
    setTimeout(()=> t.style.opacity = '0', 1600);
    setTimeout(()=> t.remove(), 2000);
  }

</script>
</body>
</html>
