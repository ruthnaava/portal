<?php
include_once('main.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <script type="text/javascript" src="jquery-1.12.3.js"></script>
    <style>
        .student-content { margin-left: var(--sidebar-width); padding: calc(var(--topbar-height) + 2.5rem) 2.5rem 2.5rem 2.5rem; min-height: 100vh; background: none; transition: var(--transition); }
        @media (max-width: 992px) { .student-content { margin-left: 0; padding: 6.5rem 1rem 1rem 1rem; } }
        .program-header { background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .program-title { font-size: 1.75rem; font-weight: bold; margin-bottom: 0.5rem; }
        .program-meta { font-size: 1rem; color: #555; }
        .feature-cards { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .feature-card { background: #e9ecef; flex: 1; min-width: 150px; text-align: center; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .feature-card h4 { margin-bottom: 0.5rem; font-size: 1.25rem; }
    .feature-value { font-size: 2rem; font-weight: bold; color: #198754; }
    .section-title { font-size: 1.5rem; font-weight: bold; margin-top: 2rem; margin-bottom: 1rem; border-bottom: 2px solid #198754; display: inline-block; padding-bottom: 0.25rem; }
    /* Action buttons styling - bigger, rounded, icon left */
    .table-actions { display:flex; gap:0.6rem; align-items:center; }
    .action-btn { display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:10px 18px; border-radius:12px; color:#fff; font-weight:700; text-decoration:none; border:3px solid transparent; box-shadow:0 2px 0 rgba(0,0,0,0.06); transition:transform .12s, box-shadow .12s; }
    .action-btn i { font-size:18px; }
    .btn-edit { background: linear-gradient(90deg,#34d399,#10b981); border-color:#1e90ff; }
    .btn-delete { background: linear-gradient(90deg,#34d399,#10b981); border-color:#ff4d4f; }
    .action-btn:hover { transform:translateY(-2px); box-shadow:0 8px 18px rgba(0,0,0,0.12); }
    td .action-btn { min-width:120px; }
    @media (max-width:600px){ .table-actions{flex-direction:column; align-items:flex-start;} td .action-btn{width:100%;} }
        table.modern-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        table.modern-table th, table.modern-table td { border: 1px solid #dee2e6; padding: 0.75rem; text-align: left; }
    table.modern-table th { background-color: #198754; color: #fff; }
        table.modern-table tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <?php include('includes/sidebar.php'); ?>
    <?php include('includes/topbar.php'); ?>
    <div class="student-content">
        <?php
        // Flash messages (simple)
        if (isset($_GET['msg'])) {
            $msg = $_GET['msg'];
            if ($msg === 'removed') {
                echo '<div class="alert alert-success">Course enrollment removed successfully.</div>';
            } elseif ($msg === 'request_submitted') {
                echo '<div class="alert alert-info">Your request to remove this course was submitted and is pending approval.</div>';
            } elseif ($msg === 'update_submitted') {
                echo '<div class="alert alert-info">Your update request was submitted and is pending approval.</div>';
            } elseif ($msg === 'update_success') {
                echo '<div class="alert alert-success">Enrollment updated successfully.</div>';
            } else {
                echo '<div class="alert alert-danger">An error occurred. Please try again.</div>';
            }
        }
        ?>
        <?php
        // 1. Get student's program and meta
        $stmt = $mysqli->prepare("SELECT se.program_id, p.name, p.duration_years, p.description, se.current_year_id, ay.year_name, se.current_semester_id, s.semester_name FROM student_enrollments se JOIN programs p ON se.program_id = p.id LEFT JOIN academic_years ay ON se.current_year_id = ay.id LEFT JOIN semesters s ON se.current_semester_id = s.id WHERE se.student_id = ? AND se.status = 'Active' LIMIT 1");
        $stmt->bind_param("s", $check);
        $stmt->execute();
        $stmt->bind_result($program_id, $program_name, $duration_years, $program_desc, $cur_year_id, $cur_year_name, $cur_sem_id, $cur_sem_name);
        $stmt->fetch();
        $stmt->close();
        echo '<div class="program-header">';
        echo '<div>';
        echo '<div class="program-title">' . htmlspecialchars($program_name) . '</div>';
        echo '<div class="program-meta">Duration: ' . htmlspecialchars($duration_years) . ' years';
        if ($cur_year_name) echo ' | Current Year: ' . htmlspecialchars($cur_year_name);
        if ($cur_sem_name) echo ' | Current Semester: ' . htmlspecialchars($cur_sem_name);
        echo '</div>';
        if ($program_desc) echo '<div class="program-meta">' . htmlspecialchars($program_desc) . '</div>';
        echo '</div>';
        echo '</div>';
        // 2. Feature cards: total courses, completed, in progress, GPA (if available)
        // Total courses in program
        $stmt = $mysqli->prepare("SELECT COUNT(DISTINCT cu.id) FROM course_units cu WHERE cu.program_id = ?");
        $stmt->bind_param("s", $program_id);
        $stmt->execute();
        $stmt->bind_result($total_courses);
        $stmt->fetch();
        $stmt->close();
        // Courses completed (has grade and not F)
        $stmt = $mysqli->prepare("SELECT COUNT(DISTINCT g.course_unit_id) FROM grades g WHERE g.student_id = ? AND g.grade NOT IN ('F', 'Fail')");
        $stmt->bind_param("s", $check);
        $stmt->execute();
        $stmt->bind_result($completed_courses);
        $stmt->fetch();
        $stmt->close();
        // Courses in progress (current semester)
        $stmt = $mysqli->prepare("SELECT COUNT(DISTINCT cu.id) FROM student_courses sc JOIN semester_courses semc ON sc.semester_course_id = semc.id JOIN course_units cu ON semc.course_unit_id = cu.id WHERE sc.student_id = ? AND semc.semester_id = ?");
        $stmt->bind_param("ss", $check, $cur_sem_id);
        $stmt->execute();
        $stmt->bind_result($inprogress_courses);
        $stmt->fetch();
        $stmt->close();
        // GPA (simple: average of marks if available)
        $stmt = $mysqli->prepare("SELECT AVG(mark) FROM grades WHERE student_id = ? AND mark IS NOT NULL");
        $stmt->bind_param("s", $check);
        $stmt->execute();
        $stmt->bind_result($gpa);
        $stmt->fetch();
        $stmt->close();
        echo '<div class="feature-cards">';
        echo '<div class="feature-card"><h4>Total Courses</h4><div class="feature-value">' . (int)$total_courses . '</div></div>';
        echo '<div class="feature-card"><h4>Completed</h4><div class="feature-value">' . (int)$completed_courses . '</div></div>';
        echo '<div class="feature-card"><h4>In Progress</h4><div class="feature-value">' . (int)$inprogress_courses . '</div></div>';
        echo '<div class="feature-card"><h4>GPA</h4><div class="feature-value">' . ($gpa ? number_format($gpa,2) : '-') . '</div></div>';
        echo '</div>';
        // 3. List all course units under this program, grouped by year/semester
        echo '<div class="section-title">All Course Units in Program</div>';
        echo '<div style="overflow-x:auto;"><table class="modern-table">';
        echo '<tr><th>Course Code</th><th>Course Name</th><th>Year</th><th>Semester</th></tr>';
        $stmt = $mysqli->prepare("SELECT cu.code, cu.name, ay.year_number, s.semester_number FROM course_units cu LEFT JOIN semester_courses sc ON cu.id = sc.course_unit_id LEFT JOIN semesters s ON sc.semester_id = s.id LEFT JOIN academic_years ay ON s.year_id = ay.id WHERE cu.program_id = ? GROUP BY cu.id, ay.year_number, s.semester_number ORDER BY ay.year_number, s.semester_number, cu.name");
        $stmt->bind_param("s", $program_id);
        $stmt->execute();
        $stmt->bind_result($code, $cname, $year, $semester);
        while ($stmt->fetch()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($code) . '</td>';
            echo '<td>' . htmlspecialchars($cname) . '</td>';
            echo '<td>' . ($year ? htmlspecialchars($year) : '-') . '</td>';
            echo '<td>' . ($semester ? htmlspecialchars($semester) : '-') . '</td>';
            echo '</tr>';
        }
        $stmt->close();
        echo '</table></div>';
        // 4. Show course units student is doing this semester
        echo '<div class="section-title">Course Units This Semester</div>';
        echo '<div style="overflow-x:auto;"><table class="modern-table">';
        echo '<tr><th>Course Code</th><th>Course Name</th><th>Semester</th><th>Teacher</th><th>Actions</th></tr>';
        $stmt = $mysqli->prepare("SELECT sc.id AS student_course_id, semc.id AS semester_course_id, cu.code, cu.name, s.semester_name, t.name FROM student_enrollments se JOIN student_courses sc ON se.student_id = sc.student_id JOIN semester_courses semc ON sc.semester_course_id = semc.id JOIN course_units cu ON semc.course_unit_id = cu.id JOIN semesters s ON semc.semester_id = s.id JOIN teachers t ON semc.teacher_id = t.id WHERE se.student_id = ? AND se.status = 'Active' AND s.id = se.current_semester_id");
        $stmt->bind_param("s", $check);
        $stmt->execute();
        $stmt->bind_result($student_course_id, $semester_course_id, $code, $cname, $semester_name, $teacher_name);
        $hasRows = false;
        while ($stmt->fetch()) {
            $hasRows = true;
            echo '<tr>';
            echo '<td>' . htmlspecialchars($code) . '</td>';
            echo '<td>' . htmlspecialchars($cname) . '</td>';
            echo '<td>' . htmlspecialchars($semester_name) . '</td>';
            echo '<td>' . htmlspecialchars($teacher_name) . '</td>';
            // Actions: Edit (link) and Delete (form POST)
            echo '<td style="white-space:nowrap;">';
            echo '<div class="table-actions">';
            // Edit link - styled
            echo '<a class="action-btn btn-edit" href="editCourseUnit.php?scid=' . urlencode($student_course_id) . '"><i class="fas fa-edit"></i> Edit</a>';
            // Delete form - styled
            echo '<form method="post" action="deleteCourseUnit.php" style="display:inline;">';
            echo '<input type="hidden" name="student_course_id" value="' . htmlspecialchars($student_course_id) . '">';
            echo '<input type="hidden" name="note" value="">';
            echo '<button type="submit" class="action-btn btn-delete" onclick="return confirmDelete(this.form);"><i class="fas fa-trash-alt"></i> Delete</button>';
            echo '</form>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        if (!$hasRows) {
            echo '<tr><td colspan="5">No courses found for this semester.</td></tr>';
        }
        $stmt->close();
        echo '</table></div>';
        ?>
    </div>
</body>
</html>

<script>
function confirmDelete(form){
    var ok = confirm('Are you sure you want to remove this course from your enrolled list?');
    if(!ok) return false;
    var note = prompt('Optional note for admin (reason for removal):','');
    if(note !== null){
        var input = form.querySelector('input[name="note"]');
        if(input) input.value = note;
    }
    return true;
}
</script>

