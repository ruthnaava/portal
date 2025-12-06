<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('main.php');

// Fetch student info
$stmt = $mysqli->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

$name = $student['name'] ?? 'Student';

// Fetch number of courses in current semester
$stmt = $mysqli->prepare("SELECT COUNT(DISTINCT sc.id) as course_count FROM student_courses sc JOIN semester_courses semc ON sc.semester_course_id = semc.id JOIN student_enrollments se ON se.student_id = sc.student_id WHERE sc.student_id = ? AND se.status = 'Active' AND semc.semester_id = se.current_semester_id");
$stmt->bind_param("s", $check);
$stmt->execute();
$stmt->bind_result($course_count);
$stmt->fetch();
$stmt->close();

// Fetch today's classes (timetable)
$stmt = $mysqli->prepare("SELECT cu.name, t.start_time FROM timetable t JOIN semester_courses sc ON t.semester_course_id = sc.id JOIN course_units cu ON sc.course_unit_id = cu.id JOIN student_courses scc ON scc.semester_course_id = sc.id JOIN student_enrollments se ON se.student_id = scc.student_id WHERE scc.student_id = ? AND se.status = 'Active' AND sc.semester_id = se.current_semester_id AND t.day_of_week = DAYOFWEEK(CURDATE()) ORDER BY t.start_time");
$stmt->bind_param("s", $check);
$stmt->execute();
$today_classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch next assignment (dummy: you can join with assignments table if exists)
$next_assignment = null;
// Fetch upcoming exams
$stmt = $mysqli->prepare("SELECT cu.name, e.examdate FROM exam_schedule e JOIN course_units cu ON e.course_unit_id = cu.id JOIN semester_courses sc ON sc.course_unit_id = cu.id JOIN student_courses scc ON scc.semester_course_id = sc.id WHERE scc.student_id = ? AND e.examdate >= CURDATE() ORDER BY e.examdate ASC LIMIT 2");
$stmt->bind_param("s", $check);
$stmt->execute();
$exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch performance (average mark)
$stmt = $mysqli->prepare("SELECT AVG(mark) FROM grades WHERE student_id = ? AND mark IS NOT NULL");
$stmt->bind_param("s", $check);
$stmt->execute();
$stmt->bind_result($avg_mark);
$stmt->fetch();
$stmt->close();
$avg_mark = $avg_mark ? round($avg_mark) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Student styles -->
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <!-- Topbar -->
    <?php include_once('includes/topbar.php'); ?>
    <!-- Sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    <!-- Main Content -->
    <main class="main-content bg-light text-dark ">
        <div class="dashboard-header mb-4 text-success">
            <h1 style="font-size: 2rem;">Student Dashboard</h1>
            <p>Welcome back! Here's what's happening today.</p>
        </div>
        
        <div class="card-grid">
            <div class="card">
                <h3><i class="bg-success align-items-center"></i> My Courses</h3>
                <p>You are enrolled in <?php echo $course_count; ?> course<?php echo $course_count == 1 ? '' : 's'; ?> this semester.</p>
            </div>
            <div class="card">
                <h3><i class="bg-success align-items-center"></i> Today's Schedule</h3>
                <p>
                <?php if ($today_classes && count($today_classes) > 0): ?>
                    <?php foreach ($today_classes as $class): ?>
                        <?php echo htmlspecialchars($class['name']) . ' (' . htmlspecialchars(substr($class['start_time'],0,5)) . ')<br>'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    No classes today.
                <?php endif; ?>
                </p>
            </div>
            
            <div class="card">
                <h3><i class="bg-success align-items-center"></i> Upcoming Exams</h3>
                <p>
                <?php if ($exams && count($exams) > 0): ?>
                    <?php foreach ($exams as $exam): ?>
                        <?php echo htmlspecialchars($exam['name']) . ' (' . htmlspecialchars($exam['examdate']) . ')<br>'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    No upcoming exams.
                <?php endif; ?>
                </p>
            </div>
            <div class="card">
                <h3><i class="bg-success align-items-center"></i> Performance</h3>
                <p>Your overall grade average is <?php echo $avg_mark; ?>%</p>
            </div>
        </div>
    </main>
    <script>
    // Close sidebar on resize if desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            closeSidebar();
        }
    });
    </script>
</body>
</html>