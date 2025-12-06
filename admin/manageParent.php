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

// Fetch all parents
$parents = [];
$res = $mysqli->query("SELECT id, fathername, mothername, fatherphone, motherphone, address FROM parents WHERE deleted_at IS NULL ORDER BY fathername ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $parents[] = $row;
    }
}

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $del_stmt = $mysqli->prepare("DELETE FROM parents WHERE id = ?");
    $del_stmt->bind_param("s", $delete_id);
    $del_stmt->execute();
    $del_stmt->close();
    header("Location: manageParent.php?msg=deleted");
    exit();
}
// Handle edit action (update parent)
$edit_parent = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $mysqli->prepare("SELECT id, fathername, mothername, fatherphone, motherphone, address FROM parents WHERE id = ?");
    $stmt->bind_param("s", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_parent = $result->fetch_assoc();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_parent'])) {
    $id = $_POST['id'];
    $fathername = $_POST['fathername'];
    $mothername = $_POST['mothername'];
    $fatherphone = $_POST['fatherphone'];
    $motherphone = $_POST['motherphone'];
    $address = $_POST['address'];
    $stmt = $mysqli->prepare("UPDATE parents SET fathername=?, mothername=?, fatherphone=?, motherphone=?, address=? WHERE id=?");
    $stmt->bind_param("ssssss", $fathername, $mothername, $fatherphone, $motherphone, $address, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manageParent.php?msg=updated");
    exit();
}
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parents - Admin Panel | School Management System</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .parents-table-container {
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 900px;
            overflow-x: auto;
        }
        .parents-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .parents-table th, .parents-table td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .parents-table th:nth-child(2), .parents-table td:nth-child(2),
        .parents-table th:nth-child(3), .parents-table td:nth-child(3) {
            max-width: 120px;
        }
        .parents-table th:nth-child(4), .parents-table td:nth-child(4),
        .parents-table th:nth-child(5), .parents-table td:nth-child(5) {
            max-width: 110px;
        }
        .parents-table th:nth-child(6), .parents-table td:nth-child(6) {
            max-width: 180px;
        }
        @media (max-width: 600px) {
            .parents-table-container { padding: 1rem; }
            .parents-table th, .parents-table td { padding: 0.5rem 0.5rem; }
            .parents-header { flex-direction: column; align-items: flex-start; gap: 0.7rem; }
        }
        .modal {
            display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.3); align-items: center; justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #fff; border-radius: 10px; padding: 2rem; min-width: 320px; max-width: 95vw;
            box-shadow: 0 2px 16px rgba(0,0,0,0.15);
        }
        .modal-header { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; }
        .modal-close { float: right; font-size: 1.3rem; cursor: pointer; color: #888; }
        .edit-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem 2rem;
        }
        .edit-form-grid .form-group { margin-bottom: 0; }
        @media (max-width: 600px) {
            .edit-form-grid { grid-template-columns: 1fr; gap: 1rem; }
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.3rem; }
        .form-group input {
            width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem;
        }
        .btn { background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.6rem 1.2rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .msg-success { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="parents-table-container text-success">
            <div class="parents-header">
                <div class="parents-title">Manage Parents</div>
                <div class="parents-actions">
                    <a class="parents-action-link" href="addParent.php"><i class="fas fa-plus"></i> New Parent</a>
                </div>
            </div>
            <?php if ($msg === 'deleted'): ?>
                <div class="msg-success">Parent deleted successfully.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="msg-success">Parent updated successfully.</div>
            <?php endif; ?>
            <table class="parents-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Father Phone</th>
                        <th>Mother Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($parents) === 0): ?>
                    <tr><td colspan="7" style="text-align:center; color:#888;">No parents found.</td></tr>
                <?php else: ?>
                    <?php foreach ($parents as $i => $parent): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($parent['fathername']) ?></td>
                            <td><?= htmlspecialchars($parent['mothername']) ?></td>
                            <td><?= htmlspecialchars($parent['fatherphone']) ?></td>
                            <td><?= htmlspecialchars($parent['motherphone']) ?></td>
                            <td><?= htmlspecialchars($parent['address']) ?></td>
                            <td>
                                <a href="?edit=<?= $parent['id'] ?>" class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?= $parent['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this parent?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($edit_parent): ?>
        <div class="modal active" id="editModal">
            <div class="modal-content">
                <div class="modal-header">
                    Edit Parent
                    <span class="modal-close" onclick="closeModal()">&times;</span>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($edit_parent['id']) ?>">
                    <div class="edit-form-grid">
                        <div class="form-group">
                            <label>Father Name</label>
                            <input type="text" name="fathername" value="<?= htmlspecialchars($edit_parent['fathername']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mother Name</label>
                            <input type="text" name="mothername" value="<?= htmlspecialchars($edit_parent['mothername']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Father Phone</label>
                            <input type="text" name="fatherphone" value="<?= htmlspecialchars($edit_parent['fatherphone']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mother Phone</label>
                            <input type="text" name="motherphone" value="<?= htmlspecialchars($edit_parent['motherphone']) ?>" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($edit_parent['address']) ?>" required>
                        </div>
                    </div>
                    <div style="text-align:right; margin-top:1.2rem;">
                        <button type="button" class="btn" onclick="closeModal()" style="background:#888; margin-right:10px;">Cancel</button>
                        <button type="submit" class="btn" name="update_parent">Update</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function closeModal() {
                window.location.href = 'manageParent.php';
            }
        </script>
        <?php endif; ?>
    </main>
</body>
</html>
