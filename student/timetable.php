<?php
// timetable.php - Student view of their timetable for current semester
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');

// Get current semester for student
$stmt = $mysqli->prepare("SELECT se.current_semester_id, s.semester_name, ay.year_name, p.name as program_name FROM student_enrollments se JOIN semesters s ON se.current_semester_id = s.id JOIN academic_years ay ON s.year_id = ay.id JOIN programs p ON se.program_id = p.id WHERE se.student_id = ? AND se.status = 'Active' ORDER BY se.enrollment_date DESC LIMIT 1");
$stmt->bind_param("s", $check);
$stmt->execute();
$stmt->bind_result($current_semester_id, $semester_name, $year_name, $program_name);
$stmt->fetch();
$stmt->close();

$timetable = [];
if ($current_semester_id) {
    $stmt2 = $mysqli->prepare("SELECT t.*, cu.name as course_unit, cu.code, sc.teacher_id, te.name as teacher_name FROM timetable t JOIN semester_courses sc ON t.semester_course_id = sc.id JOIN course_units cu ON sc.course_unit_id = cu.id JOIN teachers te ON sc.teacher_id = te.id WHERE sc.semester_id = ?");
    $stmt2->bind_param("s", $current_semester_id);
    $stmt2->execute();
    $timetable = $stmt2->get_result();
}
// Helper for day name
function getDayName($num) {
    $days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
    return $days[$num] ?? $num;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <style>
        /* Theme fallback */
        :root { --school-green: #198754; }

        .student-content { margin-left: var(--sidebar-width); padding: calc(var(--topbar-height) + 2.5rem) 2.5rem 2.5rem 2.5rem; min-height: 100vh; background: none; transition: var(--transition); }
        @media (max-width: 992px) { .student-content { margin-left: 0; padding: 6.5rem 1rem 1rem 1rem; } }

        /* Timetable header card */
        .timetable-header {
            margin-bottom: 1.6rem;
            background: linear-gradient(90deg, rgba(25,135,84,0.06), rgba(25,135,84,0.02));
            border-left: 6px solid var(--school-green);
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .timetable-title { font-size: 1.9rem; font-weight: 700; color: var(--school-green); margin-bottom: 0; }
        .timetable-meta { font-size: 0.98rem; color: #2f3f36; }

        h3.section-subtitle { margin-bottom:1rem; color: var(--school-green); font-weight:700; }

        /* Table styling */
        .modern-table { width:100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 6px 18px rgba(18,52,38,0.06); }
        .modern-table thead th { background: var(--school-green); color: #fff; padding: 12px 10px; text-transform: uppercase; font-weight:700; font-size:0.95rem; }
        .modern-table tbody td { padding: 12px 10px; border-bottom: 1px solid #eef3ef; color: #26322d; }
        .modern-table tbody tr:nth-child(even) { background: #fbfdfb; }
        .modern-table tbody tr:hover { background: rgba(25,135,84,0.03); transform: translateZ(0); }

        /* Responsive compact cells */
        @media (max-width:768px) {
            .modern-table thead { display: none; }
            .modern-table, .modern-table tbody, .modern-table tr, .modern-table td { display: block; width: 100%; }
            .modern-table tr { margin-bottom: 0.6rem; }
            .modern-table td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; position: relative; }
            .modern-table td:before { content: attr(data-label); font-weight:700; color:#4b7866; display:block; margin-bottom:6px; }
        }

        /* small helper for room/teacher labels */
        .muted { color:#5b6b64; font-size:0.95rem; }
    </style>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    <?php include('includes/topbar.php'); ?>
    <div class="student-content">
        <div class="timetable-header">
            <div class="timetable-title">My Timetable</div>
            <div class="timetable-meta">
                <?php
                echo '<strong>Program:</strong> ' . htmlspecialchars($program_name ?? '-') . ' | ';
                echo '<strong>Year:</strong> ' . htmlspecialchars($year_name ?? '-') . ' | ';
                echo '<strong>Semester:</strong> ' . htmlspecialchars($semester_name ?? '-');
                ?>
            </div>
        </div>
    <h3 class="section-subtitle">Current Semester Schedule</h3>
        <?php if ($current_semester_id && $timetable && $timetable->num_rows > 0): ?>
        <div style="overflow-x:auto;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Course Unit</th>
                    <th>Teacher</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $timetable->fetch_assoc()): ?>
                <tr>
                    <td data-label="Day"><?php echo htmlspecialchars(getDayName($row['day_of_week'])); ?></td>
                    <td data-label="Start"><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td data-label="End"><?php echo htmlspecialchars($row['end_time']); ?></td>
                    <td data-label="Course"><?php echo htmlspecialchars($row['course_unit']) . ' (' . htmlspecialchars($row['code']) . ')'; ?></td>
                    <td data-label="Teacher"><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                    <td data-label="Room"><?php echo htmlspecialchars($row['room']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p>No timetable found for your current semester.</p>
        <?php endif; ?>
    </div>
</body>
</html>
