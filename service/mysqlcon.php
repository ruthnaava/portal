<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$db_name = "schoolmsdb";

$conn = new mysqli($host, $username, $password, $db_name);
if ($conn->connect_errno) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}
// For backward compatibility
$mysqli = $conn;
?>
