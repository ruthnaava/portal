<div class="sidebar" id="sidebar">
    <style>
/* Sidebar container */
.sidebar {
    width: 220px;
    height: 100vh;
    position: fixed;
    top: 0; left: 0;
    background: #b80000; /* solid university red */
    color: #fff;
    overflow-y: auto;
    box-shadow: 2px 0 12px rgba(0,0,0,0.2);
    transition: width 0.3s, all 0.3s ease;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    z-index: 10;
}

.main-content {
    margin-left: 240px; /* leave room for sidebar */
    padding: 20px;
    position: relative;
    z-index: 1;
}



/* Sidebar menu */
.sidebar-menu {
    list-style: none;
    padding: 15px 0;
    margin: 0;
}

.sidebar-menu .menu-item {
    margin: 5px 10px;
}

/* Sidebar links */
.sidebar-menu .menu-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    transition: background 0.18s, color 0.18s, transform 0.12s;
}

.sidebar-menu .menu-link i {
    font-size: 18px;
    width: 20px;
    text-align: center;
}

/* Hover + active */
.sidebar-menu .menu-link:hover {
    background: #000; /* black hover */
    color: #fff;
    transform: translateX(6px);
}

.sidebar-menu .menu-link.active {
    background: rgba(0,0,0,0.9);
    color: #fff;
    font-weight: 700;
}

/* Scrollbar styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.25);
    border-radius: 4px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(243, 14, 14, 0.5);
}

/* Mobile responsiveness */
@media (max-width: 992px) {
    .sidebar {
        left: -260px;
    }
    .sidebar.open {
        left: 0;
    }
}
</style>

    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <div class="sidebar-header text-center py-4">

        <div class="sidebar-school">
            TEACHERS MODULE
        </div>
    </div>
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a class="menu-link active" href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="mycourse.php">
                    <i class="fas fa-book"></i>
                    <span>My Course Units</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="mystudents.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>My Students</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="attendance.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="mycourseinfo.php">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Grades</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="report.php">
                    <i class="fas fa-flag"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="viewProfile.php">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="shareNotes.php">
                    <i class="fas fa-user"></i>
                    <span>Notes</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="awardmarks.php">
                    <i class="fas fa-marker"></i>
                    <span>Award Marks & Grades</span>
                </a>
            </li>
        </ul>
    </div>
