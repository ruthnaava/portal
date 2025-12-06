<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $attendance = $_POST['attendance'];
    $date = date('Y-m-d');
    $success = true;
    foreach ($attendance as $student_id) {
        // Only allow attendance for students in teacher's courses
        $stmt = $mysqli->prepare("SELECT c.id FROM course c JOIN student s ON c.classid = s.classid WHERE s.id = ? AND c.teacherid = ?");
        $stmt->bind_param("ss", $student_id, $check);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $insert = $mysqli->prepare("INSERT INTO attendance (date, attendedid) VALUES (?, ?)");
            $insert->bind_param("ss", $date, $student_id);
            $insert->execute();
        } else {
            $success = false;
        }
    }
} else {
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Submission</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>
<body>
    <div class="main-content" id="mainContent">
        <div class="page-header">
            <h1 class="page-title">Attendance Submission</h1>
        </div>
        <div class="dashboard-grid">
            <div class="stat-card">
                <?php if ($success): ?>
                    <div class="stat-card-title">Attendance Submitted Successfully.</div>
                    <div class="stat-card-desc"><a href="index.php" class="menu-link">Go to Dashboard</a></div>
                <?php else: ?>
                    <div class="stat-card-title" style="color:red;">Attendance Submission Failed or Invalid Request.</div>
                    <div class="stat-card-desc"><a href="attendance.php" class="menu-link">Try Again</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>