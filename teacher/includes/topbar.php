
<!DOCTYPE html>
<html lang="en">
    <head>
       <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Teacher Portal Topbar</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Removed misplaced .topbar div from <head> -->
<style>
    /* Topbar container - green with black text */
    .topbar {
        height: 64px;
        width: 100%;
        background: #198754;
        color: #000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1100;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .topbar .brand { font-weight:700; color:#000; letter-spacing:0.6px; }

    .topbar-right { display:flex; gap:12px; align-items:center; }

    .user-avatar { width:36px; height:36px; border-radius:50%; background:#fff; color:#000; display:flex;align-items:center;justify-content:center;font-weight:700; }

    .logout-btn { background:#fff; color:#000; padding:8px 12px; border-radius:999px; text-decoration:none; font-weight:600; }
    .logout-btn:hover { background:#f1f1f1; transform:translateY(-2px); }

    @media (max-width:768px) { .topbar .brand .d-none.d-md-inline { display:none !important; } }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top rounded-bottom-4" style="background:#198754;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <span class="fw-bold text-uppercase d-none d-md-inline brand">MIU SCIENCE FACULTY STUDENTS PORTAL</span>
            </a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <div class="d-flex align-items-center gap-2 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <span class="d-flex align-items-center justify-content-center bg-white text-black fw-bold rounded-circle user-avatar" style="width:34px; height:34px; font-size:16px; border:2px solid rgba(0,0,0,0.06);">
                        <?php echo strtoupper(substr($loged_user_name,0,1)); ?>
                    </span>
                    <span class="small fw-semibold user-select-none text-black"><?php echo htmlspecialchars($loged_user_name); ?></span>
                </div>
                <a href="logout.php" class="logout-btn d-flex align-items-center gap-1">
                    <i class="fas fa-sign-out-alt"></i>&nbsp;<span>Logout</span>
                </a>
            </div>
        </div>
    </nav>
    <div style="height:60px;"></div>
    <script>
    // Make alerts dismissible across teacher pages
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.alert').forEach(function(alert){
            // don't add multiple buttons
            if (alert.querySelector('.alert-close')) return;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'alert-close';
            btn.innerHTML = '\u00D7';
            btn.style.border = 'none';
            btn.style.background = 'transparent';
            btn.style.fontSize = '1.2rem';
            btn.style.lineHeight = '1';
            btn.style.cursor = 'pointer';
            btn.style.marginLeft = '12px';
            btn.title = 'Dismiss';
            btn.addEventListener('click', function(){ alert.style.display = 'none'; });
            alert.appendChild(btn);
            // auto-dismiss after 7s
            setTimeout(function(){ if (alert && alert.parentNode) { alert.style.transition = 'opacity 0.4s'; alert.style.opacity = '0'; setTimeout(function(){ if (alert.parentNode) alert.parentNode.removeChild(alert); }, 420); } }, 7000);
        });
    });
    </script>
</body>
</html>