<?php
// admin_panel.php (User Dashboard - responsive + icons fixed + AJAX handlers)
// NOTE: keep db_connect.php that sets $conn to your chatbots DB
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

// --- Helper: current profile image path (single-row table) ---
function get_current_image($conn) {
    $default = 'https://media.geeksforgeeks.org/wp-content/uploads/20221210180014/profile-removebg-preview.png';
    $row = $conn->query("SELECT image_path FROM user_profile_images LIMIT 1");
    if ($row && $row->num_rows > 0) {
        $r = $row->fetch_assoc();
        if (!empty($r['image_path']) && file_exists(__DIR__ . '/' . $r['image_path'])) {
            return $r['image_path'];
        } elseif (!empty($r['image_path'])) {
            return $r['image_path'];
        }
    }
    return $default;
}

// --- AJAX actions: upload / delete / stats / lists ---
if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_GET['action'];

    // Upload profile image
    if ($action === 'upload') {
        if (empty($_FILES['image'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            exit;
        }
        $file = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            exit;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
            exit;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploads = __DIR__ . '/uploads';
        if (!is_dir($uploads)) mkdir($uploads, 0755, true);
        $fname = 'profile_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $uploads . '/' . $fname;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            echo json_encode(['success' => false, 'message' => 'Failed to move file']);
            exit;
        }
        $relpath = 'uploads/' . $fname;

        // remove old entries and file
        $oldRow = $conn->query("SELECT image_path FROM user_profile_images LIMIT 1");
        if ($oldRow && $oldRow->num_rows > 0) {
            $old = $oldRow->fetch_assoc();
            if (!empty($old['image_path']) && file_exists(__DIR__ . '/' . $old['image_path'])) {
                @unlink(__DIR__ . '/' . $old['image_path']);
            }
            $conn->query("DELETE FROM user_profile_images");
        }

        $stmt = $conn->prepare("INSERT INTO user_profile_images (image_path) VALUES (?)");
        $stmt->bind_param('s', $relpath);
        $ok = $stmt->execute();

        if ($ok) echo json_encode(['success' => true, 'image' => $relpath]);
        else {
            @unlink($dest);
            echo json_encode(['success' => false, 'message' => 'DB error: could not save image']);
        }
        exit;
    }

    // Delete profile image
    if ($action === 'delete') {
        $row = $conn->query("SELECT image_path FROM user_profile_images LIMIT 1");
        if ($row && $row->num_rows > 0) {
            $r = $row->fetch_assoc();
            if (!empty($r['image_path']) && file_exists(__DIR__ . '/' . $r['image_path'])) {
                @unlink(__DIR__ . '/' . $r['image_path']);
            }
        }
        $conn->query("DELETE FROM user_profile_images");
        echo json_encode(['success' => true]);
        exit;
    }

    // Query stats (counts)
    if ($action === 'query_stats') {
        $users = $conn->query("SELECT COUNT(*) AS c FROM users");
        $faqs  = $conn->query("SELECT COUNT(*) AS c FROM faq");
        $interns = $conn->query("SELECT COUNT(*) AS c FROM internship");
        echo json_encode([
            'success' => true,
            'users' => $users ? (int)$users->fetch_assoc()['c'] : 0,
            'faqs'  => $faqs  ? (int)$faqs->fetch_assoc()['c'] : 0,
            'interns'=> $interns ? (int)$interns->fetch_assoc()['c'] : 0
        ]);
        exit;
    }

    // List users
    if ($action === 'list_users') {
        $res = $conn->query("SELECT id, name, email FROM users ORDER BY id DESC");
        $arr = [];
        if ($res) while($r = $res->fetch_assoc()) $arr[] = $r;
        echo json_encode(['success'=>true,'users'=>$arr]);
        exit;
    }

    // List faqs
    if ($action === 'list_faqs') {
        $res = $conn->query("SELECT id, question, answer FROM faq ORDER BY id DESC");
        $arr = [];
        if ($res) while($r = $res->fetch_assoc()) $arr[] = $r;
        echo json_encode(['success'=>true,'faqs'=>$arr]);
        exit;
    }

    // Unknown
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

// If we reach here -> render UI
$currentImage = htmlspecialchars(get_current_image($conn));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>User Dashboard • R J College</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg: #fff;
    --muted:#9e9aa8;
    --accent1:#ff6fb5;
    --accent2:#ffd1e8;
    --ink:#6b2b4a;
    --glass: rgba(255,255,255,0.95);
    --shadow: 0 16px 40px rgba(107,43,74,0.06);
    --radius:14px;
    --ease: cubic-bezier(.2,.9,.2,1);
    --sidebar-w: 240px;
    --sidebar-collapsed-w: 84px;
  }
  *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif}
  html,body{height:100%;background:linear-gradient(180deg,#fff,#fff6fb);color:var(--ink);min-height:100vh}
  a{color:inherit;text-decoration:none}
  .app{display:flex;gap:22px;min-height:100vh;padding:20px;align-items:flex-start}

  /* Sidebar */
  .sidebar{
    width:var(--sidebar-w);background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(255,255,255,0.95));
    border-radius:16px;padding:18px;box-shadow:var(--shadow);position:sticky;top:20px;height:calc(100vh - 40px);
    transition:width 260ms var(--ease), transform 260ms var(--ease); z-index:40;
  }
  .sidebar.collapsed{width:var(--sidebar-collapsed-w)}
  .brand{display:flex;align-items:center;gap:12px;margin-bottom:18px;cursor:pointer}
  .logo{width:52px;height:52px;border-radius:12px;background:linear-gradient(135deg,var(--accent1),var(--accent2));display:flex;align-items:center;justify-content:center;overflow:hidden}
  .logo img{width:44px;height:44px;border-radius:8px;border:2px solid rgba(255,255,255,0.9)}
  .brand .meta h3{font-size:15px;margin:0;font-weight:700;color:var(--ink)}
  .brand .meta p{margin:0;font-size:12px;color:var(--muted)}
  .nav{display:flex;flex-direction:column;gap:10px;margin-top:6px}
  .nav-item{display:flex;align-items:center;gap:12px;padding:10px;border-radius:12px;color:var(--ink);cursor:pointer;border:1px solid rgba(107,43,74,0.03);transition:all 200ms var(--ease);background:transparent}
  .nav-item i{width:36px;text-align:center;color:var(--accent1);font-size:18px}
  .nav-item .label{font-weight:600}
  .sidebar.collapsed .nav-item .label{display:none}
  .nav-item:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(255,111,181,0.05)}
  .nav-item.active{background:linear-gradient(90deg, rgba(255,111,181,0.08), rgba(255,209,232,0.05));box-shadow:0 14px 30px rgba(255,111,181,0.05)}

  .sidebar-footer{margin-top:auto;display:flex;gap:10px;justify-content:center;padding-top:12px}
  .icon-btn{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:transparent;border:1px solid rgba(107,43,74,0.03);cursor:pointer;color:var(--accent1);transition:transform 260ms var(--ease)}
  .icon-btn:hover{transform:translateY(-6px);box-shadow:0 14px 30px rgba(255,111,181,0.05)}

  /* Main */
  .main{flex:1;display:flex;flex-direction:column;gap:16px;min-height:calc(100vh - 40px)}
  .topbar{height:68px;background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(255,255,255,0.95));border-radius:12px;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);position:sticky;top:20px;z-index:30}
  .top-left{display:flex;align-items:center;gap:12px}
  .hambtn{width:46px;height:46px;border-radius:10px;border:1px solid rgba(0,0,0,0.04);display:flex;align-items:center;justify-content:center;cursor:pointer}
  .title h2{margin:0;font-size:18px;font-weight:700;color:var(--ink)} .title p{margin:0;font-size:13px;color:var(--muted)}
  .top-right{display:flex;align-items:center;gap:12px}
  .profile-mini{display:flex;align-items:center;gap:10px;cursor:pointer}
  .profile-mini img{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.9)}
  .content{flex:1;overflow:auto;padding:22px;border-radius:12px}
  .content::-webkit-scrollbar{width:10px} .content::-webkit-scrollbar-thumb{background:linear-gradient(180deg,var(--accent2),var(--accent1));border-radius:10px}
  .card{background:#fff;border-radius:12px;padding:18px;margin-bottom:16px;box-shadow:0 8px 30px rgba(107,43,74,0.04);transition:transform 200ms var(--ease)}
  .card:hover{transform:translateY(-6px)}
  .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
  .stat{padding:14px;border-radius:12px;background:linear-gradient(180deg,#fff,#fff);box-shadow:0 8px 28px rgba(107,43,74,0.03)}
  .muted{color:var(--muted)}

  /* Sections */
  .section{display:none}
  .section.active{display:block}

  /* Profile panel */
  .profile-panel{position:fixed;right:-420px;top:18vh;width:360px;background:#fff;border-radius:12px;padding:16px;box-shadow:0 40px 80px rgba(107,43,74,0.06);border:1px solid rgba(107,43,74,0.03);transition:right 260ms var(--ease);z-index:999}
  .profile-panel.open{right:18px}
  .panel-avatar{width:110px;height:110px;border-radius:12px;overflow:hidden;margin:0 auto 10px}
  .panel-avatar img{width:100%;height:100%;object-fit:cover}
  .panel-actions{display:flex;gap:8px;justify-content:center;margin-top:12px}
  .btn{padding:10px 12px;border-radius:10px;border:0;cursor:pointer;font-weight:600}
  .btn.upload{background:linear-gradient(90deg,var(--accent1),var(--accent2));color:white}
  .btn.remove{background:#fff1f6;color:var(--ink);border:1px solid rgba(255,111,181,0.06)}
  .btn.close{background:transparent;border:1px solid rgba(0,0,0,0.04)}

  /* Responsive */
  @media (max-width:1100px){
    .grid{grid-template-columns:repeat(2,1fr)}
  }
  @media (max-width:760px){
    .app{flex-direction:column;padding:12px}
    .sidebar{position:fixed;left:12px;top:12px;height:auto;width:calc(100% - 24px);border-radius:12px;display:flex;flex-direction:row;padding:10px;align-items:center;gap:10px}
    .sidebar.collapsed{height:auto;width:calc(100% - 24px)}
    .nav{flex-direction:row;overflow:auto;gap:8px}
    .nav-item{min-width:82px;justify-content:center;padding:8px}
    .main{margin-top:86px}
    .grid{grid-template-columns:1fr}
    .profile-panel{width:92%;right:-200%}
    .profile-panel.open{right:4%}
    .topbar{position:fixed;left:12px;right:12px;top:80px;z-index:50}
    .content{padding-top:120px}
  }

  /* small */
  .center{text-align:center}
  table{width:100%;border-collapse:collapse}
  table td, table th{padding:10px;border-bottom:1px solid rgba(0,0,0,0.04);vertical-align:middle}
  .table-title{font-weight:700;margin-bottom:10px}
</style>
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" aria-label="Main sidebar">
      <div class="brand" id="homeBtn" role="button" tabindex="0">
        <div class="logo"><img src="https://thumbs2.imgbox.com/92/11/BtnGRltu_t.jpg" alt="logo"></div>
        <div class="meta"><h3>Users Dashboard</h3><p>R J College</p></div>
      </div>

      <nav class="nav" id="nav" role="navigation" aria-label="Main navigation">
        <!-- data-section values are normalized to IDs below -->
        <div class="nav-item active" data-section="dashboard" role="button" tabindex="0"><i class="fas fa-home"></i><div class="label">Dashboard</div></div>
        <div class="nav-item" data-section="academic-calendar" role="button" tabindex="0"><i class="fas fa-calendar-alt"></i><div class="label">Academic Calendar</div></div>
        <div class="nav-item" data-section="internship" role="button" tabindex="0"><i class="fas fa-briefcase"></i><div class="label">Internship</div></div>
        <div class="nav-item" data-section="announcement" role="button" tabindex="0"><i class="fas fa-bullhorn"></i><div class="label">Announcement</div></div>
        <div class="nav-item" data-section="add-question" role="button" tabindex="0"><i class="fas fa-plus-circle"></i><div class="label">Add Question</div></div>
        <div class="nav-item" data-section="images" role="button" tabindex="0"><i class="fas fa-image"></i><div class="label">Practical Images</div></div>
        <div class="nav-item" data-section="video" role="button" tabindex="0"><i class="fas fa-video"></i><div class="label">Practical Video</div></div>
        <div class="nav-item" data-section="user-feedback" role="button" tabindex="0"><i class="fas fa-comments"></i><div class="label">Users Feedback</div></div>
      </nav>

      <div class="sidebar-footer" aria-hidden="false">
        <div class="icon-btn" id="toggleBtn" title="Collapse/Expand" role="button" tabindex="0"><i class="fas fa-angles-left"></i></div>
        <div class="icon-btn" id="profileBtn" title="Profile" role="button" tabindex="0"><i class="fas fa-user"></i></div>
        <div class="icon-btn" id="logoutBtn" title="Logout" role="button" tabindex="0"><i class="fas fa-right-from-bracket"></i></div>
      </div>
    </aside>

    <!-- Main -->
    <main class="main" id="mainContent">
      <div class="topbar" role="banner">
        <div class="top-left">
          <div class="hambtn" id="hamb" role="button" tabindex="0"><i class="fas fa-bars"></i></div>
          <div class="title"><h2 id="pageTitle">Dashboard</h2><p id="pageSub">Overview & quick stats</p></div>
        </div>
        <div class="top-right">
          <div class="profile-mini" id="openProfile" role="button" tabindex="0" aria-label="Open profile panel">
            <img id="topProfileImg" src="<?= $currentImage ?>" alt="profile">
          </div>
        </div>
      </div>

      <div class="content" id="content" role="main">
        <!-- Sections (single-page) -->
        <section id="dashboard" class="section active">
          <div class="card">
            <h3 style="margin:0 0 6px 0">Welcome to Dashboard</h3>
            <p class="muted" style="margin:0 0 8px 0">Everything is on this single page — click the sidebar to switch sections.</p>
          </div>

          <div class="grid">
            <div class="stat">
              <div style="display:flex;gap:12px;align-items:center">
                <div style="width:48px;height:48px;border-radius:10px;background:linear-gradient(90deg,#ff6fb5,#ffd1e8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px"><i class="fas fa-users"></i></div>
                <div>
                  <div style="font-size:13px;color:var(--muted);font-weight:700">Total Users</div>
                  <div style="font-size:22px;font-weight:800" id="statUsers">—</div>
                </div>
              </div>
            </div>

            <div class="stat">
              <div style="display:flex;gap:12px;align-items:center">
                <div style="width:48px;height:48px;border-radius:10px;background:linear-gradient(90deg,#ffd1e8,#ff6fb5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px"><i class="fas fa-question-circle"></i></div>
                <div>
                  <div style="font-size:13px;color:var(--muted);font-weight:700">Total FAQs</div>
                  <div style="font-size:22px;font-weight:800" id="statFaqs">—</div>
                </div>
              </div>
            </div>

            <div class="stat">
              <div style="display:flex;gap:12px;align-items:center">
                <div style="width:48px;height:48px;border-radius:10px;background:linear-gradient(90deg,#ff9ad1,#ff6fb5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px"><i class="fas fa-briefcase"></i></div>
                <div>
                  <div style="font-size:13px;color:var(--muted);font-weight:700">Internships</div>
                  <div style="font-size:22px;font-weight:800" id="statIntern">—</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section id="academic-calendar" class="section">
          <div class="card"><h3>Academic Calendar</h3><p class="muted">Upload or view academic calendar here.</p></div>
        </section>

        <section id="internship" class="section">
          <div class="card"><h3>Internships</h3><p class="muted">Manage internship posts.</p></div>
        </section>

        <section id="announcement" class="section">
          <div class="card"><h3>Announcement</h3><p class="muted">Make announcements to users.</p></div>
        </section>

        <section id="add-question" class="section">
          <div class="card"><h3>Add New Question</h3><p class="muted">Add MCQs or practice questions here.</p></div>
        </section>

        <section id="images" class="section">
          <div class="card"><h3>Practical Images</h3><p class="muted">Manage images library.</p></div>
        </section>

        <section id="video" class="section">
          <div class="card"><h3>Practical Video</h3><p class="muted">Manage video uploads.</p></div>
        </section>

        <section id="user-feedback" class="section">
          <div class="card"><h3>Users Feedback</h3><p class="muted">View feedback submitted by users.</p></div>
        </section>

        <section id="users" class="section">
          <div class="card"><h3>Manage Users</h3><p class="muted">You can list and manage users here.</p></div>
          <div class="card" id="usersList"><p class="muted">Loading users...</p></div>
        </section>

        <section id="faqs" class="section">
          <div class="card"><h3>FAQs Manager</h3><p class="muted">Add or edit FAQs here.</p></div>
          <div class="card" id="faqsList"><p class="muted">Loading FAQs...</p></div>
        </section>
      </div>
    </main>
  </div>

  <!-- Profile panel -->
  <aside class="profile-panel" id="profilePanel" aria-hidden="true">
    <div class="panel-avatar"><img id="panelImg" src="<?= $currentImage ?>" alt="profile"></div>
    <h3 class="center">Manage Profile</h3>
    <p class="muted center">Upload or remove your profile picture</p>

    <div style="text-align:center;margin-top:10px">
      <input type="file" id="fileInput" accept="image/*" style="display:none;">
      <div class="panel-actions">
        <label for="fileInput" class="btn upload"><i class="fa-solid fa-upload"></i> Upload</label>
        <button type="button" class="btn remove" id="removeBtn"><i class="fa-solid fa-trash"></i> Remove</button>
        <button type="button" class="btn close" id="closePanel">Close</button>
      </div>
    </div>
  </aside>

<script>
  // Elements
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleBtn');
  const navItems = document.querySelectorAll('.nav-item');
  const sections = document.querySelectorAll('.section');
  const pageTitle = document.getElementById('pageTitle');
  const pageSub = document.getElementById('pageSub');
  const profilePanel = document.getElementById('profilePanel');
  const profileBtn = document.getElementById('profileBtn');
  const openProfile = document.getElementById('openProfile');
  const fileInput = document.getElementById('fileInput');
  const removeBtn = document.getElementById('removeBtn');
  const closePanel = document.getElementById('closePanel');
  const topProfileImg = document.getElementById('topProfileImg');
  const panelImg = document.getElementById('panelImg');
  const logoutBtn = document.getElementById('logoutBtn');
  const homeBtn = document.getElementById('homeBtn');

  // Toggle collapse
  function toggleSidebar(collapsed = null) {
    if (collapsed === null) sidebar.classList.toggle('collapsed');
    else {
      if (collapsed) sidebar.classList.add('collapsed'); else sidebar.classList.remove('collapsed');
    }
    const icon = toggleBtn.querySelector('i');
    icon.classList.toggle('fa-angles-left');
    icon.classList.toggle('fa-angles-right');
  }
  toggleBtn.addEventListener('click', () => toggleSidebar());

  // Section switching
  function showSection(id) {
    // hide all
    sections.forEach(s => s.classList.remove('active'));
    const el = document.getElementById(id);
    if (el) el.classList.add('active');

    navItems.forEach(i => i.classList.remove('active'));
    const activeNav = Array.from(navItems).find(n => n.dataset.section === id);
    if (activeNav) activeNav.classList.add('active');

    // Title
    pageTitle.textContent = id.split('-').map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(' ');
    pageSub.textContent = id === 'dashboard' ? 'Overview & quick stats' : 'Manage ' + pageTitle.textContent;

    // dynamic loads
    if (id === 'users') loadUsers();
    if (id === 'faqs') loadFaqs();
    if (id === 'dashboard') loadStats();
  }

  navItems.forEach(item => {
    item.addEventListener('click', () => {
      showSection(item.dataset.section);
    });
    // keyboard accessibility
    item.addEventListener('keypress', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); item.click(); }});
  });

  // Hamburger for small screens
  document.getElementById('hamb').addEventListener('click', () => {
    toggleSidebar();
  });

  // Home click
  homeBtn.addEventListener('click', () => showSection('dashboard'));
  homeBtn.addEventListener('keypress', e => { if (e.key === 'Enter') showSection('dashboard'); });

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

  // Logout
  logoutBtn.addEventListener('click', () => {
    if (confirm('Logout?')) window.location.href = 'l.html';
  });

  // fetch stats
  function loadStats() {
    fetch('?action=query_stats')
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          document.getElementById('statUsers').textContent = d.users;
          document.getElementById('statFaqs').textContent = d.faqs;
          document.getElementById('statIntern').textContent = d.interns;
        }
      }).catch(()=>{/* ignore */});
  }

  // load users list
  function loadUsers() {
    const el = document.getElementById('usersList');
    el.innerHTML = '<p class="muted">Loading users...</p>';
    fetch('?action=list_users')
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          if (!d.users || d.users.length === 0) el.innerHTML = '<p class="muted">No users yet.</p>';
          else {
            const rows = d.users.map(u => `<tr><td>${u.id}</td><td>${escapeHtml(u.name)}</td><td>${escapeHtml(u.email)}</td></tr>`).join('');
            el.innerHTML = `<div class="card"><div class="table-title">Users</div><table><thead><tr style="text-align:left;color:var(--muted)"><th>ID</th><th>Name</th><th>Email</th></tr></thead><tbody>${rows}</tbody></table></div>`;
          }
        } else el.innerHTML = '<p class="muted">Failed to load users.</p>';
      }).catch(()=> el.innerHTML = '<p class="muted">Failed to load users.</p>');
  }

  // load faqs list
  function loadFaqs() {
    const el = document.getElementById('faqsList');
    el.innerHTML = '<p class="muted">Loading FAQs...</p>';
    fetch('?action=list_faqs')
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          if (!d.faqs || d.faqs.length === 0) el.innerHTML = '<p class="muted">No FAQs yet.</p>';
          else {
            const items = d.faqs.map(f => `<li style="padding:12px;border-bottom:1px solid rgba(0,0,0,0.04)"><strong>Q:</strong> ${escapeHtml(f.question)}<br><small class="muted">A: ${escapeHtml(f.answer)}</small></li>`).join('');
            el.innerHTML = `<div class="card"><ul style="list-style:none;padding:0;margin:0">${items}</ul></div>`;
          }
        } else el.innerHTML = '<p class="muted">Failed to load FAQs.</p>';
      }).catch(()=> el.innerHTML = '<p class="muted">Failed to load FAQs.</p>');
  }

  // escape helper
  function escapeHtml(s){ return String(s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

  // initial
  document.addEventListener('DOMContentLoaded', () => {
    showSection('dashboard');
  });
</script>

</body>
</html>
