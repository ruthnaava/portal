 <div class="topbar">
    <style>
/* Topbar */
.topbar {
    height: 60px;
    /* Bootstrap success green */
    background: #198754;
    /* Make topbar text black per request */
    color: #000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1100;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Left section */
.topbar-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.mobile-menu-btn {
    background: transparent;
    border: none;
    font-size: 22px;
    color: #000; /* black icon */
    cursor: pointer;
    transition: transform 0.2s;
}

.mobile-menu-btn:hover {
    transform: scale(1.1);
}

.logo-container img {
    /* image styling removed per request - keep natural size */
    height: auto;
    max-height: 36px;
    width: auto;
    border-radius: 0;
    background: transparent;
    padding: 0;
}

.school-name {
    font-size: 20px; /* larger so it's more visible */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.8px; /* spaced letters */
    padding: 6px 14px; /* rounded pill look */
    border-radius: 22px;
    background: rgba(255,255,255,0.9); /* white pill for contrast */
    color: #000; /* ensure black text */
}

@media (max-width: 768px) {
    .school-name {
        font-size: 16px;
        padding: 4px 10px;
        letter-spacing: 1px;
    }
}

/* Right section */
.topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* User info */
.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    /* light white background so black text remains readable on green */
    background: rgba(255,255,255,0.85);
    padding: 6px 12px;
    border-radius: 30px;
    transition: background 0.3s;
    color: #000;
}

.user-info:hover {
    background: rgba(255,255,255,0.95);
}

.user-avatar {
    width: 35px;
    height: 35px;
    background: #fff;
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    text-transform: uppercase;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #000;
}

/* Logout button */
.logout-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    color: #000;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.18s ease;
}

.logout-btn:hover {
    background: rgba(0,0,0,0.08);
    color: #000;
}

</style>

        <div class="topbar-left">
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>

            <div class="school-name">MIU SCIENCE FACULTY STUDENTS PORTAL</div>
        </div>
        
        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">A</div>
                <div class="user-name">Hi! admin <?php echo $check; ?></div>
            </div>
            <a class="logout-btn" href="logout.php" onmouseover="changemouseover(this);" onmouseout="changemouseout(this,'<?php echo ucfirst($loged_user_name);?>');">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
