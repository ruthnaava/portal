<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../service/mysqlcon.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id = $_POST['id'];
    $cdate = date('Y-m-d');
    // Insert attendance for staff
    $stmt = $mysqli->prepare("INSERT INTO attendance (date, attendedid, role) VALUES (?, ?, 'staff')");
    $stmt->bind_param("ss", $cdate, $id);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) {
        die('Attendance Error: Could not mark attendance.');
    }
    header("Location: staffAttendance.php");
    exit();
}
?>
