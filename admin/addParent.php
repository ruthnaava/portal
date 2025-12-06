<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../service/mysqlcon.php');
$check = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : null;
if ($check) {
    $stmt = $mysqli->prepare("SELECT name FROM admin WHERE id = ?");
    $stmt->bind_param("s", $check);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $login_session = $loged_user_name = $row ? $row['name'] : null;
} else {
    $login_session = null;
}
if (!isset($login_session) || !$login_session) {
    header("Location:../../");
    exit();
}

$successMsg = $errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id = trim($_POST['id']);
    $password = trim($_POST['password']);
    $fathername = trim($_POST['fathername']);
    $mothername = trim($_POST['mothername']);
    $fatherphone = trim($_POST['fatherphone']);
    $motherphone = trim($_POST['motherphone']);
    $address = trim($_POST['address']);
    // Insert into parents
    $stmt = $mysqli->prepare("INSERT INTO parents (id, fathername, mothername, fatherphone, motherphone, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssss", $id, $fathername, $mothername, $fatherphone, $motherphone, $address);
    $ok1 = $stmt->execute();
    $stmt->close();
    // Insert into users
    $stmt2 = $mysqli->prepare("INSERT INTO users (userid, password, usertype) VALUES (?, ?, 'parent')");
    $stmt2->bind_param("ss", $id, $password);
    $ok2 = $stmt2->execute();
    $stmt2->close();
    if ($ok1 && $ok2) {
        $successMsg = "Parent registered successfully.";
    } else {
        $errorMsg = "Error registering parent. Please check the details.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Parent - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .form-container {
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 600px;
        }
        .form-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.3rem; }
        .form-group input {
            width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem;
        }
        .btn { background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.6rem 1.2rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .msg-success { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
        .msg-error { background: #fee2e2; color: #991b1b; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="form-container">
            <div class="form-title">Parent Registration</div>
            <?php if ($successMsg): ?>
                <div class="msg-success"><?= $successMsg ?></div>
            <?php elseif ($errorMsg): ?>
                <div class="msg-error"><?= $errorMsg ?></div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="form-group">
                    <label>Parent ID</label>
                    <input type="text" name="id" required placeholder="Enter ID">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" required placeholder="Enter Password">
                </div>
                <div class="form-group">
                    <label>Father Name</label>
                    <input type="text" name="fathername" required placeholder="Enter Father Name">
                </div>
                <div class="form-group">
                    <label>Mother Name</label>
                    <input type="text" name="mothername" required placeholder="Enter Mother Name">
                </div>
                <div class="form-group">
                    <label>Father Phone</label>
                    <input type="text" name="fatherphone" required placeholder="Enter Father Phone">
                </div>
                <div class="form-group">
                    <label>Mother Phone</label>
                    <input type="text" name="motherphone" required placeholder="Enter Mother Phone">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" required placeholder="Enter Address">
                </div>
                <div style="text-align:right;">
                    <button type="submit" name="submit" class="btn">Register</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
