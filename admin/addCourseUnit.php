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

// Fetch programs for dropdown
$programs = [];
$res = $mysqli->query("SELECT id, name FROM programs ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $programs[] = $row;
    }
}
$successMsg = $errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $code = trim($_POST['code']);
    $credit_hours = intval($_POST['credit_hours']);
    $description = trim($_POST['description']);
    $program_id = $_POST['program_id'] ?? null;
    if ($id && $name && $code && $credit_hours && $description) {
        $stmt = $mysqli->prepare("INSERT INTO course_units (id, name, code, credit_hours, description, program_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id, $name, $code, $credit_hours, $description, $program_id);
        if ($stmt->execute()) {
            $successMsg = "Course unit added successfully.";
        } else {
            $errorMsg = "Failed to add course unit: " . $stmt->error;
        }
    } else {
        $errorMsg = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course Unit - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .exam-dashboard {
            margin: 40px 0 0 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 100%;
            width: 100%;
        }
        .exam-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .form-table { margin: 0 auto; }
        .form-table td { padding: 0.7rem 1rem; }
        .form-table input, .form-table select, .form-table textarea { padding: 0.5rem; border-radius: 6px; border: 1px solid #cbd5e0; width: 100%; }
        .form-table input[type="submit"] { background: #2563eb; color: #fff; font-weight: 600; border: none; cursor: pointer; transition: background 0.2s; }
        .form-table input[type="submit"]:hover { background: #1d4ed8; }
        .msg-success { color: #16a34a; font-weight: 600; margin-bottom: 1rem; }
        .msg-error { color: #dc2626; font-weight: 600; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="exam-dashboard text-success bg-light text-align-center">
            <div class="exam-title">Add Course Unit</div>
            <?php if ($successMsg): ?><div class="msg-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
            <?php if ($errorMsg): ?><div class="msg-error"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
            <form action="" method="post">
                <table class="form-table">
                    <tr>
                        <td>Course Unit ID:</td>
                        <td><input type="text" name="id" placeholder="Course Unit ID" required></td>
                    </tr>
                    <tr>
                        <td>Name:</td>
                        <td><input type="text" name="name" placeholder="Course Unit Name" required></td>
                    </tr>
                    <tr>
                        <td>Code:</td>
                        <td><input type="text" name="code" placeholder="Course Code" required></td>
                    </tr>
                    <tr>
                        <td>Credit Hours:</td>
                        <td><input type="number" name="credit_hours" min="1" placeholder="Credit Hours" required></td>
                    </tr>
                    <tr>
                        <td>Program:</td>
                        <td>
                            <select name="program_id">
                                <option value="">Select Program (optional)</option>
                                <?php foreach ($programs as $p): ?>
                                    <option value="<?= htmlspecialchars($p['id']) ?>">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td><textarea name="description" placeholder="Description" required></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" style="background-color: #16a34a;" value="Add Course Unit"></td>
                    </tr>
                </table>
            </form>
        </div>
    </main>
</body>
</html>
