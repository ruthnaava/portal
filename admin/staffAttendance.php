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

// Fetch staff not yet marked present today
$staffs = [];
$res = $mysqli->query("SELECT id, name, phone, email FROM staff WHERE id NOT IN (SELECT attendedid FROM attendance WHERE date=CURDATE() AND role='staff')");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $staffs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Attendance - Admin Panel | MIU SCIENCE FACULTY PORTAL</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .attendance-table-container {
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 800px;
            overflow-x: auto;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .attendance-table th, .attendance-table td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .attendance-table th:nth-child(2), .attendance-table td:nth-child(2) {
            max-width: 120px;
        }
        .attendance-table th:nth-child(3), .attendance-table td:nth-child(3) {
            max-width: 160px;
        }
        .attendance-table th:nth-child(4), .attendance-table td:nth-child(4) {
            max-width: 160px;
        }
        .attendance-table th:nth-child(5), .attendance-table td:nth-child(5) {
            max-width: 180px;
        }
        .btn-present { background: #22c55e; color: #fff; border: none; border-radius: 6px; padding: 0.4rem 1.2rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-present:hover { background: #16a34a; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="attendance-table-container">
            <h2>Staff Attendance List</h2><hr/>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Mark Present</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($staffs) === 0): ?>
                    <tr><td colspan="5" style="text-align:center; color:#888;">All staff have been marked present today.</td></tr>
                <?php else: ?>
                    <?php foreach ($staffs as $staff): ?>
                        <tr>
                            <td>
                                <form action="attendStaff.php" method="post" style="margin:0;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($staff['id']) ?>">
                                    <button type="submit" name="submit" class="btn-present">Present</button>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($staff['id']) ?></td>
                            <td><?= htmlspecialchars($staff['name']) ?></td>
                            <td><?= htmlspecialchars($staff['phone']) ?></td>
                            <td><?= htmlspecialchars($staff['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
