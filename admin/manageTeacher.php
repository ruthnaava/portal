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

// Fetch all teachers
$teachers = [];
$res = $mysqli->query("SELECT id, name, email, phone, sex, dob, hiredate, address, salary FROM teachers WHERE deleted_at IS NULL ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $teachers[] = $row;
    }
}

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $del_stmt = $mysqli->prepare("DELETE FROM teachers WHERE id = ?");
    $del_stmt->bind_param("s", $delete_id);
    $del_stmt->execute();
    $del_stmt->close();
    header("Location: manageTeacher.php?msg=deleted");
    exit();
}
// Handle edit action (update teacher)
$edit_teacher = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $mysqli->prepare("SELECT id, name, email, phone, sex, dob, hiredate, address, salary FROM teachers WHERE id = ?");
    $stmt->bind_param("s", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_teacher = $result->fetch_assoc();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_teacher'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $sex = $_POST['sex'];
    $dob = $_POST['dob'];
    $hiredate = $_POST['hiredate'];
    $address = $_POST['address'];
    $salary = $_POST['salary'];
    $stmt = $mysqli->prepare("UPDATE teachers SET name=?, email=?, phone=?, sex=?, dob=?, hiredate=?, address=?, salary=? WHERE id=?");
    $stmt->bind_param("sssssssdss", $name, $email, $phone, $sex, $dob, $hiredate, $address, $salary, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manageTeacher.php?msg=updated");
    exit();
}
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Admin Panel | MIU SCIENCE FACULTY PORTAL</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) {
            .main-content { margin-left: 0; }
        }
        .teachers-table-container {
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 1100px;
            overflow-x: auto;
        }
        .teachers-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .teachers-table th, .teachers-table td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .teachers-table th:nth-child(2), .teachers-table td:nth-child(2),
        .teachers-table th:nth-child(8), .teachers-table td:nth-child(8) {
            max-width: 120px;
        }
        .teachers-table th:nth-child(3), .teachers-table td:nth-child(3) {
            max-width: 160px;
        }
        .teachers-table th:nth-child(7), .teachers-table td:nth-child(7) {
            max-width: 110px;
        }
        .teachers-table th:nth-child(9), .teachers-table td:nth-child(9) {
            max-width: 110px;
        }
        @media (max-width: 600px) {
            .teachers-table-container { padding: 1rem; }
            .teachers-table th, .teachers-table td { padding: 0.5rem 0.5rem; }
            .teachers-header { flex-direction: column; align-items: flex-start; gap: 0.7rem; }
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
        .form-group input, .form-group select {
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
        <div class="teachers-table-container text-success">
            <div class="teachers-header">
                <div class="teachers-title">Manage Teachers</div>
                <div class="teachers-actions">
                    <a class="teachers-action-link" href="addTeacher.php"><i class="fas fa-plus"></i> New Teacher</a>
                </div>
            </div>
            <?php if ($msg === 'deleted'): ?>
                <div class="msg-success">Teacher deleted successfully.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="msg-success">Teacher updated successfully.</div>
            <?php endif; ?>
            <table class="teachers-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Sex</th>
                        <th>DOB</th>
                        <th>Hire Date</th>
                        <th>Address</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($teachers) === 0): ?>
                    <tr><td colspan="10" style="text-align:center; color:#888;">No teachers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($teachers as $i => $teacher): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($teacher['name']) ?></td>
                            <td><?= htmlspecialchars($teacher['email']) ?></td>
                            <td><?= htmlspecialchars($teacher['phone']) ?></td>
                            <td><?= htmlspecialchars($teacher['sex']) ?></td>
                            <td><?= htmlspecialchars($teacher['dob']) ?></td>
                            <td><?= htmlspecialchars($teacher['hiredate']) ?></td>
                            <td><?= htmlspecialchars($teacher['address']) ?></td>
                            <td><?= htmlspecialchars($teacher['salary']) ?></td>
                            <td>
                                <a href="?edit=<?= $teacher['id'] ?>" class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?= $teacher['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this teacher?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($edit_teacher): ?>
        <div class="modal active" id="editModal">
            <div class="modal-content">
                <div class="modal-header">
                    Edit Teacher
                    <span class="modal-close" onclick="closeModal()">&times;</span>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($edit_teacher['id']) ?>">
                    <div class="edit-form-grid">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($edit_teacher['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($edit_teacher['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($edit_teacher['phone']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Sex</label>
                            <select name="sex" required>
                                <option value="Male" <?= $edit_teacher['sex']==='Male'?'selected':'' ?>>Male</option>
                                <option value="Female" <?= $edit_teacher['sex']==='Female'?'selected':'' ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="<?= htmlspecialchars($edit_teacher['dob']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Hire Date</label>
                            <input type="date" name="hiredate" value="<?= htmlspecialchars($edit_teacher['hiredate']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="number" name="salary" value="<?= htmlspecialchars($edit_teacher['salary']) ?>" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($edit_teacher['address']) ?>" required>
                        </div>
                    </div>
                    <div style="text-align:right; margin-top:1.2rem;">
                        <button type="button" class="btn" onclick="closeModal()" style="background:#888; margin-right:10px;">Cancel</button>
                        <button type="submit" class="btn" name="update_teacher">Update</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function closeModal() {
                window.location.href = 'manageTeacher.php';
            }
        </script>
        <?php endif; ?>
    </main>
</body>
</html>
