<?php
include ('../../service/mysqlcon.php');
session_start();
session_unset();
session_destroy();
header('Location: ../../');
exit();
?>