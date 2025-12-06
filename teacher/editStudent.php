<?php
include_once('main.php');

$student_id = isset($_GET['id']) ? $_GET['id'] : null;
$msg = '';
if (!$student_id) {
    header('Location: mystudents.php'); exit;
}

// Verify teacher owns at least one enrollment for this student
$stmt = $mysqli->prepare("SELECT sc.id FROM student_courses scs JOIN semester_courses sc ON scs.semester_course_id = sc.id WHERE scs.student_id = ? AND sc.teacher_id = ? LIMIT 1");
$stmt->bind_param('ss', $student_id, $check);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows == 0) {
    $msg = 'You are not authorized to edit this student.';
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email']) && empty($msg)) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $up = $mysqli->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
    $up->bind_param('sss', $name, $email, $student_id);
    if ($up->execute()) {
        header('Location: mystudents.php?msg=updated'); exit;
    } else {
        $msg = 'Failed to update student.';
    }
}

$student = null;
$stmt2 = $mysqli->prepare("SELECT id, name, email FROM students WHERE id = ? LIMIT 1");
$stmt2->bind_param('s', $student_id);
$stmt2->execute();
$r = $stmt2->get_result();
if ($r) $student = $r->fetch_assoc();

// include chrome after logic so header() redirects work
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Student</title>
    <link rel="stylesheet" href="css/teacher.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <style>.form-card{max-width:640px;margin:2rem auto;padding:1.2rem;border-radius:12px;background:#fff;box-shadow:0 8px 26px rgba(0,0,0,0.06)}.form-card h2{color:#198754}</style>
</head>
<body>
    <div class="main-content">
        <div class="form-card">
            <h2>Edit Student</h2>
            <?php if ($msg): ?><div class="alert alert-warning"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
            <?php if ($student): ?>
            <form method="post">
                <div class="form-section">
                    <label>Name</label>
                    <input type="text" name="name" class="modern-input" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    <label>Email</label>
                    <input type="email" name="email" class="modern-input" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                    <div style="display:flex;gap:0.6rem;justify-content:flex-end;margin-top:0.6rem;">
                        <a href="mystudents.php" class="modern-btn">Cancel</a>
                        <button type="submit" class="modern-btn">Save</button>
                    </div>
                </div>
            </form>
            <?php else: ?>
                <div class="muted">Student not found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
