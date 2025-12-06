<?php  
include_once('main.php');
 $em = $_REQUEST['id'];
$stmt = $mysqli->prepare("SELECT grade FROM grade WHERE courseid = ? AND studentid = ?");
$stmt->bind_param("ss", $em, $check);
$stmt->execute();
$stmt->bind_result($grade);
echo "<tr> <th>Grade</th> </tr>";
while ($stmt->fetch()) {
    echo "<tr> <td>" . htmlspecialchars($grade) . "<td></tr>";
}
$stmt->close();
?>
