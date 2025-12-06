<?php
include_once('main.php');

// Fetch dashboard stats from the database
$total_students = $mysqli->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$total_teachers = $mysqli->query("SELECT COUNT(*) FROM teachers")->fetch_row()[0];

// Calculate attendance rate (for today)
$today = date('Y-m-d');
$total_attendance = $mysqli->query("SELECT COUNT(*) FROM attendance WHERE date = '$today' AND role = 'student'")->fetch_row()[0];
$total_students_today = $mysqli->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$attendance_rate = $total_students_today > 0 ? round(($total_attendance / $total_students_today) * 100, 1) : 0;

// Fetch recent activities (example: last 4 student/teacher/fee/exam actions)
$activities = [];

// New student registered
$res = $mysqli->query("SELECT name, addmissiondate FROM students ORDER BY addmissiondate DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $activities[] = [
        'icon' => 'fa-user-plus',
        'title' => 'New student registered',
        'desc' => htmlspecialchars($row['name']) . ' joined',
        'time' => date('M d', strtotime($row['addmissiondate']))
    ];
}

// Fee payment received
$res = $mysqli->query("SELECT studentid, amount, month, year FROM payment ORDER BY id DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $activities[] = [
        'icon' => 'fa-money-check',
        'title' => 'Fee payment received',
        'desc' => 'From ' . htmlspecialchars($row['studentid']) . ' for ' . htmlspecialchars($row['month']) . ' ' . htmlspecialchars($row['year']),
        'time' => 'Recent'
    ];
}

// Exam schedule published (show course name)
$res = $mysqli->query("SELECT es.examdate, cu.name AS course_name FROM exam_schedule es JOIN course_units cu ON es.course_unit_id = cu.id ORDER BY es.examdate DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $activities[] = [
        'icon' => 'fa-clipboard-list',
        'title' => 'Exam schedule published',
        'desc' => htmlspecialchars($row['course_name']),
        'time' => date('M d', strtotime($row['examdate']))
    ];
}

// Teacher assigned
$res = $mysqli->query("SELECT name, id FROM teachers ORDER BY id DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $activities[] = [
        'icon' => 'fa-chalkboard-teacher',
        'title' => 'Teacher assigned',
        'desc' => htmlspecialchars($row['name']) . ' assigned',
        'time' => 'Recent'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MIU SCIENCE FACULTY PORTAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>
<body>
    <!-- Topbar -->
    <?php include_once('includes/topbar.php'); ?>
    <!-- Sidebar -->
    <?php include_once('includes/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span>/</span>
                <span>Dashboard</span>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-grid mb-4">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Students</div>
                    <div class="stat-card-icon" style="background-color: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?php echo $total_students; ?></div>
                <div class="stat-card-desc">Live count</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Teachers</div>
                    <div class="stat-card-icon" style="background-color: rgba(14, 159, 110, 0.1); color: #0e9f6e;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?php echo $total_teachers; ?></div>
                <div class="stat-card-desc">Live count</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Attendance Rate</div>
                    <div class="stat-card-icon" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?php echo $attendance_rate; ?>%</div>
                <div class="stat-card-desc">Today</div>
            </div>
        </div>

        <div class="recent-activities">
            <h2 class="section-title">Recent Activities</h2>
            <ul class="activity-list">
                <?php foreach ($activities as $activity): ?>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas <?php echo $activity['icon']; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title"><?php echo $activity['title']; ?></div>
                        <div class="activity-desc"><?php echo $activity['desc']; ?></div>
                        <div class="activity-time"><?php echo $activity['time']; ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickInsideMenuBtn = mobileMenuBtn.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickInsideMenuBtn && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            }
        });
        
        // Adjust content area on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('open');
            }
        });
        
        // Your existing functions
        function changemouseover(element) {
            // Your existing implementation
        }
        
        function changemouseout(element, text) {
            // Your existing implementation
        }
    </script>
</body>
</html>