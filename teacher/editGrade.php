<?php
// editGrade.php - simple edit form for a grade row
include_once('main.php');

$grade_id = isset($_GET['grade_id']) ? intval($_GET['grade_id']) : (isset($_POST['grade_id']) ? intval($_POST['grade_id']) : null);
$course = isset($_GET['course']) ? intval($_GET['course']) : (isset($_POST['semester_course_id']) ? intval($_POST['semester_course_id']) : null);

if (!$grade_id) {
    header('Location: awardmarks.php' . ($course ? '?course='.$course : '')); exit;
}

// Helper grade calculation (copied from awardmarks.php) so this page can compute grade letters
function calculate_grade($mark) {
    if ($mark === null || $mark === '') return '';
    if ($mark >= 80) return 'A+';
    if ($mark >= 75) return 'A';
    if ($mark >= 70) return 'A-';
    if ($mark >= 65) return 'B+';
    if ($mark >= 60) return 'B';
    if ($mark >= 55) return 'B-';
    if ($mark >= 50) return 'C+';
    if ($mark >= 45) return 'C';
    if ($mark >= 40) return 'D';
    return 'F';
}

// Fetch grade and related info
$stmt = $mysqli->prepare("SELECT g.*, s.name as student_name, sc.teacher_id, sc.course_unit_id, sc.semester_id FROM grades g JOIN students s ON g.student_id = s.id JOIN semester_courses sc ON sc.id = ? WHERE g.id = ? LIMIT 1");
$stmt->bind_param('ii', $course, $grade_id);
$stmt->execute();
$res = $stmt->get_result();
$grade = $res ? $res->fetch_assoc() : null;

// If not found or teacher doesn't own course, redirect
if (!$grade || $grade['teacher_id'] != $check) {
    header('Location: awardmarks.php' . ($course ? '?course='.$course : '')); exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark'], $_POST['exam_type'], $_POST['remarks'])) {
    $mark = intval($_POST['mark']);
    $exam_type = trim($_POST['exam_type']);
    $remarks = trim($_POST['remarks']);
    $grade_calc = calculate_grade($mark);
    $up = $mysqli->prepare("UPDATE grades SET mark = ?, grade = ?, exam_type = ?, remarks = ? WHERE id = ?");
    $up->bind_param('isssi', $mark, $grade_calc, $exam_type, $remarks, $grade_id);
    if ($up->execute()) {
        header('Location: awardmarks.php?course=' . $course . '&msg=updated'); exit;
    } else {
        $msg = 'Failed to update grade.';
    }
}

// include chrome
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Grade</title>
    <link rel="stylesheet" href="css/teacher.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>
<body>
    <div class="main-content">
        <div class="form-card">
            <h2>Edit Grade for <?php echo htmlspecialchars($grade['student_name']); ?></h2>
            <?php if ($msg): ?><div class="alert alert-warning"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
            <form method="post">
                <input type="hidden" name="grade_id" value="<?php echo $grade_id; ?>">
                <div class="form-grid" style="grid-template-columns:120px 1fr;gap:0.8rem;align-items:center;">
                    <div>
                        <label>Mark</label>
                        <input type="number" name="mark" value="<?php echo htmlspecialchars($grade['mark']); ?>" min="0" max="100" class="modern-input">
                    </div>
                    <div>
                        <label>Exam Type</label>
                        <input type="text" name="exam_type" value="<?php echo htmlspecialchars($grade['exam_type']); ?>" class="modern-input">
                    </div>
                    <div class="full-row">
                        <label>Remarks</label>
                        <input type="text" name="remarks" value="<?php echo htmlspecialchars($grade['remarks']); ?>" class="modern-input">
                    </div>
                    <div class="full-row" style="display:flex;justify-content:flex-end;gap:0.6rem;">
                        <a href="awardmarks.php?course=<?php echo $course; ?>" class="modern-btn danger">Cancel</a>
                        <button type="submit" class="modern-btn">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
