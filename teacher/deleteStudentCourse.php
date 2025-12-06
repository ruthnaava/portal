<?php
include_once('main.php');
include_once('../service/mysqlcon.php');

// Expect POST scs_id
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['scs_id'])) {
    header('Location: mystudents.php'); exit;
}
$scs_id = intval($_POST['scs_id']);

// Verify that the logged-in teacher owns the semester_course for this student record
$stmt = $mysqli->prepare("SELECT sc.id as sc_id, sc.teacher_id, sc.course_unit_id, sc.semester_id, scs.student_id FROM student_courses scs JOIN semester_courses sc ON scs.semester_course_id = sc.id WHERE scs.id = ? LIMIT 1");
$stmt->bind_param('i', $scs_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $row = $res->fetch_assoc()) {
    if ($row['teacher_id'] == $check) {
        // Create a request entry instead of deleting immediately.
        $studentid = $row['student_id'];
        $teacherid = $check;
        $course_unit_id = $row['course_unit_id'];
        $semester_id = $row['semester_id'];
        $msg = 'Teacher requested removal of student ' . $studentid . ' from course unit ' . $course_unit_id . ' (semester ' . $semester_id . ')';
        $ins = $mysqli->prepare("INSERT INTO report (studentid, teacherid, message, course_unit_id, semester_id) VALUES (?, ?, ?, ?, ?)");
        if ($ins) {
            $ins->bind_param('sssss', $studentid, $teacherid, $msg, $course_unit_id, $semester_id);
            $ins->execute();
        }
    }
}

header('Location: mystudents.php?msg=requested');
exit;
