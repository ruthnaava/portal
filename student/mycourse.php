<?php  
include_once('main.php');
 $emn = $_REQUEST['classname'];
$stmt = $mysqli->prepare("SELECT DISTINCT id, name FROM course WHERE classid IN (SELECT id FROM class WHERE name = ?) AND studentid = ?");
$stmt->bind_param("ss", $emn, $check);
$stmt->execute();
$stmt->bind_result($cid, $cname);
while ($stmt->fetch()) {
    echo '<option value="', htmlspecialchars($cid), '">', htmlspecialchars($cname), '</option>';
}
$stmt->close();
?>
