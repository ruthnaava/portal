<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

// Fetch teacher's course units
$stmt = $mysqli->prepare("SELECT sc.id as semester_course_id, cu.name as course_unit, cu.code, sc.semester_id FROM semester_courses sc JOIN course_units cu ON sc.course_unit_id = cu.id WHERE sc.teacher_id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$courses = $stmt->get_result();

$selected_course = isset($_GET['course']) ? $_GET['course'] : null;
$selected_student = isset($_GET['student']) ? $_GET['student'] : null;
$selected_semester_id = null;
$students = [];

if ($selected_course) {
    $stmt_info = $mysqli->prepare("SELECT semester_id, course_unit_id FROM semester_courses WHERE id = ?");
    $stmt_info->bind_param("i", $selected_course);
    $stmt_info->execute();
    $stmt_info->bind_result($selected_semester_id, $selected_course_unit_id);
    $stmt_info->fetch();
    $stmt_info->close();
    // Fetch students for this course unit
    $stmt2 = $mysqli->prepare("SELECT s.id, s.name FROM student_courses scs JOIN students s ON scs.student_id = s.id WHERE scs.semester_course_id = ?");
    $stmt2->bind_param("i", $selected_course);
    $stmt2->execute();
    $students = $stmt2->get_result();
}

// Handle report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['course_unit_id'], $_POST['semester_id'], $_POST['report'])) {
    $student_id = $_POST['student_id'];
    $course_unit_id = $_POST['course_unit_id'];
    $semester_id = $_POST['semester_id'];
    $report = trim($_POST['report']);
    $stmt3 = $mysqli->prepare("INSERT INTO report (studentid, teacherid, message, course_unit_id, semester_id) VALUES (?, ?, ?, ?, ?)");
    $stmt3->bind_param("sssss", $student_id, $check, $report, $course_unit_id, $semester_id);
    $stmt3->execute();
    header("Location: report.php?course=$selected_course");
    exit;
}

// Fetch all reports by this teacher
$reports = [];
$stmt4 = $mysqli->prepare("SELECT r.reportid, r.studentid, s.name as student_name, r.course_unit_id, cu.name as course_unit, r.semester_id, r.message FROM report r JOIN students s ON r.studentid = s.id JOIN course_units cu ON r.course_unit_id = cu.id WHERE r.teacherid = ? ORDER BY r.reportid DESC");
$stmt4->bind_param("s", $check);
$stmt4->execute();
$reports = $stmt4->get_result();

// Download report as text
if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $report_id = $_GET['download'];
    $stmt_dl = $mysqli->prepare("SELECT r.message, s.name as student_name, cu.name as course_unit FROM report r JOIN students s ON r.studentid = s.id JOIN course_units cu ON r.course_unit_id = cu.id WHERE r.reportid = ? AND r.teacherid = ?");
    $stmt_dl->bind_param("is", $report_id, $check);
    $stmt_dl->execute();
    $stmt_dl->bind_result($message, $student_name, $course_unit);
    if ($stmt_dl->fetch()) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="report_' . $report_id . '.txt"');
        echo "Report for $student_name\nCourse Unit: $course_unit\n\n$message";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>
<body>
<div class="main-content bg-light min-vh-70 p-4 text-dark" id="mainContent">
    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-2">
        <div class="page-header w-100 text-center d-flex flex-column align-items-center justify-content-center ">
            <h1 class="page-title text-center text-success py-2" style="text-align: center; border-radius:1rem; width:fit-content; margin:0 auto;">Student Report</h1>
        </div>
        <div class="dashboard-grid w-100 d-flex flex-column align-items-center justify-content-center">
            <div class="stat-card full-width rounded-4 shadow-sm d-flex flex-column align-items-center justify-content-center p-4 w-100" style="max-width:900px;">
                <form method="get" class="w-100 d-flex flex-column align-items-center justify-content-center mb-3" style="max-width:350px;">
                    <label for="course" class="form-label w-100 text-center">Select Course Unit:</label>
                    <select name="course" id="course" onchange="this.form.submit()" class="form-select form-select-sm mb-2" style="max-width:250px;">
                        <option value="">-- Select Course Unit --</option>
                        <?php $courses->data_seek(0); while ($row = $courses->fetch_assoc()): ?>
                            <option value="<?php echo $row['semester_course_id']; ?>" <?php if ($selected_course == $row['semester_course_id']) echo 'selected'; ?> >
                                <?php echo htmlspecialchars($row['course_unit']) . " (" . htmlspecialchars($row['code']) . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
                <?php if ($selected_course && $students && $students instanceof mysqli_result): ?>
                <form method="post" action="" class="w-100 d-flex flex-column align-items-center justify-content-center gap-3" style="max-width:400px;">
                    <input type="hidden" name="course_unit_id" value="<?php echo $selected_course_unit_id; ?>">
                    <input type="hidden" name="semester_id" value="<?php echo $selected_semester_id; ?>">
                    <label for="student_id" class="form-label w-100 text-center">Select Student:</label>
                    <select name="student_id" id="student_id" class="form-select form-select-sm mb-2" style="max-width:250px;" required>
                        <option value="">-- Select Student --</option>
                        <?php while ($s = $students->fetch_assoc()): ?>
                            <option value="<?php echo $s['id']; ?>" <?php if ($selected_student == $s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <label for="report" class="form-label w-100 text-center">Report:</label>
                    <textarea name="report" id="report" rows="5" class="form-control form-control-sm mb-2" style="max-width:250px;" required></textarea>
                    <div class="d-flex justify-content-center w-100">
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Report</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="dashboard-grid w-100 d-flex flex-column align-items-center justify-content-center">
            <div class="stat-card full-width rounded-4 shadow-sm d-flex flex-column align-items-center justify-content-center p-4 w-100" style="max-width:900px;">
                <h2 class="text-center mb-3" style="color:success;">My Reports</h2>
                <div class="table-responsive w-100">
                    <table class="modern-table" style="width:100%; background-color:#fff;">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Student</th>
                                <th>Course Unit</th>
                                <th>Report</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $reports->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r['reportid']); ?></td>
                                <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($r['course_unit']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($r['message'])); ?></td>
                                <td><a href="report.php?download=<?php echo $r['reportid']; ?>" class="btn btn-success btn-sm rounded-pill"><i class="fas fa-download"></i> Download</a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </body>
