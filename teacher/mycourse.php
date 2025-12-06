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
    <title>My Course Units</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    </head>
<body>
    <div class="main-content container min-vh-100 py-4 bg-light text-succcess">
        <div class="page-header w-100">
            <h1 class="page-title text-center text-success">My Course Units</h1>
        </div>
        <div class="dashboard-grid w-100 d-flex flex-column align-items-center justify-content-center">
            <div class="stat-card full-width rounded-4 shadow-sm d-flex flex-column align-items-center justify-content-center p-4 w-100" style="max-width:900px;">
                <div class="stat-card-header w-100 d-flex flex-column align-items-center justify-content-center mb-3">
                    <div class="stat-card-title text-center mb-2 text-success">Course Units</div>
                    <div class="stat-card-icon mb-2" style="background-color: rgba(185,16,16,0.1); color: #b91010; border-radius:50%; width:48px; height:48px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                </div>
                <div class="table-responsive w-100 text-success">
                    <table class="modern-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Course Unit</th>
                                <th>Code</th>
                                <th>Semester</th>
                                <th>Program</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $mysqli->prepare("SELECT sc.id as semester_course_id, cu.name, cu.code, s.semester_name, p.name as program FROM semester_courses sc JOIN course_units cu ON sc.course_unit_id = cu.id JOIN semesters s ON sc.semester_id = s.id JOIN programs p ON cu.program_id = p.id WHERE sc.teacher_id = ?");
                            $stmt->bind_param("s", $check);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['code']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['semester_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['program']) . '</td>';
                                echo '<td style="white-space:nowrap;"><a class="action-btn btn-manage" href="awardmarks.php?course=' . urlencode($row['semester_course_id']) . '"><i class="fas fa-marker"></i> Manage</a></td>';
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
