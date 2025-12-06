<?php
// Handler to allow a student to remove (unenroll) a course unit from their student_courses
include_once('main.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['student_course_id'])) {
    header('Location: course.php');
    exit;
}

$student_course_id = intval($_POST['student_course_id']);

// First, check if a course change requests table exists; if so, create a deletion request instead of immediate delete
$checkTable = $mysqli->query("SHOW TABLES LIKE 'course_change_requests'");
if ($checkTable && $checkTable->num_rows > 0) {
    // Insert a request record for admins to review (columns: student_id, student_course_id, type, note, status, created_at)
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $stmt = $mysqli->prepare("INSERT INTO course_change_requests (student_id, student_course_id, type, note, status, created_at) VALUES (?, ?, 'delete', ?, 'pending', NOW())");
    if ($stmt) {
        $stmt->bind_param('is', $check, $student_course_id);
        $stmt->execute();
        $stmt->close();
        header('Location: course.php?msg=request_submitted');
        exit;
    } else {
        // If insert fails, fallback to direct deletion
    }
}

// Fallback: perform immediate delete if requests table doesn't exist or insert failed
$stmt = $mysqli->prepare("DELETE FROM student_courses WHERE id = ? AND student_id = ?");
if (!$stmt) {
    header('Location: course.php?msg=error');
    exit;
}
$stmt->bind_param('is', $student_course_id, $check);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    header('Location: course.php?msg=removed');
} else {
    header('Location: course.php?msg=error');
}
exit;

?>
