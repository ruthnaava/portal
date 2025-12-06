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
    <title>Teachers Panel - MIU SCIENCE FACULTY PORTAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="teacher.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>



<body> 
    <div class="main-content" id="mainContent" style="min-height:100vh; padding:2rem; text-align:center; ">
        <div class="page-header w-100 d-flex flex-column align-items-center justify-content-center" style="margin-bottom: 1.5rem;">
            <h1 class="page-title text-center text-success" style="font-weight:700;">Dashboard</h1>
            <div class="breadcrumb d-flex flex-row justify-content-center align-items-center gap-2" style="font-size:1rem;">
                <a href="index.php">Home</a>
                <span>--</span>
                <span>Dashboard</span>
            </div>
        </div>
        
    <div class="container-center">
    <div class="dashboard-grid d-flex flex-row flex-wrap justify-content-center align-items-stretch gap-4" style="width:100%;">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title text-light bg-success">My Course Units</div>
                    <div class="stat-card-icon" style="background-color: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="stat-card-value">
                    <?php
                    $stmt = $mysqli->prepare("SELECT COUNT(DISTINCT sc.course_unit_id) FROM semester_courses sc WHERE sc.teacher_id = ?");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($cnt);
                    $stmt->fetch();
                    echo $cnt;
                    $stmt->close();
                    ?>
                </div>
                <a href="mycourse.php" class="menu-link">View All</a>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <link rel="stylesheet" href="../../source/CSS/style.css">
                    <div class="stat-card-title text-black bg-success">My Students</div>
                    <div class="stat-card-icon" style="background-color: rgba(14, 159, 110, 0.1); color: #0e9f6e;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                <div class="stat-card-value">
                    <?php
                    $stmt = $mysqli->prepare("SELECT COUNT(DISTINCT scs.student_id) FROM student_courses scs JOIN semester_courses sc ON scs.semester_course_id = sc.id WHERE sc.teacher_id = ?");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($cnt);
                    $stmt->fetch();
                    echo $cnt;
                    $stmt->close();
                    ?>
                </div>
                <a href="mystudents.php" class="menu-link">View All</a>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title text-light bg-success">Students Attendance Rate</div>
                    <div class="stat-card-icon" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-card-value">
                    <?php
                    $stmt = $mysqli->prepare("SELECT COUNT(a.id) FROM attendance a JOIN semester_courses sc ON a.semester_course_id = sc.id WHERE sc.teacher_id = ? AND a.role = 'student'");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($present);
                    $stmt->fetch();
                    $stmt->close();
                    $stmt = $mysqli->prepare("SELECT COUNT(scs.id) FROM student_courses scs JOIN semester_courses sc ON scs.semester_course_id = sc.id WHERE sc.teacher_id = ?");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($total);
                    $stmt->fetch();
                    $stmt->close();
                    $rate = ($total > 0) ? round(($present / $total) * 100, 1) : 0;
                    echo $rate . '%';
                    ?>
                </div>
                <a href="viewAttendance.php" class="menu-link">View Attendance</a>
            </div>
        </div>
    </div>
    </div>
    <div class="dashboard-advanced container-center">
            <div class="dashboard-section">
                <h2>Recent Grades</h2>
                <table class="modern-table" width="100%">
                    <thead><tr><th>Student</th><th>Course</th><th>Grade</th><th>Exam Type</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php
                    $stmt = $mysqli->prepare("SELECT s.name, cu.name, g.grade, g.exam_type, g.semester_id FROM grades g JOIN students s ON g.student_id = s.id JOIN course_units cu ON g.course_unit_id = cu.id JOIN semester_courses sc ON sc.course_unit_id = cu.id AND sc.semester_id = g.semester_id WHERE sc.teacher_id = ? ORDER BY g.id DESC LIMIT 5");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($student, $course, $grade, $exam_type, $semester_id);
                    while ($stmt->fetch()) {
                        echo "<tr><td>$student</td><td>$course</td><td>$grade</td><td>$exam_type</td><td>$semester_id</td></tr>";
                    }
                    $stmt->close();
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="dashboard-section">
                <h2>Upcoming Exams</h2>
                <table class="modern-table" width="100%">
                    <thead><tr><th>Course</th><th>Semester</th><th>Date</th><th>Time</th></tr></thead>
                    <tbody>
                    <?php
                    $stmt = $mysqli->prepare("SELECT cu.name, sem.semester_name, es.examdate, es.time FROM exam_schedule es JOIN course_units cu ON es.course_unit_id = cu.id JOIN semesters sem ON es.semester_id = sem.id JOIN semester_courses sc ON sc.course_unit_id = cu.id AND sc.semester_id = sem.id WHERE sc.teacher_id = ? AND es.examdate >= CURDATE() ORDER BY es.examdate ASC LIMIT 5");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($course, $semester, $date, $time);
                    while ($stmt->fetch()) {
                        echo "<tr><td>$course</td><td>$semester</td><td>$date</td><td>$time</td></tr>";
                    }
                    $stmt->close();
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="dashboard-section">
                <h2>My Timetable</h2>
                <table class="modern-table" width="100%">
                    <thead><tr><th>Course</th><th>Day</th><th>Start</th><th>End</th><th>Room</th></tr></thead>
                    <tbody>
                    <?php
                    $stmt = $mysqli->prepare("SELECT cu.name, t.day_of_week, t.start_time, t.end_time, t.room FROM timetable t JOIN semester_courses sc ON t.semester_course_id = sc.id JOIN course_units cu ON sc.course_unit_id = cu.id WHERE sc.teacher_id = ? ORDER BY t.day_of_week, t.start_time");
                    $stmt->bind_param("s", $check);
                    $stmt->execute();
                    $stmt->bind_result($course, $day, $start, $end, $room);
                    $days = ['','Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                    while ($stmt->fetch()) {
                        $dayname = isset($days[$day]) ? $days[$day] : $day;
                        echo "<tr><td>$course</td><td>$dayname</td><td>$start</td><td>$end</td><td>$room</td></tr>";
                    }
                    $stmt->close();
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="dashboard-section">
                <h2>Quick Actions</h2>
                <?php
                // small counts for quick action cards
                $reports_count = 0;
                $stmt = $mysqli->prepare("SELECT COUNT(*) FROM report WHERE teacherid = ?");
                $stmt->bind_param('s', $check);
                $stmt->execute();
                $stmt->bind_result($reports_count);
                $stmt->fetch();
                $stmt->close();

                $upcoming_count = 0;
                $stmt = $mysqli->prepare("SELECT COUNT(es.id) FROM exam_schedule es JOIN semester_courses sc ON es.course_unit_id = sc.course_unit_id AND es.semester_id = sc.semester_id WHERE sc.teacher_id = ? AND es.examdate >= CURDATE()");
                $stmt->bind_param('s', $check);
                $stmt->execute();
                $stmt->bind_result($upcoming_count);
                $stmt->fetch();
                $stmt->close();
                ?>

                <div class="quick-cards" style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:0.6rem;">
                    <a href="attendance.php" class="quick-card">
                        <div class="qc-icon" style="background:linear-gradient(90deg,#ecfccb,#bbf7d0);color:#14532d;"><i class="fas fa-calendar-check fa-2x"></i></div>
                        <div class="qc-body">
                            <div class="qc-title">Mark Attendance</div>
                            <div class="qc-copy">Quickly record class attendance</div>
                        </div>
                    </a>

                    <a href="mycourseinfo.php" class="quick-card">
                        <div class="qc-icon" style="background:linear-gradient(90deg,#dbeafe,#bfdbfe);color:#1e40af;"><i class="fas fa-book-open fa-2x"></i></div>
                        <div class="qc-body">
                            <div class="qc-title">View Grades</div>
                            <div class="qc-copy">Manage marks and view class performance</div>
                        </div>
                    </a>

                    <a href="report.php" class="quick-card">
                        <div class="qc-icon" style="background:linear-gradient(90deg,#fef3c7,#fde68a);color:#92400e;"><i class="fas fa-flag fa-2x"></i></div>
                        <div class="qc-body">
                            <div class="qc-title">Student Reports</div>
                            <div class="qc-copy"><?php echo intval($reports_count); ?> pending items</div>
                        </div>
                    </a>

                    <a href="viewProfile.php" class="quick-card">
                        <div class="qc-icon" style="background:linear-gradient(90deg,#f0fdf4,#dcfce7);color:#064e3b;"><i class="fas fa-user fa-2x"></i></div>
                        <div class="qc-body">
                            <div class="qc-title">My Profile</div>
                            <div class="qc-copy">Update your personal info and settings</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
