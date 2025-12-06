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

// Modernized: List teachers, their course units, and count of students with low grades (C+ or below)
$reportRows = [];
$sql = "SELECT t.name AS teacher, cu.name AS course_unit, s.semester_name, COUNT(g.id) AS num_students
FROM teachers t
JOIN semester_courses sc ON t.id = sc.teacher_id
JOIN course_units cu ON sc.course_unit_id = cu.id
JOIN semesters s ON sc.semester_id = s.id
LEFT JOIN grades g ON g.course_unit_id = cu.id AND g.semester_id = s.id AND g.grade NOT IN ('A+','A','A-','B+','B','B-')
GROUP BY t.id, cu.id, s.id
HAVING num_students > 0
ORDER BY t.name, cu.name, s.semester_name";
$res = $mysqli->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $reportRows[] = $row;
    }
}

// Dashboard stats
$recentStudents = 0;
$recentTeachers = 0;
$recentStaff = 0;
$recentParents = 0;
$recentDays = 30;
$recentDate = date('Y-m-d', strtotime("-$recentDays days"));
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM students WHERE addmissiondate >= '$recentDate'");
if ($res) { $recentStudents = $res->fetch_assoc()['cnt']; }
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM teachers WHERE hiredate >= '$recentDate'");
if ($res) { $recentTeachers = $res->fetch_assoc()['cnt']; }
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM staff WHERE hiredate >= '$recentDate'");
if ($res) { $recentStaff = $res->fetch_assoc()['cnt']; }
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM parents WHERE created_at >= '$recentDate'");
if ($res) { $recentParents = $res->fetch_assoc()['cnt']; }

// Salary paid this month
$salaryMonth = date('Y-m');
$teacherSalary = 0;
$res = $mysqli->query("SELECT SUM(salary) as total FROM teachers");
if ($res) { $teacherSalary = $res->fetch_assoc()['total']; }
$staffSalary = 0;
$res = $mysqli->query("SELECT SUM(salary) as total FROM staff");
if ($res) { $staffSalary = $res->fetch_assoc()['total']; }

// Recent changes (last 5 students/teachers/staff/parents added)
$recentChanges = [];
$res = $mysqli->query("SELECT 'Student' as type, id, name, addmissiondate as date FROM students ORDER BY addmissiondate DESC LIMIT 2");
while ($row = $res && $res->num_rows ? $res->fetch_assoc() : []) { $recentChanges[] = $row; }
$res = $mysqli->query("SELECT 'Teacher' as type, id, name, hiredate as date FROM teachers ORDER BY hiredate DESC LIMIT 2");
while ($row = $res && $res->num_rows ? $res->fetch_assoc() : []) { $recentChanges[] = $row; }
$res = $mysqli->query("SELECT 'Staff' as type, id, name, hiredate as date FROM staff ORDER BY hiredate DESC LIMIT 2");
while ($row = $res && $res->num_rows ? $res->fetch_assoc() : []) { $recentChanges[] = $row; }
$res = $mysqli->query("SELECT 'Parent' as type, id, fathername, created_at as date FROM parents ORDER BY created_at DESC LIMIT 2");
while ($row = $res && $res->num_rows ? $res->fetch_assoc() : []) {
    $row['name'] = $row['fathername'];
    $recentChanges[] = $row;
}
// Sort by date desc
usort($recentChanges, function($a, $b) { return strcmp($b['date'], $a['date']); });
$recentChanges = array_slice($recentChanges, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Evaluation Report - Admin Panel | MIU SCIENCE FACULTY PORTAL</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .report-dashboard {
            margin: 40px 0 0 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 100%;
            width: 100%;
        }
        .report-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .report-table {
            width: 100%; border-collapse: collapse; margin-bottom: 2rem; background: #fff;
        }
        .report-table th, .report-table td {
            padding: 0.7rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left;
        }
        .report-table th { background: #f1f5f9; }
        .report-table tr:last-child td { border-bottom: none; }
        .dashboard-cards { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; }
        .dashboard-card { background: #f1f5f9; border-radius: 10px; padding: 1.2rem 2rem; min-width: 180px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; font-weight: 600; }
        .dashboard-card .icon { font-size: 2rem; margin-bottom: 0.5rem; color: #2563eb; }
        .dashboard-card .count { font-size: 1.5rem; color: #1d4ed8; }
        .recent-changes { margin-bottom: 2rem; background: #f9fafb; border-radius: 10px; padding: 1.2rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .recent-changes-title { font-weight: 600; margin-bottom: 1rem; }
        .recent-changes-list { list-style: none; padding: 0; margin: 0; }
        .recent-changes-list li { padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.7rem; }
        .recent-changes-list li:last-child { border-bottom: none; }
        .type-badge { display: inline-block; padding: 0.2rem 0.7rem; border-radius: 6px; font-size: 0.95rem; font-weight: 500; }
        .type-Student { background: #dbeafe; color: #2563eb; }
        .type-Teacher { background: #fef9c3; color: #b45309; }
        .type-Staff { background: #fce7f3; color: #be185d; }
        .type-Parent { background: #e0e7ff; color: #3730a3; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="report-dashboard text-success bg-light text-align-center">
            <div class="report-title">School Dashboard & Reports</div>
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="count"><?= $recentStudents ?></div>
                    <div>New Students (30)</div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="count"><?= $recentTeachers ?></div>
                    <div>New Teachers (30)</div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class="fas fa-user-tie"></i></div>
                    <div class="count"><?= $recentStaff ?></div>
                    <div>New Staff (30)</div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="count"><?= $recentParents ?></div>
                    <div>New Parents (30)</div>
                </div>
                <div class="dashboard-card">
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="count">shs<?= number_format($teacherSalary + $staffSalary) ?></div>
                    <div>Total Salary (Monthly)</div>
                </div>
            </div>
            <div class="recent-changes">
                <div class="recent-changes-title">Recent Changes</div>
                <ul class="recent-changes-list">
                    <?php if (count($recentChanges) === 0): ?>
                        <li style="color:#888;">No recent changes.</li>
                    <?php else: ?>
                        <?php foreach ($recentChanges as $change): ?>
                            <li>
                                <span class="type-badge type-<?= htmlspecialchars($change['type']) ?>">
                                    <?= htmlspecialchars($change['type']) ?>
                                </span>
                                <span><b><?= htmlspecialchars($change['name']) ?></b> (<?= htmlspecialchars($change['id']) ?>)</span>
                                <span>on <?= htmlspecialchars($change['date']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="report-title">Teacher Evaluation Report</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Course Unit</th>
                        <th>Semester</th>
                        <th># of Students (Low Grades)</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($reportRows) === 0): ?>
                    <tr><td colspan="4" style="text-align:center; color:#888;">No records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($reportRows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['teacher']) ?></td>
                            <td><?= htmlspecialchars($row['course_unit']) ?></td>
                            <td><?= htmlspecialchars($row['semester_name']) ?></td>
                            <td><?= htmlspecialchars($row['num_students']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
