<?php
// shareNotes.php - Teacher uploads/shares notes with students
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

$success = $error = '';

// Fetch teacher's course units
$stmt = $mysqli->prepare("SELECT sc.id as semester_course_id, cu.name as course_unit, cu.code, sc.semester_id, cu.id as course_unit_id FROM semester_courses sc JOIN course_units cu ON sc.course_unit_id = cu.id WHERE sc.teacher_id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$courses = $stmt->get_result();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_note'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $course_unit_id = $_POST['course_unit_id'];
    $semester_id = $_POST['semester_id'];
    if (!empty($_FILES['note_file']['name'])) {
        $base_dir = dirname(__DIR__, 2) . '/documents';
        $notes_dir = $base_dir . '/notes/';
        // Try to create the directories if they do not exist
        if (!is_dir($base_dir)) {
            if (!mkdir($base_dir, 0777, true)) {
                $error = 'Failed to create base documents directory.';
            }
        }
        if (!is_dir($notes_dir)) {
            if (!mkdir($notes_dir, 0777, true)) {
                $error = 'Failed to create notes directory.';
            }
        }
        if (!$error) {
            $filename = basename($_FILES['note_file']['name']);
            $safe_filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
            $target_file = $notes_dir . $safe_filename;
            if (move_uploaded_file($_FILES['note_file']['tmp_name'], $target_file)) {
                $relpath = 'documents/notes/' . $safe_filename;
                $stmt2 = $mysqli->prepare("INSERT INTO notes (teacherid, course_unit_id, semester_id, title, description, filepath) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt2->bind_param("ssssss", $check, $course_unit_id, $semester_id, $title, $description, $relpath);
                if ($stmt2->execute()) {
                    $success = 'Note uploaded and shared successfully.';
                } else {
                    $error = 'Database error: Could not save note.';
                }
            } else {
                $error = 'Failed to upload file. Check folder permissions.';
            }
        }
    } else {
        $error = 'Please select a file to upload.';
    }
}
// Fetch notes shared by this teacher
$stmt3 = $mysqli->prepare("SELECT n.*, cu.name as course_unit, s.semester_name FROM notes n JOIN course_units cu ON n.course_unit_id = cu.id JOIN semesters s ON n.semester_id = s.id WHERE n.teacherid = ? ORDER BY n.created_at DESC");
$stmt3->bind_param("s", $check);
$stmt3->execute();
$notes = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Notes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>

<body>
<div class="main-content bg-light min-vh-100 p-4 text-dark">
    <div class="page-header mb-4">
        <h1 class="page-title text-success fw-bold">Share Notes & Documents</h1>
        <div class="page-subtitle text-muted">Upload files for your students. Files are stored under documents/notes/</div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm mb-4">
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Course Unit</label>
          <select name="course_unit_id" class="form-select" required onchange="updateSemester(this)">
            <option value="">-- Select Course Unit --</option>
            <?php $courses->data_seek(0); while ($row = $courses->fetch_assoc()): ?>
              <option value="<?php echo $row['course_unit_id']; ?>" data-semester="<?php echo $row['semester_id']; ?>">
                <?php echo htmlspecialchars($row['course_unit']) . ' (' . htmlspecialchars($row['code']) . ')'; ?>
              </option>
            <?php endwhile; ?>
          </select>
          <input type="hidden" name="semester_id" id="semester_id" value="">
        </div>

        <div class="col-md-6">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">File</label>
          <input type="file" name="note_file" class="form-control" required>
        </div>

        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" rows="2" class="form-control" placeholder="Optional short description"></textarea>
        </div>

        <div class="col-12 text-end">
          <button type="submit" name="upload_note" class="btn btn-success">
            <i class="fas fa-upload"></i> Upload & Share
          </button>
        </div>
      </form>
    </div>

    <!-- Notes Table -->
    <h2 class="fw-bold text-success mb-3">My Shared Notes</h2>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-success text-center">
          <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Course Unit</th>
            <th>Semester</th>
            <th>Description</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($n = $notes->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($n['created_at']); ?></td>
            <td><?php echo htmlspecialchars($n['title']); ?></td>
            <td><?php echo htmlspecialchars($n['course_unit']); ?></td>
            <td><?php echo htmlspecialchars($n['semester_name']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($n['description'])); ?></td>
            <td class="text-center">
              <a href="<?php echo htmlspecialchars($n['filepath']); ?>" target="_blank" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download"></i> Download
              </a>
              <td><?php echo date("d M Y, h:i A", strtotime($n['created_at'])); ?></td>

            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function updateSemester(sel) {
      var semester = sel.options[sel.selectedIndex].getAttribute('data-semester');
      document.getElementById('semester_id').value = semester;
    }
  </script>
                <tbody>
                    <?php while ($n = $notes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($n['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($n['title']); ?></td>
                        <td><?php echo htmlspecialchars($n['course_unit']); ?></td>
                        <td><?php echo htmlspecialchars($n['semester_name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($n['description'])); ?></td>
                        <td><a href="<?php echo htmlspecialchars($n['filepath']); ?>" target="_blank" class="action-btn btn-manage"><i class="fas fa-download"></i> Download</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateSemester(sel) {
        var semester = sel.options[sel.selectedIndex].getAttribute('data-semester');
        document.getElementById('semester_id').value = semester;
    }
</script>
</body>
</html>
