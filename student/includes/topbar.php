<?php
// Get student name from session (set in main.php or included earlier)
$studentName = isset($name) ? $name : (isset($_SESSION['name']) ? $_SESSION['name'] : 'Student');
// Generate initials (two letters max)
$initials = '';
foreach (preg_split('/\s+/', trim($studentName)) as $part) {
    if ($part !== '') {
        $initials .= strtoupper($part[0]);
    }
    if (strlen($initials) >= 2) break;
}
?>

<div class="topbar">
    <style>
    /* Topbar (student - match admin appearance) */
    .topbar {
        height: 60px;
        background: #198754; /* same green as admin */
        color: #000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 18px;
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 1100;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .topbar-left { display:flex; align-items:center; gap:12px; }

    .mobile-menu-btn {
        background: transparent;
        border: none;
        font-size: 20px;
        color: #000;
        cursor: pointer;
    }

    .logo-container img { max-height:36px; height:auto; }

    .school-name {
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.6px;
        padding: 6px 14px;
        border-radius: 22px;
        background: rgba(255,255,255,0.9);
        color: #000;
    }

    .topbar-right { display:flex; align-items:center; gap:16px; }

    .user-info { display:flex; align-items:center; gap:10px; background: rgba(255,255,255,0.85); padding:6px 12px; border-radius:30px; }

    .user-avatar { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#fff; color:#000; font-weight:700; }

    .user-name { font-size:14px; font-weight:600; color:#000; }

    .logout-btn { display:flex; align-items:center; gap:8px; background:#fff; color:#000; padding:6px 12px; border-radius:8px; text-decoration:none; font-weight:600; }

    .logout-btn:hover { background: rgba(0,0,0,0.06); }

    @media (max-width:768px) {
        .school-name { font-size:14px; padding:4px 10px; }
        .user-name { display:none; }
    }
    </style>

    <div class="topbar-left">
        <div class="school-name">MIU SCIENCE FACULTY STUDENTS PORTAL</div>
    </div>

    <div class="topbar-right">
        <div class="user-info">
            <div class="user-avatar"><?php echo htmlspecialchars($initials ? $initials : 'S'); ?></div>
            <div class="user-name"><?php echo 'Hi! ' . htmlspecialchars($studentName); ?></div>
        </div>
        <a class="logout-btn" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<div style="height:60px;"></div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('studentSidebar');
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebarOverlay = document.getElementById('sidebarOverlay');
    function openSidebar(){ if(sidebar) { sidebar.classList.add('open'); } if(sidebarOverlay) { sidebarOverlay.style.display='block'; } }
    function closeSidebar(){ if(sidebar) { sidebar.classList.remove('open'); } if(sidebarOverlay) { sidebarOverlay.style.display='none'; } }
    if(sidebarToggle){ sidebarToggle.addEventListener('click', function(e){ e.stopPropagation(); if(sidebar && sidebar.classList.contains('open')) { closeSidebar(); } else { openSidebar(); } }); }
    if(sidebarOverlay){ sidebarOverlay.addEventListener('click', closeSidebar); }
    window.addEventListener('resize', function(){ if(window.innerWidth > 992) closeSidebar(); });
});
</script>