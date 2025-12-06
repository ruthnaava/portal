<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../service/mysqlcon.php');
$check = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : null;
if ($check) {
    $stmt = $mysqli->prepare("SELECT name FROM admin WHERE id = ?");
    $stmt->bind_param("s", $check);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $login_session = $loged_user_name = $row ? $row['name'] : null;
} else {
    $login_session = null;
}
if (!isset($login_session) || !$login_session) {
    header("Location:../../");
    exit();
}

// Attendance summary and recent activity
$totalAttendance = 0;
$roleCounts = ['student' => 0, 'teacher' => 0, 'staff' => 0];
$recentAttendance = [];

// Date filter
$selectedDate = isset($_GET['date']) && $_GET['date'] ? $_GET['date'] : date('Y-m-d');

// Get total attendance count
$res = $mysqli->query("SELECT COUNT(*) as total FROM attendance");
if ($res) {
    $row = $res->fetch_assoc();
    $totalAttendance = $row['total'];
}
// Get attendance count by role
$res = $mysqli->query("SELECT role, COUNT(*) as cnt FROM attendance GROUP BY role");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $roleCounts[$row['role']] = $row['cnt'];
    }
}
// Get recent attendance activity
$res = $mysqli->query("SELECT attendedid, role, date FROM attendance ORDER BY date DESC, id DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recentAttendance[] = $row;
    }
}

// Fetch attendance lists for the selected date
$attendanceData = ['student' => [], 'teacher' => [], 'staff' => []];
$names = ['student' => [], 'teacher' => [], 'staff' => []];
// Get all users for each role
$roles = ['student' => 'students', 'teacher' => 'teachers', 'staff' => 'staff'];
foreach ($roles as $role => $table) {
    $res = $mysqli->query("SELECT id, name FROM $table");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $names[$role][$row['id']] = $row['name'];
        }
    }
}
// Get attendance for the selected date
$res = $mysqli->query("SELECT attendedid, role FROM attendance WHERE date = '".$mysqli->real_escape_string($selectedDate)."'");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $attendanceData[$row['role']][] = $row['attendedid'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .attendance-dashboard {
            margin: 40px 0 0 0; /* Remove auto centering, align to top */
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 100%; /* Full width */
            width: 100%;
            text-align: center;
        }
        .attendance-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .attendance-links {
            display: flex; flex-direction: column; gap: 1.2rem; align-items: center;
        }
        .attendance-link {
            display: inline-block; background: #2563eb; color: #fff; border-radius: 6px;
            padding: 0.8rem 2rem; font-size: 1.1rem; font-weight: 600; text-decoration: none;
            transition: background 0.2s;
        }
        .attendance-link:hover { background: #1d4ed8; }
        .summary-cards {
            display: flex; gap: 1.5rem; justify-content: center; margin-bottom: 2rem;
        }
        .summary-card {
            background: #f1f5f9; border-radius: 10px; padding: 1.2rem 2rem; min-width: 160px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; font-weight: 600;
        }
        .summary-card .icon { font-size: 2rem; margin-bottom: 0.5rem; color: #2563eb; }
        .summary-card .count { font-size: 1.5rem; color: #1d4ed8; }
        .recent-activity {
            margin-top: 2.5rem; background: #f9fafb; border-radius: 10px; padding: 1.2rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .recent-activity-title { font-weight: 600; margin-bottom: 1rem; }
        .recent-activity-list { list-style: none; padding: 0; margin: 0; }
        .recent-activity-list li {
            padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.7rem;
        }
        .recent-activity-list li:last-child { border-bottom: none; }
        .role-badge {
            display: inline-block; padding: 0.2rem 0.7rem; border-radius: 6px; font-size: 0.95rem; font-weight: 500;
        }
        .role-student { background: #dbeafe; color: #2563eb; }
        .role-teacher { background: #fef9c3; color: #b45309; }
        .role-staff { background: #fce7f3; color: #be185d; }
        .attendance-filter-bar {
            margin: 2rem 0 1.5rem 0; display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; justify-content: center;
        }
        .attendance-filter-bar label { font-weight: 500; }
        .attendance-tabs {
            display: flex; gap: 1.2rem; justify-content: center; margin-bottom: 1.2rem;
        }
        .attendance-tab {
            background: #f1f5f9; color: #2563eb; border: none; border-radius: 6px 6px 0 0; padding: 0.7rem 2rem; font-weight: 600; cursor: pointer; font-size: 1.1rem; transition: background 0.2s;
        }
        .attendance-tab.active, .attendance-tab:hover { background: #2563eb; color: #fff; }
        .attendance-list-table {
            width: 100%; border-collapse: collapse; margin-bottom: 2rem; background: #fff;
        }
        .attendance-list-table th, .attendance-list-table td {
            padding: 0.7rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .attendance-list-table th { background: #f1f5f9; }
        .present-badge { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.2rem 0.8rem; font-weight: 600; }
        .absent-badge { background: #fee2e2; color: #991b1b; border-radius: 6px; padding: 0.2rem 0.8rem; font-weight: 600; }
        /* Make dashboard full width and remove centering */
        .attendance-dashboard { box-sizing: border-box; width: 100%; max-width: 100%; margin-left: 0; margin-right: 0; }
        .main-content { max-width: 100vw; width: 100vw; box-sizing: border-box; }
    </style>
    <script>
        function switchTab(tab) {
            document.querySelectorAll('.attendance-tab').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.attendance-list-section').forEach(sec => sec.style.display = 'none');
            document.getElementById('tab-' + tab).classList.add('active');
            document.getElementById('section-' + tab).style.display = 'block';
        }
        window.onload = function() {
            switchTab('teacher'); // Default tab
        };
    </script>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="attendance-dashboard text-success bg-light">
            <div class="attendance-title">Attendance Management</div>
            <div class="summary-cards mb-4 d-flex flex-wrap gap-3 justify-content-center ">
                <div class="summary-card">
                    <div class="icon"><i class="fas fa-list"></i></div>
                    <div class="count"><?= $totalAttendance ?></div>
                    <div>Total Records</div>
                </div>
                <div class="summary-card">
                    <div class="icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="count"><?= $roleCounts['student'] ?></div>
                    <div>Students</div>
                </div>
                <div class="summary-card">
                    <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="count"><?= $roleCounts['teacher'] ?></div>
                    <div>Teachers</div>
                </div>
                <div class="summary-card">
                    <div class="icon"><i class="fas fa-user-tie"></i></div>
                    <div class="count"><?= $roleCounts['staff'] ?></div>
                    <div>Staff</div>
                </div>
            </div>
            <div class="attendance-links">
                <a class="attendance-link" style="background-color: #184d03f7; color: #fff; display: flex;" href="teacherAttendance.php"><i class="fas fa-chalkboard-teacher"></i> Teacher Attendance</a>
                <a class="attendance-link" style="background-color: #184d03f7; color: #fff; display: flex;" href="staffAttendance.php"><i class="fas fa-user-tie"></i> Staff Attendance</a>
                <a class="attendance-link" style="background-color: #184d03f7; color: #fff; display: flex;" href="viewAttendance.php"><i class="fas fa-eye"></i> View Attendance Records</a>
            </div>
            <div class="recent-activity">
                <div class="recent-activity-title">Recent Attendance Activity</div>
                <ul class="recent-activity-list">
                    <?php if (count($recentAttendance) === 0): ?>
                        <li style="color:#888;">No recent attendance records.</li>
                    <?php else: ?>
                        <?php foreach ($recentAttendance as $rec): ?>
                            <li>
                                <span class="role-badge role-<?= htmlspecialchars($rec['role']) ?>">
                                    <?= ucfirst($rec['role']) ?>
                                </span>
                                <span><b><?= htmlspecialchars($rec['attendedid']) ?></b></span>
                                <span>on <?= htmlspecialchars($rec['date']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="attendance-filter-bar">
                <form method="get" style="display:inline-block; text-align:center;">
                    <label for="date">Select Date: </label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" max="<?= date('Y-m-d') ?>">
                    <button type="submit" class="attendance-link" style="padding:0.4rem 1.2rem; font-size:1rem;">Filter</button>
                </form>
            </div>
            <div class="attendance-tabs">
                <button class="attendance-tab active bg-success text-white" id="tab-teacher" onclick="switchTab('teacher')">Teachers</button>
                <button class="attendance-tab active bg-success text-white" id="tab-staff" onclick="switchTab('staff')">Staff</button>
                <button class="attendance-tab active bg-success text-white" id="tab-student" onclick="switchTab('student')">Students</button>
            </div>
            <div id="section-teacher" class="attendance-list-section" style="display:none;">
                <table class="attendance-list-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($names['teacher'] as $id => $name): ?>
                        <tr>
                            <td><?= htmlspecialchars($id) ?></td>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td>
                                <?php if (in_array($id, $attendanceData['teacher'])): ?>
                                    <span class="present-badge">Present</span>
                                <?php else: ?>
                                    <span class="absent-badge">Absent</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="section-staff" class="attendance-list-section" style="display:none;">
                <table class="attendance-list-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($names['staff'] as $id => $name): ?>
                        <tr>
                            <td><?= htmlspecialchars($id) ?></td>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td>
                                <?php if (in_array($id, $attendanceData['staff'])): ?>
                                    <span class="present-badge">Present</span>
                                <?php else: ?>
                                    <span class="absent-badge">Absent</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="section-student" class="attendance-list-section" style="display:none;">
                <table class="attendance-list-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($names['student'] as $id => $name): ?>
                        <tr>
                            <td><?= htmlspecialchars($id) ?></td>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td>
                                <?php if (in_array($id, $attendanceData['student'])): ?>
                                    <span class="present-badge">Present</span>
                                <?php else: ?>
                                    <span class="absent-badge">Absent</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
