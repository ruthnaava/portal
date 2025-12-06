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
    <title>My Course Grades</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>
<body>
    <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-4 bg-light text-dark">
        <div class="page-header w-100">
            <h1 class="page-title text-center text-success">My Course Grades</h1>
        </div>
        <div class="dashboard-grid w-70 d-flex flex-column align-items-center justify-content-center">
            <div class="stat-card full-width rounded-4 shadow-sm d-flex flex-column align-items-center justify-content-center p-4 w-100" style="max-width:900px;">
                <div class="stat-card-header w-100 d-flex flex-column align-items-center justify-content-center mb-3">
                    <div class="stat-card-title text-center mb-4 text-success border-bottom ">Grades for My Courses</div>
                    <div class="stat-card-icon mb-2" style="background-color: rgba(185,16,16,0.1); color: #b91010; border-radius:50%; width:48px; height:48px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                </div>
                <div class="table-responsive w-100 text-success">
                    <table class="modern-table" style="width:100%; table-layout:fixed;">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Course Unit</th>
                                <th>Semester</th>
                                <th>Grade</th>
                                <th>Exam Type</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $mysqli->prepare("SELECT g.student_id, s.name as student_name, cu.id as course_unit_id, cu.name as course_unit, sem.id as semester_id, sem.semester_name, g.grade, g.exam_type, g.remarks FROM grades g JOIN students s ON g.student_id = s.id JOIN course_units cu ON g.course_unit_id = cu.id JOIN semesters sem ON g.semester_id = sem.id JOIN semester_courses sc ON sc.course_unit_id = cu.id AND sc.semester_id = sem.id WHERE sc.teacher_id = ?");
                            $stmt->bind_param("s", $check);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['student_id']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['student_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['course_unit']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['semester_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['grade']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['exam_type']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['remarks']) . '</td>';
                                // Action: edit grade
                                echo '<td style="white-space:nowrap;"><a class="action-btn btn-edit" href="grade.php?student_id=' . urlencode($row['student_id']) . '&course_unit_id=' . urlencode($row['course_unit_id']) . '&semester_id=' . urlencode($row['semester_id']) . '"><i class="fas fa-edit"></i> Edit</a></td>';
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
