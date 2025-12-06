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

// Fetch exam schedules (current and upcoming)
$examSchedules = [];
$sql = "SELECT es.id, es.examdate, es.time, cu.name AS course_name, cu.code AS course_code, s.semester_name
        FROM exam_schedule es
        JOIN course_units cu ON es.course_unit_id = cu.id
        JOIN semesters s ON es.semester_id = s.id
        WHERE es.examdate >= CURDATE()
        ORDER BY es.examdate ASC, es.time ASC";
$res = $mysqli->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $examSchedules[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Schedule - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .exam-dashboard {
            margin: 40px 0 0 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 100%;
            width: 100%;
        }
        .exam-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .exam-links {
            display: flex; gap: 1.2rem; margin-bottom: 2rem; flex-wrap: wrap;
        }
        .exam-link {
            display: inline-block; background: #2563eb; color: #fff; border-radius: 6px;
            padding: 0.8rem 2rem; font-size: 1.1rem; font-weight: 600; text-decoration: none;
            transition: background 0.2s;
        }
        .exam-link:hover { background: #1d4ed8; }
        .exam-table {
            width: 100%; border-collapse: collapse; margin-bottom: 2rem; background: #fff;
        }
        .exam-table th, .exam-table td {
            padding: 0.7rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .exam-table th { background: #f1f5f9; }
        .no-exams { color: #888; text-align: center; padding: 2rem 0; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="exam-dashboard">
            <div class="exam-title text-success text-uppercase">Exam Schedule Management</div>
            <div class="exam-links mb-4 d-flex flex-wrap gap-3 ">
                <a class="exam-link" style="background-color: #184d03f7; color: #fff;" href="createExamSchedule.php"><i class="fas fa-plus"></i> Create Exam Schedule</a>
                <a class="exam-link" style="background-color: #184d03f7; color: #fff;" href="updateExamSchedule.php"><i class="fas fa-edit"></i> Update Exam Schedule</a>
            </div>
            <h3 style="margin-bottom:1rem; text-transform: uppercase; color: #184d03f7;">Upcoming & Current Exam Schedules</h3>
            <table class="exam-table mb-5 text-success">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Course</th>
                        <th>Course Code</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($examSchedules) === 0): ?>
                    <tr><td colspan="6" class="no-exams">No upcoming or current exam schedules found.</td></tr>
                <?php else: ?>
                    <?php foreach ($examSchedules as $exam): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['id']) ?></td>
                            <td><?= htmlspecialchars($exam['examdate']) ?></td>
                            <td><?= htmlspecialchars($exam['time']) ?></td>
                            <td><?= htmlspecialchars($exam['course_name']) ?></td>
                            <td><?= htmlspecialchars($exam['course_code']) ?></td>
                            <td><?= htmlspecialchars($exam['semester_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
