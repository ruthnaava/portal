<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stat-card.full-width { width: 100%; min-width: 0; }
        .dashboard-grid { display: block; }
    </style>
</head>
<body>
    <div class="main-content container min-vh-100 py-4">
        <div class="page-header w-100">
            <h1 class="page-title text-center text-success">My Students</h1>
        </div>
        <div class="dashboard-grid w-100 d-flex flex-column align-items-center justify-content-center">
            <div class="stat-card full-width rounded-4 shadow-sm d-flex flex-column align-items-center justify-content-center p-4 w-100" style="max-width:900px;">
                <div class="stat-card-header w-100 d-flex flex-column align-items-center justify-content-center mb-3">
                    <div class="stat-card-title text-center mb-2 text-success">Students in My Courses</div>
                    <div class="stat-card-icon mb-2" style="background-color: rgba(185,16,16,0.1); color: #b91010; border-radius:50%; width:48px; height:48px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                </div>
                <div class="table-responsive w-100 text-success">
                    <table class="modern-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Semester</th>
                                <th>Course Unit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $mysqli->prepare("SELECT scs.id AS scs_id, scs.semester_course_id, s.id AS student_id, s.name, s.email, p.name as program, sem.semester_name, cu.name as course_unit FROM student_courses scs JOIN students s ON scs.student_id = s.id JOIN semester_courses sc ON scs.semester_course_id = sc.id JOIN course_units cu ON sc.course_unit_id = cu.id JOIN semesters sem ON sc.semester_id = sem.id JOIN programs p ON cu.program_id = p.id WHERE sc.teacher_id = ?");
                            $stmt->bind_param("s", $check);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['student_id']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['program']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['semester_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['course_unit']) . '</td>';
                                echo '<td style="white-space:nowrap;">';
                                // Edit -> edit student; Delete -> request removal
                                echo '<a class="action-btn btn-edit" href="editStudent.php?id=' . urlencode($row['student_id']) . '"><i class="fas fa-edit"></i> Edit</a> ';
                                echo '<form method="post" action="deleteStudentCourse.php" style="display:inline-block;margin-left:6px;">
                                        <input type="hidden" name="scs_id" value="' . htmlspecialchars($row['scs_id']) . '">
                                        <button type="submit" class="action-btn btn-delete" onclick="return confirm(\'Remove student from this course?\')"><i class="fas fa-trash"></i> Delete</button>
                                    </form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
