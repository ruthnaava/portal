<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

$student_id = isset($_REQUEST['student_id']) ? $_REQUEST['student_id'] : null;
$course_unit_id = isset($_REQUEST['course_unit_id']) ? $_REQUEST['course_unit_id'] : null;
$semester_id = isset($_REQUEST['semester_id']) ? $_REQUEST['semester_id'] : null;
$grade = null;
$exam_type = null;
$remarks = null;
$valid = false;
if ($student_id && $course_unit_id && $semester_id) {
    $sql = "SELECT g.grade, g.exam_type, g.remarks FROM grades g
            JOIN semester_courses sc ON g.course_unit_id = sc.course_unit_id AND g.semester_id = sc.semester_id
            WHERE g.student_id = ? AND g.course_unit_id = ? AND g.semester_id = ? AND sc.teacher_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $student_id, $course_unit_id, $semester_id, $check);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $grade = $row['grade'];
        $exam_type = $row['exam_type'];
        $remarks = $row['remarks'];
        $valid = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Student</title>
    <link rel="icon" type="image/x-icon" href="../../source/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    
    
    </head>
<body>
    <div class="main-content d-flex flex-column align-items-center justify-content-center min-vh-100" id="mainContent">
        <div class="page-header w-100">
            <h1 class="page-title text-center">Grade Student</h1>
        </div>
        <div class="dashboard-grid w-100 d-flex flex-column align-items-center justify-content-center">
            <div class="stat-card rounded-4 shadow-sm d-flex flex-column align-items-center justify-content-center p-4 w-100" style="max-width:420px;">
                <div class="w-100">
                    <div class="action-bar mb-3 w-100 d-flex justify-content-end">
                        <a href="awardmarks.php" class="btn btn-danger rounded-pill px-">
                            <i class="fas fa-marker"></i> Award Marks & Grades
                        </a>
                    </div>
                    <?php if ($valid): ?>
                    <form action='succeed.php' method="post" class="w-100 d-flex flex-column align-items-center justify-content-center gap-3" style="width:100% !important;">
                        <input type='hidden' name='student_id' value='<?= htmlspecialchars($student_id) ?>' />
                        <input type='hidden' name='course_unit_id' value='<?= htmlspecialchars($course_unit_id) ?>' />
                        <input type='hidden' name='semester_id' value='<?= htmlspecialchars($semester_id) ?>' />
                        <div class="w-100 d-flex flex-column align-items-center">
                            <label class="w-100 text-center mb-2">Grade:</label>
                            <input type='text' name='grade' value='<?= htmlspecialchars($grade) ?>' required class="form-control form-control-sm text-center" style="max-width:220px;">
                        </div>
                        <div class="w-100 d-flex flex-column align-items-center">
                            <label class="w-100 text-center mb-2">Exam Type:</label>
                            <input type='text' name='exam_type' value='<?= htmlspecialchars($exam_type) ?>' required class="form-control form-control-sm text-center" style="max-width:220px;">
                        </div>
                        <div class="w-100 d-flex flex-column align-items-center">
                            <label class="w-100 text-center mb-2">Remarks:</label>
                            <input type='text' name='remarks' value='<?= htmlspecialchars($remarks) ?>' class="form-control form-control-sm text-center" style="max-width:220px;">
                        </div>
                        <div class="d-flex justify-content-center">
                            <input class="btn btn-success btn-sm mb-2 w-auto" type="submit" value="Save Grade" name="save_grade" />
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="stat-card-title text-center" style="color:red;">Invalid student, course unit, or semester for this teacher.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>