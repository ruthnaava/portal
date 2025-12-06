<?php
include_once('main.php');
// Handle claim submission
$claim_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_date'], $_POST['claim_course'], $_POST['claim_teacher'])) {
    $claim_date = $_POST['claim_date'];
    $claim_course = $_POST['claim_course'];
    $claim_teacher = $_POST['claim_teacher'];
    $claim_reason = trim($_POST['claim_reason'] ?? '');
    if ($claim_course && $claim_teacher) {
        $stmt = $mysqli->prepare("INSERT INTO report (studentid, teacherid, message, course_unit_id, semester_id) VALUES (?, ?, ?, ?, (SELECT se.current_semester_id FROM student_enrollments se WHERE se.student_id = ? AND se.status = 'Active' LIMIT 1))");
        $msg = 'Attendance claim for ' . $claim_date . ' - ' . $claim_reason;
        $stmt->bind_param("sssss", $check, $claim_teacher, $msg, $claim_course, $check);
        if ($stmt->execute()) {
            $claim_msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">Claim submitted to teacher.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } else {
            $claim_msg = '<div class="alert-error">Failed to submit claim.</div>';
        }
        $stmt->close();
    } else {
        $claim_msg = '<div class="alert-error">No course/teacher found for claim.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/student.css">
    <script type="text/javascript" src="jquery-1.12.3.js"></script>
    <style>
        .student-content { margin-left: var(--sidebar-width); padding: calc(var(--topbar-height) + 2.5rem) 2.5rem 2.5rem 2.5rem; min-height: 100vh; background: none; transition: var(--transition); }
        @media (max-width: 992px) { .student-content { margin-left: 0; padding: 6.5rem 1rem 1rem 1rem; } }
        .modern-table { max-width: 100%; overflow-x: auto; }
    /* Theme fallback */
    :root { --school-green: #198754; }
    .attendance-header { margin-bottom: 1.6rem; background: linear-gradient(90deg, rgba(25,135,84,0.06), rgba(25,135,84,0.02)); border-left:6px solid var(--school-green); border-radius:10px; padding:1.25rem 1.5rem; }
    .attendance-title { font-size: 1.9rem; font-weight:700; color: var(--school-green); margin-bottom: 0; }
    .attendance-meta { font-size: 1rem; color: #2f3f36; }
    h3.section-subtitle { margin-bottom:1rem; color: var(--school-green); font-weight:700; }
    /* Action buttons (reuse style similar to course actions) */
    .table-actions { display:flex; gap:0.6rem; align-items:center; }
    .action-btn { display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:8px 14px; border-radius:10px; color:#fff; font-weight:700; text-decoration:none; border:3px solid transparent; box-shadow:0 2px 0 rgba(0,0,0,0.06); transition:transform .12s, box-shadow .12s; }
    .action-btn i { font-size:16px; }
    .btn-edit { background: linear-gradient(90deg,#34d399,#10b981); border-color:#1e90ff; }
    .btn-request { background: linear-gradient(90deg,#34d399,#10b981); border-color:#0ea5a4; }
    .action-btn:hover { transform:translateY(-2px); box-shadow:0 8px 18px rgba(0,0,0,0.12); }
        .filter-form { margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; }
        .claim-btn { background: var(--accent); color: #fff; border: none; border-radius: 8px; padding: 0.4em 1em; cursor: pointer; font-size: 0.95em; }
        .claim-btn:hover { background: var(--primary); }
        .claim-form { display: flex; flex-direction: column; gap: 0.5em; margin-top: 0.5em; }
        @media (max-width: 600px) {
            .feature-cards { flex-direction: column; gap: 1rem; }
            .attendance-header { padding: 1rem; }
        }
            /* Summary cards */
            .feature-cards { display:flex; gap:0.9rem; margin-bottom:1.25rem; }
            .feature-card { flex:1; background: linear-gradient(180deg, rgba(25,135,84,0.06), rgba(25,135,84,0.02)); border-radius:12px; padding:1rem 1.25rem; box-shadow:0 6px 18px rgba(6,10,6,0.04); border-left:6px solid var(--school-green); }
            .feature-card h4 { margin:0 0 0.35rem 0; color: #214733; font-size:0.95rem; }
            .feature-value { font-size:1.5rem; font-weight:800; color: #0b5132; }
            /* Modal styles */
            #claimModal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; }
            #claimModal .modal-box { background:#fff; border-radius:10px; padding:1.25rem; width:420px; max-width:92%; box-shadow:0 12px 36px rgba(0,0,0,0.25); }
            #claimModal .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem; }
            #claimModal .modal-body { display:flex; flex-direction:column; gap:0.6rem; }
            #claimModal .modal-actions { display:flex; gap:0.6rem; justify-content:flex-end; margin-top:0.5rem; }
            .btn-ghost { background:transparent; border:1px solid #ddd; padding:0.45rem 0.9rem; border-radius:8px; }
    </style>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    <?php include('includes/topbar.php'); ?>
    <div class="student-content">
        <div class="attendance-header">
            <div class="attendance-title">My Attendance</div>
            <div class="attendance-meta">
                <?php
                $stmt = $mysqli->prepare("SELECT se.program_id, p.name, ay.year_name, s.semester_name FROM student_enrollments se JOIN programs p ON se.program_id = p.id LEFT JOIN academic_years ay ON se.current_year_id = ay.id LEFT JOIN semesters s ON se.current_semester_id = s.id WHERE se.student_id = ? AND se.status = 'Active' LIMIT 1");
                $stmt->bind_param("s", $check);
                $stmt->execute();
                $stmt->bind_result($program_id, $program_name, $year_name, $semester_name);
                $stmt->fetch();
                $stmt->close();
                echo '<strong>Program:</strong> ' . htmlspecialchars($program_name ?? '-') . ' | ';
                echo '<strong>Year:</strong> ' . htmlspecialchars($year_name ?? '-') . ' | ';
                echo '<strong>Semester:</strong> ' . htmlspecialchars($semester_name ?? '-');
                ?>
            </div>
        </div>
        <?php echo $claim_msg; ?>
        <form method="get" class="filter-form">
            <div>
                <label for="from">From:</label>
                <input type="date" id="from" name="from" class="modern-input" value="<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>">
            </div>
            <div>
                <label for="to">To:</label>
                <input type="date" id="to" name="to" class="modern-input" value="<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>">
            </div>
            <button type="submit"  class="btn btn-success btn-sm mb-3 w-auto">Filter</button>
            <a href="attendance.php" class="btn btn-success btn-sm mb-3 w-auto" style="text-decoration:none;">Show All</a>
        </form>
        <h3 style="margin-bottom:1em;">Attendance Summary</h3>
        <?php
        // Fetch all attendance dates for this student in the filtered range
        $from = !empty($_GET['from']) ? $_GET['from'] : null;
        $to = !empty($_GET['to']) ? $_GET['to'] : null;
        $params = [$check];
        $types = 's';
        $date_filter = '';
        if ($from) {
            $date_filter .= ' AND a.date >= ?';
            $params[] = $from;
            $types .= 's';
        }
        if ($to) {
            $date_filter .= ' AND a.date <= ?';
            $params[] = $to;
            $types .= 's';
        }
        $stmt = $mysqli->prepare("SELECT a.date FROM attendance a WHERE a.attendedid = ? AND a.role = 'student' $date_filter ORDER BY a.date DESC");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->bind_result($adate);
        $attendanceDates = [];
        while ($stmt->fetch()) {
            $attendanceDates[] = $adate;
        }
        $stmt->close();
        // Calculate present/absent/percentage
        $present = count($attendanceDates);
        $absent = 0;
        $total = $present;
        $allDates = $attendanceDates;
        if ($from && $to) {
            $allDates = [];
            $start = new DateTime($from);
            $end = new DateTime($to);
            for ($d = $start; $d <= $end; $d->modify('+1 day')) {
                $allDates[] = $d->format('Y-m-d');
            }
            $total = count($allDates);
            $absent = 0;
            foreach ($allDates as $dt) {
                if (!in_array($dt, $attendanceDates)) $absent++;
            }
            $present = $total - $absent;
        }
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        echo '<div class="feature-cards">';
        echo '<div class="feature-card"><h4>Total Records</h4><div class="feature-value">' . (int)$total . '</div></div>';
        echo '<div class="feature-card"><h4>Present</h4><div class="feature-value">' . (int)$present . '</div></div>';
        echo '<div class="feature-card"><h4>Absent</h4><div class="feature-value">' . (int)$absent . '</div></div>';
        echo '<div class="feature-card"><h4>Attendance %</h4><div class="feature-value">' . $percentage . '%</div></div>';
        echo '</div>';
        // Detailed table: show all dates in range if filtered, else only present dates
        echo '<div class="section-title">All Attendance Records</div>';
        echo '<div style="overflow-x:auto;"><table class="modern-table">';
    echo '<tr><th>Date</th><th>Status</th><th>Claim</th><th>Actions</th></tr>';
        $hasRows = false;
        if ($from && $to) {
            foreach ($allDates as $currDate) {
                $hasRows = true;
                if (in_array($currDate, $attendanceDates)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($currDate) . '</td>';
                    echo '<td>Present</td>';
                    echo '<td>-</td>';
                    echo '<td style="white-space:nowrap;">';
                    echo '<div class="table-actions">';
                    echo '<a class="action-btn btn-edit" href="editAttendance.php?date=' . urlencode($currDate) . '"><i class="fas fa-edit"></i> Edit</a>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                } else {
                    // Use first course/teacher from current semester for claim (or leave blank if not available)
                    $stmt = $mysqli->prepare("SELECT sc.course_unit_id, t.id as teacher_id FROM student_courses scs JOIN semester_courses sc ON scs.semester_course_id = sc.id LEFT JOIN teachers t ON sc.teacher_id = t.id WHERE scs.student_id = ? LIMIT 1");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($course_id, $teacher_id);
                    $stmt->fetch();
                    $stmt->close();
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($currDate) . '</td>';
                    echo '<td>Absent</td>';
                    echo '<td>';
                    if ($course_id && $teacher_id) {
                        // Button opens modal to submit claim
                        echo '<button type="button" class="action-btn btn-request" onclick="openClaimModal(\'' . htmlspecialchars($currDate) . '\', \'' . htmlspecialchars($course_id) . '\', \'' . htmlspecialchars($teacher_id) . '\')"><i class="fas fa-exclamation-circle"></i> Claim</button>';
                    } else {
                        echo '<span style="color:#888;font-size:0.95em;">No course/teacher found</span>';
                    }
                    echo '</td>';
                    echo '<td style="white-space:nowrap;">';
                    echo '<div class="table-actions">';
                    echo '<a class="action-btn btn-request" href="editAttendance.php?date=' . urlencode($currDate) . '"><i class="fas fa-edit"></i> Request</a>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
        } else {
            foreach ($attendanceDates as $date) {
                $hasRows = true;
                echo '<tr>';
                echo '<td>' . htmlspecialchars($date) . '</td>';
                echo '<td>Present</td>';
                echo '<td>-</td>';
                echo '<td style="white-space:nowrap;">';
                echo '<div class="table-actions">';
                echo '<a class="action-btn btn-edit" href="editAttendance.php?date=' . urlencode($date) . '"><i class="fas fa-edit"></i> Edit</a>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
        }
        if (!$hasRows) {
            echo '<tr><td colspan="4">No attendance records found.</td></tr>';
        }
        echo '</table></div>';
        ?>
    </div>
    <!-- Claim Modal -->
    <div id="claimModal" aria-hidden="true">
        <div class="modal-box">
            <div class="modal-header">
                <strong>Submit Attendance Claim</strong>
                <button type="button" class="btn-ghost" onclick="closeClaimModal()">Close</button>
            </div>
            <form id="claimFormModal" method="post">
                <div class="modal-body">
                    <input type="hidden" id="claim_date_input" name="claim_date" value="">
                    <input type="hidden" id="claim_course_input" name="claim_course" value="">
                    <input type="hidden" id="claim_teacher_input" name="claim_teacher" value="">
                    <label>Selected Date</label>
                    <div id="claim_selected_date" style="font-weight:700;color:#214733;margin-bottom:6px;">-</div>
                    <label for="claim_reason">Reason (optional)</label>
                    <textarea id="claim_reason_input" name="claim_reason" rows="4" class="modern-input" placeholder="Explain why you think this was a mistake (optional)"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-ghost" onclick="closeClaimModal()">Cancel</button>
                    <button type="button" class="modern-btn" onclick="submitClaimForm()">Submit Claim</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openClaimModal(date, course, teacher) {
            document.getElementById('claim_date_input').value = date;
            document.getElementById('claim_course_input').value = course;
            document.getElementById('claim_teacher_input').value = teacher;
            document.getElementById('claim_reason_input').value = '';
            document.getElementById('claim_selected_date').innerText = date;
            var m = document.getElementById('claimModal');
            if (m) m.style.display = 'flex';
        }
        function closeClaimModal() { var m = document.getElementById('claimModal'); if (m) m.style.display = 'none'; }
        function submitClaimForm() { var f = document.getElementById('claimFormModal'); if (f) f.submit(); }
        // close modal on ESC
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeClaimModal(); });
    </script>
</body>
</html>

