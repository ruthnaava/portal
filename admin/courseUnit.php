<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../service/mysqlcon.php');
$check = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : null;
if ($check) {
    $stmt = $mysqli->prepare("SELECT name FROM admin WHERE id = ?");
    $stmt->bind_param("s", $check);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $login_session = $loged_user_name = $row ? $row['name'] : null;
} else {
    $login_session = null;
}
if (!isset($login_session) || !$login_session) {
    header("Location:../../");
    exit();
}

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $del_stmt = $mysqli->prepare("DELETE FROM course_units WHERE id = ?");
    $del_stmt->bind_param("s", $delete_id);
    $del_stmt->execute();
    $del_stmt->close();
    header("Location: courseUnit.php?msg=deleted");
    exit();
}
// Handle edit action (update course unit)
$edit_unit = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $mysqli->prepare("SELECT id, name, code, credit_hours, description FROM course_units WHERE id = ?");
    $stmt->bind_param("s", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_unit = $result->fetch_assoc();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_unit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $code = $_POST['code'];
    $credit_hours = $_POST['credit_hours'];
    $description = $_POST['description'];
    $stmt = $mysqli->prepare("UPDATE course_units SET name=?, code=?, credit_hours=?, description=? WHERE id=?");
    $stmt->bind_param("ssiss", $name, $code, $credit_hours, $description, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: courseUnit.php?msg=updated");
    exit();
}
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Fetch all course units
$units = [];
$res = $mysqli->query("SELECT id, name, code, credit_hours, description FROM course_units ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $units[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Units - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 64px; }
        .courses-card { border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border: none; margin-top: 48px; background: #fff; }
        .courses-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem 0.5rem 1.5rem; }
        .courses-card-title { font-size: 1.3rem; font-weight: 600; color: #2a2a2a; }
        .courses-card-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .courses-card-body { padding: 0.5rem 1.5rem 1.5rem 1.5rem; }
        .courses-actions { display: flex; gap: 1.2rem; flex-wrap: wrap; margin-bottom: 0.7rem; }
        .courses-action-link { color: #2563eb; font-weight: 500; text-decoration: none; transition: color 0.2s; }
        .courses-action-link:hover { color: #1d4ed8; text-decoration: underline; }
        .courses-card-desc { color: #666; font-size: 0.97rem; }
        .courses-table-container { margin-top: 2.5rem; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); padding: 1.5rem; }
        .courses-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .courses-table th, .courses-table td { padding: 0.75rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left; }
        .courses-table th { background: #f7fafc; font-weight: 600; color: #2a2a2a; }
        .courses-table tr:last-child td { border-bottom: none; }
        .action-btn { border: none; background: none; color: #2563eb; font-size: 1.1rem; cursor: pointer; margin-right: 0.5rem; transition: color 0.2s; }
        .action-btn.delete { color: #e11d48; }
        .action-btn.edit { color: #059669; }
        .action-btn:hover { opacity: 0.8; }
        .msg-success { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        @media (max-width: 600px) { .courses-card-header, .courses-card-body, .courses-table-container { padding: 1rem; } .courses-actions { gap: 0.7rem; } .courses-table th, .courses-table td { padding: 0.5rem 0.5rem; } }
        .main-content-spacer { min-height: 70px; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.3); align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: #fff; border-radius: 10px; padding: 2rem; min-width: 320px; max-width: 95vw; box-shadow: 0 2px 16px rgba(0,0,0,0.15); }
        .modal-header { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; }
        .modal-close { float: right; font-size: 1.3rem; cursor: pointer; color: #888; }
        .edit-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 2rem; }
        .edit-form-grid .form-group { margin-bottom: 0; }
        @media (max-width: 600px) { .edit-form-grid { grid-template-columns: 1fr; gap: 1rem; } }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.3rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem; }
        .form-group textarea { resize: vertical; min-height: 60px; }
        .btn { background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.6rem 1.2rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .msg-success { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content container py-5" id="mainContent">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card courses-card">
                    <div class="courses-card-header">
                        <div class="courses-card-title text-success">Manage Course Units</div>
                        <div class="courses-card-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="courses-card-body">
                        <div class="courses-actions">
                            <a class="courses-action-link" style="text-decoration: underline; color: green;" href="addCourseUnit.php"><i class="fas fa-plus"></i> New Course Unit</a>
                        </div>
                        <div class="courses-card-desc text-muted color:green;">Add, view, or delete course units for your school programs.</div>
                    </div>
                </div>
                <?php if ($msg === 'deleted'): ?>
                    <div class="msg-success mt-3">Course unit deleted successfully.</div>
                <?php elseif ($msg === 'updated'): ?>
                    <div class="msg-success mt-3">Course unit updated successfully.</div>
                <?php endif; ?>
                <div class="courses-table-container mt-4">
                    <table class="courses-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Credit Hours</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($units) === 0): ?>
                            <tr><td colspan="6" style="text-align:center; color:#888;">No course units found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($units as $i => $unit): ?>
                                <tr>
                                    <td><?= $i+1 ?></td>
                                    <td><?= htmlspecialchars($unit['name']) ?></td>
                                    <td><?= htmlspecialchars($unit['code']) ?></td>
                                    <td><?= htmlspecialchars($unit['credit_hours']) ?></td>
                                    <td><?= htmlspecialchars($unit['description']) ?></td>
                                    <td>
                                        <a href="?edit=<?= $unit['id'] ?>" class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?delete=<?= $unit['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this course unit?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($edit_unit): ?>
                <div class="modal active" id="editModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            Edit Course Unit
                            <span class="modal-close" onclick="closeModal()">&times;</span>
                        </div>
                        <form method="post" action="">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_unit['id']) ?>">
                            <div class="edit-form-grid">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($edit_unit['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Code</label>
                                    <input type="text" name="code" value="<?= htmlspecialchars($edit_unit['code']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Credit Hours</label>
                                    <input type="number" name="credit_hours" value="<?= htmlspecialchars($edit_unit['credit_hours']) ?>" min="1" required>
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label>Description</label>
                                    <textarea name="description" required><?= htmlspecialchars($edit_unit['description']) ?></textarea>
                                </div>
                            </div>
                            <div style="text-align:right; margin-top:1.2rem;">
                                <button type="button" class="btn" onclick="closeModal()" style="background:#888; margin-right:10px;">Cancel</button>
                                <button type="submit" class="btn" name="update_unit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    function closeModal() {
                        window.location.href = 'courseUnit.php';
                    }
                </script>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
