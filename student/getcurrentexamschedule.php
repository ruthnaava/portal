<?php  
include_once('main.php');
$em = $_REQUEST['curid'];
$stmt = $mysqli->prepare("SELECT examdate, time FROM examschedule WHERE courseid = ?");
$stmt->bind_param("s", $em);
$stmt->execute();
$stmt->bind_result($examdate, $time);
echo "<tr> <th>Exam Date:</th><th>Exam Time:</th></tr>";
while ($stmt->fetch()) {
    echo "<tr><td>" . htmlspecialchars($examdate) . "</td><td>" . htmlspecialchars($time) . "</td></tr>";
}
$stmt->close();
?>
