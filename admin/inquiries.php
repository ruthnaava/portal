<?php
// Admin: list student inquiries / reports
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');
include_once('../../service/mysqlcon.php');

$items = [];
$res = $mysqli->query("SELECT * FROM report ORDER BY id DESC LIMIT 500");
if ($res) {
    while ($r = $res->fetch_assoc()) $items[] = $r;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inquiries & Reports | Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{ --school-green: #198754; }
        .main-content { margin-top:70px; margin-left:260px; padding:24px; }
        .panel { background:#fff; border-radius:12px; padding:18px; box-shadow:0 8px 28px rgba(0,0,0,0.06); }
        .panel h2 { color:var(--school-green); margin-bottom:8px; }
        .inquiry-table { width:100%; border-collapse: collapse; margin-top:12px; }
        .inquiry-table th, .inquiry-table td { padding:10px 12px; border-bottom:1px solid #eef3ee; text-align:left; }
        .inquiry-table th { background: linear-gradient(90deg,#198754,#36b37a); color:#fff; font-weight:600; }
        .badge-student { background:#e6f6ee; color:#0b5f3a; padding:6px 8px; border-radius:8px; font-weight:600; }
        .no-data { color:#6b756d; padding:1rem; text-align:center; }
        .action-btn { padding:6px 10px; border-radius:8px; text-decoration:none; color:#fff; background:var(--school-green); display:inline-block; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <main class="main-content">
        <div class="panel">
            <h2><i class="fas fa-inbox"></i> Student Inquiries & Reports</h2>
            <p class="muted">Latest inquiries and attendance reports submitted by students. This is a read-only view — no schema changes performed.</p>
            <?php if (count($items) === 0): ?>
                <div class="no-data">No inquiries or reports found.</div>
            <?php else: ?>
                <table class="inquiry-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Teacher ID</th>
                            <th>Course</th>
                            <th>Message</th>
                            <th>Semester</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($it['id'] ?? ''); ?></td>
                            <td><span class="badge-student"><?php echo htmlspecialchars($it['studentid'] ?? $it['student_id'] ?? ''); ?></span></td>
                            <td><?php echo htmlspecialchars($it['teacherid'] ?? $it['teacher_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($it['course_unit_id'] ?? $it['course_unit'] ?? ''); ?></td>
                            <td style="max-width:520px;white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars($it['message'] ?? $it['msg'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($it['semester_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($it['created_at'] ?? $it['date'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
