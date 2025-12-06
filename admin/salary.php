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

// Calculate teacher salaries
$teacherSalaries = [];
$sql = "SELECT t.id, t.name, t.salary, COUNT(a.date) AS present_days
        FROM teachers t
        LEFT JOIN attendance a ON t.id = a.attendedid AND a.role = 'teacher' AND MONTH(a.date) = MONTH(CURDATE()) AND YEAR(a.date) = YEAR(CURDATE())
        GROUP BY t.id, t.name, t.salary";
$res = $mysqli->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row['payable_salary'] = round($row['salary'] * $row['present_days'] / 30); // Assume 30 days in month
        $teacherSalaries[] = $row;
    }
}
// Calculate staff salaries
$staffSalaries = [];
$sql = "SELECT s.id, s.name, s.salary, COUNT(a.date) AS present_days
        FROM staff s
        LEFT JOIN attendance a ON s.id = a.attendedid AND a.role = 'staff' AND MONTH(a.date) = MONTH(CURDATE()) AND YEAR(a.date) = YEAR(CURDATE())
        GROUP BY s.id, s.name, s.salary";
$res = $mysqli->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row['payable_salary'] = round($row['salary'] * $row['present_days'] / 30);
        $staffSalaries[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary - Admin Panel | MIU SCIENCE FACULTY PORTAL</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .salary-dashboard {
            margin: 40px 0 0 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 100%;
            width: 100%;
        }
        .salary-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .salary-table {
            width: 100%; border-collapse: collapse; margin-bottom: 2rem; background: #fff;
        }
        .salary-table th, .salary-table td {
            padding: 0.7rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left;
        }
        .salary-table th { background: #f1f5f9; }
        .salary-table tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="salary-dashboard">
            <div class="salary-title text-success ">TEACHERS SALARY LIST</div>
            <table class="salary-table mb-5 text-success">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Salary</th>
                        <th>Present Days</th>
                        <th>Payable Salary</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($teacherSalaries as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['salary']) ?></td>
                        <td><?= htmlspecialchars($row['present_days']) ?></td>
                        <td><?= htmlspecialchars($row['payable_salary']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="salary-title text-success">STAFF SALARY LIST</div>
            <table class="salary-table mb-0 text-success">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Salary</th>
                        <th>Present Days</th>
                        <th>Payable Salary</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($staffSalaries as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['salary']) ?></td>
                        <td><?= htmlspecialchars($row['present_days']) ?></td>
                        <td><?= htmlspecialchars($row['payable_salary']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
