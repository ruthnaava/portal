<?php  
include_once('main.php');
$em = $_REQUEST['cid'];
// Get teacher info
$stmt = $mysqli->prepare("SELECT t.id, t.name, t.email FROM teachers t JOIN course c ON t.id = c.teacherid WHERE c.id = ? AND c.studentid = ?");
$stmt->bind_param("ss", $em, $check);
$stmt->execute();
$stmt->bind_result($tid, $tname, $temail);
// Get class info
$stmt2 = $mysqli->prepare("SELECT section, room FROM class WHERE id = (SELECT classid FROM course WHERE id = ? AND studentid = ?)");
$stmt2->bind_param("ss", $em, $check);
$stmt2->execute();
$stmt2->bind_result($section, $room);
$stmt2->fetch();
while ($stmt->fetch()) {
    echo "Teacher ID: $tid<br/>";
    echo "Teacher Name: $tname<br/>";
    echo "Teacher Email: $temail<br/>";
    echo "Your Section : $section<br/>";
    echo "Your Class Room : $room<br/>";
}
$stmt->close();
$stmt2->close();
?>
