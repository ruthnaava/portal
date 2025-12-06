<nav class="student-sidebar" id="studentSidebar">
        <link rel="stylesheet" href="/school portal/index.css">
        <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* Sidebar Styling */
.student-sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    /* solid red sidebar to match admin style */
    background: #b80000;
    color: #fff;
    transition: all 0.3s;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0,0,0,0.15);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.student-sidebar .sidebar-header {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.student-sidebar .sidebar-logo img {
    max-width: 80px;
    border-radius: 50%;
    margin-bottom: 10px;
    border: 2px solid #fff;
}

.student-sidebar .sidebar-school {
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.3;
}

.student-sidebar .sidebar-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.student-sidebar .sidebar-menu li {
    margin: 5px 0;
}

.student-sidebar .sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    text-decoration: none;
    color: #fff;
    border-radius: 8px;
    transition: background 0.18s, transform 0.16s;
    font-size: 15px;
}

.student-sidebar .sidebar-menu a i {
    margin-right: 10px;
    font-size: 18px;
    color: #fff; /* ensure icons are white */
}
.student-sidebar .sidebar-menu a:hover {
    /* solid black hover for maximum contrast */
    background: #000 !important;
    color: #fff !important;
    transform: translateX(6px);
}

.student-sidebar .sidebar-menu a.active {
    background: rgba(0,0,0,0.15);
    color: #fff;
    font-weight: 600;
}

.sidebar-resizer {
    height: 6px;
    background: rgba(0,0,0,0.12);
    cursor: ns-resize;
    margin-top: auto;
}
.sidebar-resizer:hover {
    background: rgba(0,0,0,0.24);
}
.dashboard {
    background: transparent;
}
.dashboard h1 {
    color: #fff;
    text-align: center;
    margin-top: 50px;
    background-color: transparent;
}
</style>

        
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fas fa-home"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="course.php"><i class="fas fa-book"></i> <span class="menu-text">My Courses</span></a></li>
            <li><a href="timetable.php"><i class="fas fa-calendar-alt"></i> <span class="menu-text">Timetable</span></a></li>
            <li><a href="exam.php"><i class="fas fa-clipboard-list"></i> <span class="menu-text">Exam Schedule</span></a></li>
            <li><a href="attendance.php"><i class="fas fa-user-check"></i> <span class="menu-text">Attendance</span></a></li>
            <li><a href="notes.php"><i class="fas fa-file-alt"></i> <span class="menu-text">Notes & Documents</span></a></li>
            <li><a href="inquiry.php"><i class="fas fa-envelope"></i> <span class="menu-text">Contact Teacher</span></a></li>
            <li><a href="changepassword.php"><i class="fas fa-key"></i> <span class="menu-text">Change Password</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span class="menu-text">Logout</span></a></li>
        </ul>
        <div class="sidebar-resizer" id="sidebarResizer"></div>
    </nav>
