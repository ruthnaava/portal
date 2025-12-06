<?php
include_once('../../service/mysqlcon.php');
$check = $_SESSION['login_id'];
// Use mysqli for queries
$stmt = $mysqli->prepare("SELECT name FROM students WHERE id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$login_session = $loged_user_name = $name;
$stmt->close();
if (!isset($login_session) || !$login_session) {
    header("Location:../../");
    exit;
}
?>
