<?php
// notes.php - Student view/download notes shared by teachers for all semesters and all course units in their program
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');

// Get all semesters for the student's program
$semesters = [];
$stmt = $mysqli->prepare("SELECT s.id, s.semester_name FROM student_enrollments se JOIN academic_years ay ON se.program_id = ay.program_id JOIN semesters s ON ay.id = s.year_id WHERE se.student_id = ? GROUP BY s.id ORDER BY s.start_date DESC");
$stmt->bind_param("s", $check);
$stmt->execute();
$stmt->bind_result($sem_id, $sem_name);
while ($stmt->fetch()) {
    $semesters[] = ['id' => $sem_id, 'name' => $sem_name];
}
$stmt->close();

// For each semester, get notes
$all_notes = [];
foreach ($semesters as $sem) {
    $stmt2 = $mysqli->prepare("SELECT n.*, cu.name as course_unit, cu.code, t.name as teacher_name FROM notes n JOIN course_units cu ON n.course_unit_id = cu.id JOIN teachers t ON n.teacherid = t.id WHERE n.semester_id = ? ORDER BY n.created_at DESC");
    $stmt2->bind_param("s", $sem['id']);
    $stmt2->execute();
    $notes = $stmt2->get_result();
    $all_notes[$sem['id']] = ['name' => $sem['name'], 'notes' => $notes];
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Notes & Documents</title>
    <link rel='stylesheet' href='css/student.css'>
</head>
<body>
    <?php include('includes/topbar.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <div class='main-content'>
        <h2 class="page-title">Notes & Documents</h2>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <input id="notesSearch" type="search" class="modern-input" placeholder="Search notes by title, course or teacher">
            </div>
            <div style="min-width:160px;text-align:right;">
                <a href="notes.php" class="btn btn-success">Refresh</a>
            </div>
        </div>
        <?php if (!empty($all_notes)): ?>
            <?php foreach ($all_notes as $sem_id => $semdata): ?>
                <div class="card" style="margin-top:1.25rem;">
                    <h3 style="margin-bottom:0.75rem;">Semester: <?php echo htmlspecialchars($semdata['name']); ?></h3>
                    <?php if ($semdata['notes'] && $semdata['notes']->num_rows > 0): ?>
                    <div style="overflow-x:auto;">
                    <table class='modern-table'>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Course Unit</th>
                                <th>Teacher</th>
                                <th>Description</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($n = $semdata['notes']->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($n['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($n['title']); ?></td>
                                <td><?php echo htmlspecialchars($n['course_unit']) . ' (' . htmlspecialchars($n['code']) . ')'; ?></td>
                                <td><?php echo htmlspecialchars($n['teacher_name']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($n['description'])); ?></td>
                                <td><a href="../../<?php echo htmlspecialchars($n['filepath']); ?>" target="_blank" class="btn btn-success"><i class="fas fa-download"></i> Download</a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                    <script>
                        // Client-side search across table rows within this semester card
                        (function(){
                            var input = document.getElementById('notesSearch');
                            if (!input) return;
                            input.addEventListener('input', function(){
                                var q = this.value.trim().toLowerCase();
                                var tables = document.querySelectorAll('.modern-table tbody');
                                tables.forEach(function(tb){
                                    Array.from(tb.querySelectorAll('tr')).forEach(function(tr){
                                        var text = tr.innerText.toLowerCase();
                                        tr.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none';
                                    });
                                });
                            });
                        })();
                    </script>
                    <?php else: ?>
                        <div class="card" style="padding:0.8rem; margin:0.5rem 0; background:transparent; box-shadow:none;"><p class="muted">No notes or documents found for this semester.</p></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="muted">No notes or documents found for your program.</p>
        <?php endif; ?>
    </div>
</body>
</html>
