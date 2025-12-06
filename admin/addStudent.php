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

// Handle form submission
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $stuId = trim($_POST['studentId']);
    $stuName = trim($_POST['studentName']);
    $stuPassword = trim($_POST['studentPassword']);
    $stuPhone = trim($_POST['studentPhone']);
    $stuEmail = trim($_POST['studentEmail']);
    $stugender = $_POST['gender'];
    $stuDOB = $_POST['studentDOB'];
    $stuAddress = trim($_POST['studentAddress']);
    $stuParentId = trim($_POST['studentParentId']);
    if ($stuParentId === '') $stuParentId = null;
    $filename = $stuId . ".jpg";
    $filetmp = $_FILES['file']['tmp_name'];
    if ($filetmp) {
        move_uploaded_file($filetmp, "../images/" . $filename);
    }
    // Insert into students (id, name, phone, email, sex, dob, address, parentid)
    $stmt = $mysqli->prepare("INSERT INTO students (id, name, phone, email, sex, dob, address, parentid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $stuId, $stuName, $stuPhone, $stuEmail, $stugender, $stuDOB, $stuAddress, $stuParentId);
    $ok1 = $stmt->execute();
    $stmt->close();
    // Insert into users (userid, password, usertype)
    $stmt2 = $mysqli->prepare("INSERT INTO users (userid, password, usertype) VALUES (?, ?, 'student')");
    $stmt2->bind_param("ss", $stuId, $stuPassword);
    $ok2 = $stmt2->execute();
    $stmt2->close();
    if ($ok1 && $ok2) {
        $msg = '<div class="msg-success">Student registered successfully.</div>';
    } else {
        $msg = '<div class="msg-error">Error: Could not register student. Please check details.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .add-student-container {
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2.5rem 2rem;
            max-width: 600px;
        }
        .add-student-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .add-student-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem 2rem;
        }
        .add-student-form .form-group { margin-bottom: 0; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.3rem; }
        .form-group input, .form-group select {
            width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem;
        }
        .form-group input[type="file"] { padding: 0.2rem; }
        .form-group.gender-group { display: flex; align-items: center; gap: 1rem; }
        .form-group.gender-group label { margin-bottom: 0; }
        .form-group.full { grid-column: span 2; }
        .btn { background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.6rem 1.2rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .msg-success { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
        .msg-error { background: #fee2e2; color: #b91c1c; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
        @media (max-width: 600px) {
            .add-student-container { padding: 1rem; }
            .add-student-form { grid-template-columns: 1fr; gap: 1rem; }
        }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="add-student-container">
            <div class="add-student-title">Student Registration</div>
            <?= $msg ?>
            <form class="add-student-form" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Student Id</label>
                    <input type="text" name="studentId" required placeholder="Enter Id">
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="studentName" required placeholder="Enter Name">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="studentPassword" required placeholder="Enter Password">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="studentPhone" required placeholder="Enter Phone Number">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="studentEmail" required placeholder="Enter Email">
                </div>
                <div class="form-group gender-group">
                    <label>Gender</label>
                    <input type="radio" name="gender" value="Male" required> Male
                    <input type="radio" name="gender" value="Female"> Female
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="studentDOB" required>
                </div>
                <div class="form-group full">
                    <label>Address</label>
                    <input type="text" name="studentAddress" required placeholder="Enter Address">
                </div>
                <div class="form-group full">
                    <label>Parent Id <span style="color:#888;font-weight:400;">(optional)</span></label>
                    <input type="text" name="studentParentId" placeholder="Enter Parent Id (if any)">
                </div>
                <div class="form-group full">
                    <label>Picture</label>
                    <input type="file" name="file" accept="image/*">
                </div>
                <div class="form-group full" style="text-align:right;">
                    <button type="submit" class="btn" name="submit">Submit</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
