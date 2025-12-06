<?php
session_start();
include_once('mysqlcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location:../index.php?login=false");
        exit();
    }

    $myid = trim($_POST['myid']);
    $mypassword = $_POST['mypassword'];

    // Prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT userid, password, usertype FROM users WHERE userid = ?");
    $stmt->bind_param("s", $myid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userid, $db_password, $usertype);
        $stmt->fetch();
        // WARNING: Plain text password check for testing only!
        if ($mypassword === $db_password) {
            $_SESSION['login_id'] = $userid;
            $_SESSION['usertype'] = $usertype;
            // Redirect based on usertype
            header("Location:../module/$usertype");
            exit();
        }
    }
    // Login failed
    header("Location:../index.php?login=false");
    exit();
}
header("Location:../index.php");
exit();
?>
