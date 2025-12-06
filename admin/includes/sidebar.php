<div class="sidebar" id="sidebar">
    <style>
/* Sidebar container */
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    /* solid red sidebar background */
    background: #b80000;
    color: #fff;
    overflow-y: auto;
    box-shadow: 2px 0 12px rgba(0,0,0,0.2);
    transition: all 0.25s ease-in-out;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    z-index: 1000;
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

.sidebar-menu .menu-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 8px;
    color: #ffffff; /* white text */
    text-decoration: none;
    transition: background 0.18s, color 0.18s, transform 0.16s;
    font-size: 14px;
    font-weight: 500;
}

.sidebar-menu .menu-link i {
    font-size: 18px;
    width: 20px;
    text-align: center;
    color: #fff; /* ensure icons are white */
}

/* Hover + active states */
.sidebar-menu .menu-link:hover {
    /* solid black hover for maximum contrast */
    background: #000 !important;
    color: #fff !important;
    transform: translateX(6px);
}

.sidebar-menu .menu-link.active {
    background: rgba(0,0,0,0.15);
    color: #fff;
    font-weight: 600;
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
    background: rgba(0,0,0,0.4);
}


</style>

    <link rel="stylesheet" href="/schoolportal/index.css">
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a class="menu-link active" href="index.php">
                    <i class="fas fa-home"></i>
                    
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="manageStudent.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>Manage Students</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="manageTeacher.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Manage Teachers</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="manageParent.php">
                    <i class="fas fa-users"></i>
                    <span>Manage Parents</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="manageStaff.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Manage Staff</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="course.php">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="attendance.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="examSchedule.php">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Exam Schedule</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="salary.php">
                    <i class="fas fa-money-check"></i>
                    <span>Salary</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="report.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="payment.php">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="menu-item">
                <a class="menu-link" href="courseUnit.php">
                    <i class="fas fa-layer-group"></i>
                    <span>Course Units</span>
                </a>
            </li>
        </ul>
    </div>
