<?php
include_once('main.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Exam Schedule</title>
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
    .exam-header { margin-bottom: 1.6rem; background: linear-gradient(90deg, rgba(25,135,84,0.06), rgba(25,135,84,0.02)); border-left:6px solid var(--school-green); border-radius:10px; padding:1.25rem 1.5rem; }
    .exam-title { font-size: 1.9rem; font-weight:700; color: var(--school-green); margin-bottom:0; }
    .exam-meta { font-size: 1rem; color: #2f3f36; }
    .section-subtitle { margin-bottom:1rem; color: var(--school-green); font-weight:700; }
    </style>
</head>
<body>
    <?php include('includes/topbar.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <div class="student-content">
        <div class="exam-header">
            <div class="exam-title">My Exam Schedule</div>
            <div class="exam-meta">
                <?php
                // Show program, year, semester
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
        <h3 style="margin-bottom:1em;">Upcoming Exams (Current Semester)</h3>
        <?php
        // List all exams for current semester for this student
        $stmt = $mysqli->prepare("SELECT cu.code, cu.name, e.examdate, e.time, t.name as teacher_name FROM student_enrollments se JOIN student_courses sc ON se.student_id = sc.student_id JOIN semester_courses semc ON sc.semester_course_id = semc.id JOIN course_units cu ON semc.course_unit_id = cu.id JOIN exam_schedule e ON e.course_unit_id = cu.id AND e.semester_id = semc.semester_id JOIN teachers t ON semc.teacher_id = t.id WHERE se.student_id = ? AND se.status = 'Active' AND semc.semester_id = se.current_semester_id ORDER BY e.examdate, e.time");
        $stmt->bind_param("s", $check);
        $stmt->execute();
        $stmt->bind_result($code, $cname, $examdate, $time, $teacher_name);
        $hasRows = false;
        echo '<div style="overflow-x:auto;"><table class="modern-table">';
        echo '<tr><th>Course Code</th><th>Course Name</th><th>Exam Date</th><th>Time</th><th>Teacher</th></tr>';
        while ($stmt->fetch()) {
            $hasRows = true;
            echo '<tr>';
            echo '<td>' . htmlspecialchars($code) . '</td>';
            echo '<td>' . htmlspecialchars($cname) . '</td>';
            echo '<td>' . htmlspecialchars($examdate) . '</td>';
            echo '<td>' . htmlspecialchars($time) . '</td>';
            echo '<td>' . htmlspecialchars($teacher_name) . '</td>';
            echo '</tr>';
        }
        if (!$hasRows) {
            echo '<tr><td colspan="5">No exams scheduled for your current semester.</td></tr>';
        }
        echo '</table></div>';
        $stmt->close();
        ?>
    </div>
</body>
</html>

