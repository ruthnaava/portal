<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

// Fetch teacher's course units
$stmt = $mysqli->prepare("SELECT sc.id as semester_course_id, cu.name as course_unit, cu.code FROM semester_courses sc JOIN course_units cu ON sc.course_unit_id = cu.id WHERE sc.teacher_id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$courses = $stmt->get_result();

// Handle course selection and date
$selected_course = isset($_GET['course']) ? $_GET['course'] : null;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['semester_course_id'], $_POST['date'], $_POST['attendance'])) {
    $semester_course_id = $_POST['semester_course_id'];
    $date = $_POST['date'];
    foreach ($_POST['attendance'] as $student_id => $status) {
        // Remove any existing attendance for this student/date/course
        $stmt_del = $mysqli->prepare("DELETE FROM attendance WHERE attendedid = ? AND date = ? AND semester_course_id = ? AND role = 'student'");
        $stmt_del->bind_param("ssi", $student_id, $date, $semester_course_id);
        $stmt_del->execute();
        // Insert new record
        $stmt_ins = $mysqli->prepare("INSERT INTO attendance (date, attendedid, role, semester_course_id) VALUES (?, ?, 'student', ?)");
        if ($status === 'present') {
            $stmt_ins->bind_param("ssi", $date, $student_id, $semester_course_id);
            $stmt_ins->execute();
        }
    }
    header("Location: attendance.php?course=$semester_course_id&date=$date");
    exit;
}

// Fetch students for selected course
$students = [];
if ($selected_course) {
    $stmt2 = $mysqli->prepare("SELECT s.id, s.name, s.email FROM student_courses scs JOIN students s ON scs.student_id = s.id WHERE scs.semester_course_id = ?");
    $stmt2->bind_param("i", $selected_course);
    $stmt2->execute();
    $students = $stmt2->get_result();
}

// Fetch attendance for selected date
$attendance = [];
if ($selected_course && $selected_date) {
    $stmt3 = $mysqli->prepare("SELECT attendedid FROM attendance WHERE semester_course_id = ? AND date = ? AND role = 'student'");
    $stmt3->bind_param("is", $selected_course, $selected_date);
    $stmt3->execute();
    $res = $stmt3->get_result();
    while ($row = $res->fetch_assoc()) {
        $attendance[$row['attendedid']] = true;
    }
}

// Fetch previous attendance records for this course
$history = [];
if ($selected_course) {
    $stmt4 = $mysqli->prepare("SELECT date, attendedid FROM attendance WHERE semester_course_id = ? AND role = 'student' ORDER BY date DESC");
    $stmt4->bind_param("i", $selected_course);
    $stmt4->execute();
    $res = $stmt4->get_result();
    while ($row = $res->fetch_assoc()) {
        $history[$row['date']][] = $row['attendedid'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="main-content" id="mainContent">
        <div class="page-header">
            <h1 class="page-title text-center text-success">Take Attendance</h1>
        </div>
        <div class="dashboard-grid">
            <div class="stat-card full-width bg-white text-dark rounded-4 shadow-sm p-4 d-flex flex-column align-items-center justify-content-center">
                <form method="get" class="w-100 d-flex flex-column align-items-center justify-content-center mb-3" style="max-width:350px;">
                    <label for="course" class="form-label w-100 text-center text-success">Select Course Unit:</label>
                    <select name="course" id="course" onchange="this.form.submit()" class="form-select form-select-sm mb-2" style="max-width:250px;">
                        <option value="">-- Select Course Unit --</option>
                        <?php $courses->data_seek(0); while ($row = $courses->fetch_assoc()): ?>
                            <option value="<?php echo $row['semester_course_id']; ?>" <?php if ($selected_course == $row['semester_course_id']) echo 'selected'; ?> >
                                <?php echo htmlspecialchars($row['course_unit']) . " (" . htmlspecialchars($row['code']) . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label for="date" class="form-label w-100 text-center text-success">Date:</label>
                    <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($selected_date); ?>" class="form-control form-control-sm mb-2" style="max-width:250px;" onchange="this.form.submit()">
                </form>
                <?php if ($selected_course): ?>
                <form method="post" action="" class="w-100 d-flex flex-column align-items-center justify-content-center">
                    <input type="hidden" name="semester_course_id" value="<?php echo $selected_course; ?>">
                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
                    <div class="table-responsive w-100">
                        <table class="table table-dark table-bordered table-striped align-middle text-center" style="background:#b91010;">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Present</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($students && $students instanceof mysqli_result): while ($row = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><input type="checkbox" name="attendance[<?php echo $row['id']; ?>]" value="present" <?php if (isset($attendance[$row['id']])) echo 'checked'; ?> style="width:18px;height:18px;"></td>
                                </tr>
                            <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center w-100">
                        <button type="submit" class="btn btn-danger rounded-pill px-4 mt-3">Save Attendance</button>
                    </div>
                </form>
                <h3 class="mt-4 text-center">Attendance History</h3>
                <form method="get" class="w-100 d-flex flex-column align-items-center justify-content-center mb-3" style="max-width:350px;">
                    <input type="hidden" name="course" value="<?php echo htmlspecialchars($selected_course); ?>">
                    <label for="history_date" class="form-label w-100 text-center">Filter by Date:</label>
                    <input type="date" name="history_date" id="history_date" value="<?php echo isset($_GET['history_date']) ? htmlspecialchars($_GET['history_date']) : ''; ?>" class="form-control form-control-sm mb-2" style="max-width:250px;">
                    <div class="d-flex justify-content-center w-100">
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Filter</button>
                    </div>
                </form>
                <div class="table-responsive w-100">
                    <table class="table table-dark table-bordered table-striped align-middle text-center" style="background:#b91010;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Present Student IDs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $filter_date = isset($_GET['history_date']) ? $_GET['history_date'] : null;
                            foreach ($history as $date => $ids): 
                                if ($filter_date && $date !== $filter_date) continue;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($date); ?></td>
                                <td><?php echo htmlspecialchars(implode(', ', $ids)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
