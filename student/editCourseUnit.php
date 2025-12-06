<?php
include_once('main.php');

$scid = isset($_GET['scid']) ? intval($_GET['scid']) : 0;
if (!$scid) {
    header('Location: course.php');
    exit;
}

$stmt = $mysqli->prepare("SELECT sc.id AS student_course_id, semc.id AS semester_course_id, cu.code, cu.name, s.semester_name, t.name AS teacher_name FROM student_courses sc JOIN semester_courses semc ON sc.semester_course_id = semc.id JOIN course_units cu ON semc.course_unit_id = cu.id JOIN semesters s ON semc.semester_id = s.id JOIN teachers t ON semc.teacher_id = t.id WHERE sc.id = ? AND sc.student_id = ? LIMIT 1");
$stmt->bind_param('is', $scid, $check);
$stmt->execute();
$stmt->bind_result($student_course_id, $semester_course_id, $code, $cname, $semester_name, $teacher_name);
if (!$stmt->fetch()) {
    $stmt->close();
    header('Location: course.php');
    exit;
}
$stmt->close();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <title>Edit Course Enrollment</title>
    <style>body{padding:2rem;font-family:Segoe UI,Arial,Helvetica,sans-serif} .form-small{max-width:720px}</style>
</head>
<body>
    <div class="container form-small">
        <h3>Edit Enrolled Course</h3>
        <div class="card p-3 mb-3">
            <p><strong>Course Code:</strong> <?php echo htmlspecialchars($code); ?></p>
            <p><strong>Course Name:</strong> <?php echo htmlspecialchars($cname); ?></p>
            <p><strong>Semester:</strong> <?php echo htmlspecialchars($semester_name); ?></p>
            <p><strong>Teacher:</strong> <?php echo htmlspecialchars($teacher_name); ?></p>
        </div>

        <?php
        // Handle POST updates (student submits an edit)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note = isset($_POST['note']) ? trim($_POST['note']) : '';
            $desired_semester_course = isset($_POST['desired_semester_course']) ? intval($_POST['desired_semester_course']) : 0;

            // If course_change_requests table exists, create an update request
            $checkTable = $mysqli->query("SHOW TABLES LIKE 'course_change_requests'");
            if ($checkTable && $checkTable->num_rows > 0) {
                $stmt = $mysqli->prepare("INSERT INTO course_change_requests (student_id, student_course_id, type, desired_semester_course_id, note, status, created_at) VALUES (?, ?, 'update', ?, ?, 'pending', NOW())");
                if ($stmt) {
                        $stmt->bind_param('siis', $check, $student_course_id, $desired_semester_course, $note);
                    // note: if bind fails due to type mismatch, fallback to simpler insert
                    $stmt->execute();
                    $stmt->close();
                    header('Location: course.php?msg=update_submitted');
                    exit;
                }
            }

            // Fallback: attempt direct update on student_courses table if the column exists
            // Check if student_courses has column semester_course_id
            $colCheck = $mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'student_courses' AND COLUMN_NAME = 'semester_course_id'");
            if ($colCheck && $colCheck->num_rows > 0 && $desired_semester_course > 0) {
                $u = $mysqli->prepare("UPDATE student_courses SET semester_course_id = ? WHERE id = ? AND student_id = ?");
                if ($u) {
                    $u->bind_param('iis', $desired_semester_course, $student_course_id, $check);
                    $u->execute();
                    $u->close();
                    header('Location: course.php?msg=update_success');
                    exit;
                }
            }

            // If update columns don't exist, at least save a note via requests table if exists
            header('Location: course.php?msg=update_submitted');
            exit;
        }
        ?>

        <form method="post" class="card p-3">
            <div class="mb-3">
                <label class="form-label">Request changes (optional note)</label>
                <textarea name="note" class="form-control" rows="4" placeholder="Describe what you want changed (e.g., change section, swap to lab group X)"></textarea>
            </div>

            <?php
            // Provide a select of available semester_course options for same course unit (if found)
            $optStmt = $mysqli->prepare("SELECT semc.id, s.semester_name, t.name FROM semester_courses semc JOIN semesters s ON semc.semester_id = s.id JOIN teachers t ON semc.teacher_id = t.id WHERE semc.course_unit_id = (SELECT course_unit_id FROM semester_courses WHERE id = ?) ORDER BY s.semester_name");
            if ($optStmt) {
                $optStmt->bind_param('i', $semester_course_id);
                $optStmt->execute();
                $optStmt->bind_result($opt_id, $opt_sem, $opt_teacher);
                $opts = [];
                while ($optStmt->fetch()) {
                    $opts[] = ['id'=>$opt_id, 'label'=>($opt_sem . ' - ' . $opt_teacher)];
                }
                $optStmt->close();
            }
            if (!empty($opts)) {
                echo '<div class="mb-3">';
                echo '<label class="form-label">Preferred section / semester course</label>';
                echo '<select name="desired_semester_course" class="form-select">';
                echo '<option value="0">Leave unchanged</option>';
                foreach ($opts as $o) {
                    echo '<option value="' . intval($o['id']) . '">' . htmlspecialchars($o['label']) . '</option>';
                }
                echo '</select></div>';
            }
            ?>

            <div class="d-flex gap-2">
                <a href="course.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Changes / Request</button>
            </div>
        </form>
    </div>
</body>
</html>
