<?php
include_once('main.php');

$date = isset($_GET['date']) ? $_GET['date'] : null;
if (!$date) {
    header('Location: attendance.php');
    exit;
}

// Check if student has an attendance record for this date
$stmt = $mysqli->prepare("SELECT id FROM attendance WHERE attendedid = ? AND role = 'student' AND date = ? LIMIT 1");
$stmt->bind_param('ss', $check, $date);
$stmt->execute();
$stmt->bind_result($attendance_id);
$has = $stmt->fetch();
$stmt->close();

// Handle POST: student requests change or direct update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $note = trim($_POST['note'] ?? '');

    // If attendance_change_requests table exists, insert a request
    $checkTable = $mysqli->query("SHOW TABLES LIKE 'attendance_change_requests'");
    if ($checkTable && $checkTable->num_rows > 0) {
        $stmt = $mysqli->prepare("INSERT INTO attendance_change_requests (student_id, attendance_date, action, note, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        if ($stmt) {
            $stmt->bind_param('ssss', $check, $date, $action, $note);
            $stmt->execute();
            $stmt->close();
            header('Location: attendance.php?msg=attendance_request_submitted');
            exit;
        }
    }

    // Fallback immediate operations
    if ($action === 'mark_absent' && $has) {
        $d = $mysqli->prepare("DELETE FROM attendance WHERE id = ? AND attendedid = ?");
        $d->bind_param('is', $attendance_id, $check);
        $d->execute();
        $d->close();
        header('Location: attendance.php?msg=attendance_updated');
        exit;
    }
    if ($action === 'mark_present' && !$has) {
        $i = $mysqli->prepare("INSERT INTO attendance (attendedid, role, date) VALUES (?, 'student', ?)");
        $i->bind_param('ss', $check, $date);
        $i->execute();
        $i->close();
        header('Location: attendance.php?msg=attendance_updated');
        exit;
    }

    // default
    header('Location: attendance.php?msg=attendance_error');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <title>Edit Attendance</title>
    <style>
        :root{--school-green:#198754}
        body{font-family:Segoe UI,Arial,Helvetica,sans-serif;padding:1.5rem}
        .card{max-width:720px;margin:0 auto;padding:1rem;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.06)}
        .card h3{color:var(--school-green);}
        .action-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;color:#fff;font-weight:700;border:3px solid transparent}
        .btn-confirm{background:linear-gradient(90deg,#34d399,#10b981);border-color:#059669}
        .btn-cancel{background:#fff;color:#333;border:1px solid #ddd}
        textarea{min-height:80px}
    </style>
</head>
<body>
    <div class="card">
        <h3>Edit Attendance - <?php echo htmlspecialchars($date); ?></h3>
        <p>Use this form to request changes to your attendance for <strong><?php echo htmlspecialchars($date); ?></strong>. Requests will be reviewed by staff.</p>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Action</label>
                <select name="action" class="form-select">
                    <?php if ($has): ?>
                    <option value="mark_absent">Mark as Absent (remove record)</option>
                    <?php else: ?>
                    <option value="mark_present">Request mark as Present (add record)</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Note (optional)</label>
                <textarea name="note" class="form-control" placeholder="Explain why this change should be made"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button class="action-btn btn-confirm" type="submit">Submit Request</button>
                <a href="attendance.php" class="action-btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
