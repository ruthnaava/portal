<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

// Fetch teacher's course units
$stmt = $mysqli->prepare("SELECT sc.id as semester_course_id, sc.semester_id, cu.id as course_unit_id, cu.name as course_unit, cu.code FROM semester_courses sc JOIN course_units cu ON sc.course_unit_id = cu.id WHERE sc.teacher_id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$courses = $stmt->get_result();

// Handle course selection
$selected_course = isset($_GET['course']) ? $_GET['course'] : null;
$selected_semester_id = null;
$selected_course_unit_id = null;
$students = [];
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : null;

if ($selected_course) {
    // Get semester_id and course_unit_id for this semester_course_id
    $stmt_info = $mysqli->prepare("SELECT semester_id, course_unit_id FROM semester_courses WHERE id = ?");
    $stmt_info->bind_param("i", $selected_course);
    $stmt_info->execute();
    $stmt_info->bind_result($selected_semester_id, $selected_course_unit_id);
    $stmt_info->fetch();
    $stmt_info->close();
}

// Handle delete -> convert to request (non-destructive)
if (isset($_GET['delete']) && $selected_course && $selected_semester_id && $selected_course_unit_id) {
    $delete_id = intval($_GET['delete']);
    // Fetch grade row
    $stmt_g = $mysqli->prepare("SELECT student_id, course_unit_id, semester_id FROM grades WHERE id = ? LIMIT 1");
    $stmt_g->bind_param('i', $delete_id);
    $stmt_g->execute();
    $rg = $stmt_g->get_result();
    $grade_row = $rg ? $rg->fetch_assoc() : null;
    if ($grade_row && intval($grade_row['course_unit_id']) === intval($selected_course_unit_id) && intval($grade_row['semester_id']) === intval($selected_semester_id)) {
        // Verify teacher owns the semester_course
        $stmt_sc = $mysqli->prepare("SELECT teacher_id FROM semester_courses WHERE id = ? LIMIT 1");
        $stmt_sc->bind_param('i', $selected_course);
        $stmt_sc->execute();
        $rsc = $stmt_sc->get_result();
        $sc_row = $rsc ? $rsc->fetch_assoc() : null;
        if ($sc_row && $sc_row['teacher_id'] == $check) {
            $studentid = $grade_row['student_id'];
            $teacherid = $check;
            $msg = 'Teacher requested deletion of grade id ' . $delete_id . ' for student ' . $studentid . ' (course unit ' . $selected_course_unit_id . ', semester ' . $selected_semester_id . ')';
            $ins = $mysqli->prepare("INSERT INTO report (studentid, teacherid, message, course_unit_id, semester_id) VALUES (?, ?, ?, ?, ?)");
            if ($ins) {
                $ins->bind_param('sssss', $studentid, $teacherid, $msg, $selected_course_unit_id, $selected_semester_id);
                $ins->execute();
            }
        }
    }
    header("Location: awardmarks.php?course=" . $selected_course . "&msg=requested");
    exit;
}

// Grade calculation function
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

// Handle update or add
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['student_id'], $_POST['mark'], $_POST['semester_course_id'], $_POST['semester_id'], $_POST['course_unit_id'], $_POST['exam_type'], $_POST['remarks'])
) {
    $student_id = $_POST['student_id'];
    $mark = $_POST['mark'];
    $grade = calculate_grade($mark);
    $semester_course_id = $_POST['semester_course_id'];
    $semester_id = $_POST['semester_id'];
    $course_unit_id = $_POST['course_unit_id'];
    $exam_type = $_POST['exam_type'];
    $remarks = $_POST['remarks'];
    $grade_id = isset($_POST['grade_id']) ? $_POST['grade_id'] : null;
    if ($grade_id) {
        // Update
        $stmt3 = $mysqli->prepare("UPDATE grades SET mark=?, grade=?, exam_type=?, remarks=? WHERE id=?");
        $stmt3->bind_param("isssi", $mark, $grade, $exam_type, $remarks, $grade_id);
        $stmt3->execute();
    } else {
        // Insert
        $stmt3 = $mysqli->prepare("INSERT INTO grades (student_id, mark, grade, course_unit_id, semester_id, exam_type, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt3->bind_param("sisssss", $student_id, $mark, $grade, $course_unit_id, $semester_id, $exam_type, $remarks);
        $stmt3->execute();
    }
    header("Location: awardmarks.php?course=" . $semester_course_id);
    exit;
}

// Fetch all students enrolled in the selected course, and their grades (if any)
if ($selected_course && $selected_semester_id && $selected_course_unit_id) {
    $stmt2 = $mysqli->prepare("SELECT s.id, s.name, s.email, g.id as grade_id, g.mark, g.grade, g.exam_type, g.remarks FROM student_courses scs JOIN students s ON scs.student_id = s.id LEFT JOIN grades g ON g.student_id = s.id AND g.course_unit_id = ? AND g.semester_id = ? WHERE scs.semester_course_id = ?");
    $stmt2->bind_param("ssi", $selected_course_unit_id, $selected_semester_id, $selected_course);
    $stmt2->execute();
    $students = $stmt2->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Award Marks & Grades</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="main-content d-flex flex-column align-items-center justify-content-center" id="mainContent" style="min-height: 70vh;">
        <div class="page-header w-100 d-flex flex-column align-items-center justify-content-center" style="margin-bottom: 1.5rem;">
            <h1 class="page-title text-center" style="font-weight: 700; color: #198754; letter-spacing: 1px;">Award Marks & Grades</h1>
        </div>
        <?php
        // Flash messages (show after redirects)
        if (isset($_GET['msg'])) {
            $m = $_GET['msg'];
            if ($m === 'updated') {
                echo '<div class="alert alert-success w-100" role="alert" style="max-width:1100px;margin:0 auto 1rem;">Grade updated successfully.</div>';
            } elseif ($m === 'requested') {
                echo '<div class="alert alert-info w-100" role="alert" style="max-width:1100px;margin:0 auto 1rem;">Request submitted for admin review.</div>';
            } elseif ($m === 'deleted') {
                echo '<div class="alert alert-warning w-100" role="alert" style="max-width:1100px;margin:0 auto 1rem;">Item deleted.</div>';
            }
        }
        ?>
        <div class="dashboard-grid w-100 d-flex flex-column align-items-center justify-content-center text-success" style="max-width:1200px;">
            <div class="stat-card full-width">
                <div class="stat-card-header d-flex flex-column align-items-center justify-content-center mb-3">
                    <div class="stat-card-title text-success">Select Course Unit</div>
                    <div class="stat-card-icon" style="background-color: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <form method="get" style="margin-bottom: 1rem; text-align: center; width:100% !important; color: #198754;">
                    <select name="course" onchange="this.form.submit()" class="modern-input">
                        <option value="">-- Select Course Unit --</option>
                        <?php while ($row = $courses->fetch_assoc()): ?>
                            <option value="<?php echo $row['semester_course_id']; ?>" <?php if ($selected_course == $row['semester_course_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($row['course_unit']) . " (" . htmlspecialchars($row['code']) . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
                <?php if ($selected_course && $selected_semester_id && $selected_course_unit_id): ?>
                <div style="overflow-x:auto; table-layout: auto;">
                <table class="modern-table sortable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mark</th>
                            <th>Grade</th>
                            <th>Exam Type</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['mark']); ?></td>
                                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($row['exam_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td>
                                        <?php if ($row['grade_id']): ?>
                                                <a href="editGrade.php?grade_id=<?php echo $row['grade_id']; ?>&course=<?php echo $selected_course; ?>" class="action-btn btn-edit">Edit</a>
                                                <a href="awardmarks.php?course=<?php echo $selected_course; ?>&delete=<?php echo $row['grade_id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this grade?');">Delete</a>
                                            <?php else: ?>
                                                <a href="awardmarks.php?course=<?php echo $selected_course; ?>&edit=new&student=<?php echo $row['id']; ?>" class="action-btn btn-manage">Award</a>
                                            <?php endif; ?>
                                    </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    // Simple sort for table columns
    document.querySelectorAll('.sortable th').forEach(header => {
        header.addEventListener('click', () => {
            const table = header.closest('table');
            const tbody = table.querySelector('tbody');
            Array.from(tbody.querySelectorAll('tr'))
                .sort((a, b) => {
                    const idx = Array.from(header.parentNode.children).indexOf(header);
                    return a.children[idx].innerText.localeCompare(b.children[idx].innerText, undefined, {numeric: true});
                })
                .forEach(tr => tbody.appendChild(tr));
        });
    });
    </script>
</body>
</html>
