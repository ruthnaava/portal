<?php
// inquiry.php - Student can send email to teachers for inquiry
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');

$success = $error = '';

// Get all teachers for student's current semester courses
$stmt = $mysqli->prepare("SELECT DISTINCT t.id, t.name, t.email FROM teachers t JOIN semester_courses sc ON t.id = sc.teacher_id JOIN student_courses scc ON scc.semester_course_id = sc.id WHERE scc.student_id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$teachers = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_id'], $_POST['subject'], $_POST['message'])) {
    $teacher_id = $_POST['teacher_id'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    // Get teacher email
    $stmt2 = $mysqli->prepare("SELECT email FROM teachers WHERE id = ?");
    $stmt2->bind_param("s", $teacher_id);
    $stmt2->execute();
    $stmt2->bind_result($teacher_email);
    $stmt2->fetch();
    $stmt2->close();
    if ($teacher_email) {
        $headers = "From: noreply@schoolportal.com\r\nReply-To: noreply@schoolportal.com";
        if (mail($teacher_email, $subject, $message, $headers)) {
            $success = 'Inquiry sent successfully!';
        } else {
            $error = 'Failed to send email. Please try again later.';
        }
    } else {
        $error = 'Teacher email not found.';
    }
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Contact Teacher</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <?php include('includes/topbar.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <div class='main-content'>
        <h2 class="page-title">Contact Teacher / Inquiry</h2>
        <?php if ($success): ?>
            <div class='alert-success'><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class='alert-error'><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card" style="max-width:720px;margin:0 auto;">
            <h3><i class="bg-success"></i> Send an Inquiry</h3>
            <form method='post' class='modern-form' style='width:100%'>
                <label for='teacher_id'><strong>To Teacher</strong></label>
                <select name='teacher_id' id='teacher_id' class='modern-input' required>
                    <option value=''>-- Select Teacher --</option>
                    <?php while ($t = $teachers->fetch_assoc()): ?>
                        <option value='<?php echo $t['id']; ?>'><?php echo htmlspecialchars($t['name']) . ' (' . htmlspecialchars($t['email']) . ')'; ?></option>
                    <?php endwhile; ?>
                </select>

                <label for='subject'><strong>Subject</strong></label>
                <input type='text' name='subject' id='subject' class='modern-input' required>

                <label for='message'><strong>Message</strong></label>
                <textarea name='message' id='message' rows='6' class='modern-input' required></textarea>

                <div style="display:flex;justify-content:flex-end;gap:0.6rem;">
                    <button type='submit' class='btn btn-success'><i class='fas fa-paper-plane'></i> Send Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
