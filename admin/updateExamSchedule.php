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

// Fetch exam schedules for update search
$examToEdit = null;
$successMsg = $errorMsg = '';
$courseUnits = [];
$semesters = [];
$res = $mysqli->query("SELECT id, name, code FROM course_units ORDER BY name");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $courseUnits[] = $row;
    }
}
$res = $mysqli->query("SELECT id, semester_name FROM semesters ORDER BY semester_name");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $semesters[] = $row;
    }
}

if (isset($_GET['id']) && $_GET['id']) {
    $id = $mysqli->real_escape_string($_GET['id']);
    $res = $mysqli->query("SELECT * FROM exam_schedule WHERE id = '$id'");
    if ($res && $res->num_rows > 0) {
        $examToEdit = $res->fetch_assoc();
    } else {
        $errorMsg = "Exam schedule not found.";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = trim($_POST['id']);
    $examDate = $_POST['examDate'];
    $examTime = $_POST['examTime'];
    $courseUnitId = $_POST['courseUnitId'];
    $semesterId = $_POST['semesterId'];
    if ($id && $examDate && $examTime && $courseUnitId && $semesterId) {
        $stmt = $mysqli->prepare("UPDATE exam_schedule SET examdate=?, time=?, course_unit_id=?, semester_id=? WHERE id=?");
        $stmt->bind_param("sssss", $examDate, $examTime, $courseUnitId, $semesterId, $id);
        if ($stmt->execute()) {
            $successMsg = "Exam schedule updated successfully.";
            // Refresh data
            $res = $mysqli->query("SELECT * FROM exam_schedule WHERE id = '$id'");
            if ($res && $res->num_rows > 0) {
                $examToEdit = $res->fetch_assoc();
            }
        } else {
            $errorMsg = "Failed to update exam schedule: " . $stmt->error;
        }
    } else {
        $errorMsg = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Exam Schedule - Admin Panel | MIU SCIENCE FACULTY PORTAL</title>
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
        .form-table { margin: 0 auto; }
        .form-table td { padding: 0.7rem 1rem; }
        .form-table input, .form-table select { padding: 0.5rem; border-radius: 6px; border: 1px solid #cbd5e0; width: 100%; }
        .form-table input[type="submit"] { background: #2563eb; color: #fff; font-weight: 600; border: none; cursor: pointer; transition: background 0.2s; }
        .form-table input[type="submit"]:hover { background: #1d4ed8; }
        .msg-success { color: #16a34a; font-weight: 600; margin-bottom: 1rem; }
        .msg-error { color: #dc2626; font-weight: 600; margin-bottom: 1rem; }
        .search-bar { margin-bottom: 2rem; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="exam-dashboard">
            <div class="exam-title">Update Exam Schedule</div>
            <form class="search-bar" method="get" action="">
                <label for="id">Search by Exam Schedule ID:</label>
                <input type="text" name="id" id="id" value="<?= isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '' ?>" placeholder="Enter Exam Schedule ID" required>
                <input type="submit" value="Search">
            </form>
            <?php if ($successMsg): ?><div class="msg-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
            <?php if ($errorMsg): ?><div class="msg-error"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
            <?php if ($examToEdit): ?>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($examToEdit['id']) ?>">
                <table class="form-table">
                    <tr>
                        <td>Exam Date:</td>
                        <td><input type="date" name="examDate" value="<?= htmlspecialchars($examToEdit['examdate']) ?>" required></td>
                    </tr>
                    <tr>
                        <td>Exam Time:</td>
                        <td><input type="text" name="examTime" value="<?= htmlspecialchars($examToEdit['time']) ?>" required></td>
                    </tr>
                    <tr>
                        <td>Course Unit:</td>
                        <td>
                            <select name="courseUnitId" required>
                                <option value="">Select Course Unit</option>
                                <?php foreach ($courseUnits as $cu): ?>
                                    <option value="<?= htmlspecialchars($cu['id']) ?>" <?= $examToEdit['course_unit_id'] == $cu['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cu['name']) ?> (<?= htmlspecialchars($cu['code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Semester:</td>
                        <td>
                            <select name="semesterId" required>
                                <option value="">Select Semester</option>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?= htmlspecialchars($sem['id']) ?>" <?= $examToEdit['semester_id'] == $sem['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sem['semester_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="update" value="Update Exam Schedule"></td>
                    </tr>
                </table>
            </form>
            <?php elseif(isset($_GET['id'])): ?>
                <div class="msg-error">No exam schedule found for the given ID.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
