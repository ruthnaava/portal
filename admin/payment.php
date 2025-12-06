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

// Fetch payments for current month/year
$payments = [];
$curMonth = date('m');
$curYear = date('Y');
$res = $mysqli->query("SELECT * FROM payment WHERE month = '$curMonth' AND year = '$curYear'");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $payments[] = $row;
    }
}

// Fetch students and semesters for payment form
$students = [];
$res = $mysqli->query("SELECT id, name FROM students ORDER BY name");
if ($res) { while ($row = $res->fetch_assoc()) { $students[] = $row; } }
$semesters = [];
$res = $mysqli->query("SELECT id, semester_name FROM semesters ORDER BY semester_name");
if ($res) { while ($row = $res->fetch_assoc()) { $semesters[] = $row; } }

// Handle add payment
$successMsg = $errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $studentid = $_POST['studentid'];
    $amount = $_POST['amount'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $semester_id = $_POST['semester_id'];
    if ($studentid && $amount && $month && $year && $semester_id) {
        $stmt = $mysqli->prepare("INSERT INTO payment (studentid, amount, month, year, semester_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $studentid, $amount, $month, $year, $semester_id);
        if ($stmt->execute()) {
            $successMsg = "Payment added successfully.";
        } else {
            $errorMsg = "Failed to add payment: " . $stmt->error;
        }
    } else {
        $errorMsg = "All fields are required.";
    }
}

// Pagination for payments
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pageSize = 25;
$offset = ($page - 1) * $pageSize;
$totalPayments = 0;
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM payment");
if ($res) { $totalPayments = $res->fetch_assoc()['cnt']; }
$totalPages = ceil($totalPayments / $pageSize);
$payments = [];
$res = $mysqli->query("SELECT * FROM payment ORDER BY id DESC LIMIT $pageSize OFFSET $offset");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $payments[] = $row;
    }
}

// Handle update payment (for balance)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment'])) {
    $id = $_POST['payment_id'];
    $amount = $_POST['amount'];
    if ($id && $amount) {
        $stmt = $mysqli->prepare("UPDATE payment SET amount = ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $id);
        if ($stmt->execute()) {
            $successMsg = "Payment updated successfully.";
        } else {
            $errorMsg = "Failed to update payment: " . $stmt->error;
        }
    } else {
        $errorMsg = "All fields are required for update.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Admin Panel | MIU SCIENCE FACULTY PORTAL</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content-spacer { min-height: 70px; }
        .main-content { margin-top: 70px; margin-left: 260px; padding: 0; transition: margin-left 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
        .payment-dashboard {
            margin: 40px 0 0 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2rem;
            max-width: 100%;
            width: 100%;
        }
        .payment-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 1.2rem; }
        .payment-table {
            width: 100%; border-collapse: collapse; margin-bottom: 2rem; background: #fff;
        }
        .payment-table th, .payment-table td {
            padding: 0.7rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left;
        }
        .payment-table th { background: #f1f5f9; }
        .payment-table tr:last-child td { border-bottom: none; }
        .autocomplete-suggestions {
            position: absolute;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            max-height: 180px;
            overflow-y: auto;
            z-index: 3000;
            min-width: 220px;
            width: auto;
            max-width: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            left: 0;
            right: 0;
        }
        .autocomplete-suggestion {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }
        .autocomplete-suggestion:hover {
            background: #f1f5f9;
        }
        .form-group { position: relative; }
    </style>
</head>
<body>
    <?php include_once('includes/topbar.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-content-spacer"></div>
    <main class="main-content" id="mainContent">
        <div class="payment-dashboard">
            <div class="payment-title text-success">Payments for <?= date('F Y') ?>
                <button class="btn" style="float:right; background-color: #28a745; color: #fff;" onclick="openAddPaymentModal()"><i class="fas fa-plus"></i> Add Payment</button>
            </div>
            <?php if ($successMsg): ?><div class="msg-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
            <?php if ($errorMsg): ?><div class="msg-error"><?= htmlspecialchars($errorMsg) ?></div><?php endif; ?>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Amount</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($payments) === 0): ?>
                    <tr><td colspan="7" style="text-align:center; color:#888;">No payments found for this month.</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $row): ?>
                        <tr>
                            <td><?= $row['id'] !== null ? htmlspecialchars($row['id']) : '' ?></td>
                            <td><?= $row['studentid'] !== null ? htmlspecialchars($row['studentid']) : '' ?></td>
                            <td><?= $row['amount'] !== null ? htmlspecialchars($row['amount']) : '' ?></td>
                            <td><?= $row['month'] !== null ? htmlspecialchars($row['month']) : '' ?></td>
                            <td><?= $row['year'] !== null ? htmlspecialchars($row['year']) : '' ?></td>
                            <td><?= $row['semester_id'] !== null ? htmlspecialchars($row['semester_id']) : '' ?></td>
                            <td>
                                <button class="action-btn edit" onclick="openUpdatePaymentModal('<?= $row['id'] ?>', '<?= $row['amount'] ?>')" title="Update"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Add Payment Modal -->
        <div class="modal" id="addPaymentModal">
            <div class="modal-content">
                <div class="modal-header">
                    Add Payment
                    <span class="modal-close" onclick="closeAddPaymentModal()">&times;</span>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="add_payment" value="1">
                    <div class="form-group">
                        <label>Student</label>
                        <input type="text" name="student_search" id="studentSearch" placeholder="Type student name or ID" autocomplete="off" required>
                        <input type="hidden" name="studentid" id="studentIdHidden">
                        <div id="studentSuggestions" class="autocomplete-suggestions"></div>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" name="amount" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" name="month" placeholder="e.g. 08" maxlength="2" required>
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" name="year" placeholder="e.g. 2025" maxlength="4" required>
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <select name="semester_id" required>
                            <option value="">Select Semester</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= htmlspecialchars($sem['id']) ?>"><?= htmlspecialchars($sem['semester_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="text-align:right; margin-top:1.2rem;">
                        <button type="button" class="btn" onclick="closeAddPaymentModal()" style="background:#888; margin-right:10px;">Cancel</button>
                        <button type="submit" class="btn" style="background:#28a745; color:#fff;">Add Payment</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Update Payment Modal -->
        <div class="modal" id="updatePaymentModal">
            <div class="modal-content">
                <div class="modal-header">
                    Update Payment
                    <span class="modal-close" onclick="closeUpdatePaymentModal()">&times;</span>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="update_payment" value="1">
                    <input type="hidden" name="payment_id" id="updatePaymentId">
                    <div class="form-group">
                        <label>New Amount</label>
                        <input type="number" name="amount" id="updatePaymentAmount" min="1" required>
                    </div>
                    <div style="text-align:right; margin-top:1.2rem;">
                        <button type="button" class="btn" onclick="closeUpdatePaymentModal()" style="background:#888; margin-right:10px;">Cancel</button>
                        <button type="submit" class="btn">Update Payment</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Pagination -->
        <div style="text-align:center; margin-top:1.5rem;">
            <?php if ($totalPages > 1): ?>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="?page=<?= $p ?>" class="btn" style="margin:0 3px;<?= $p == $page ? 'background:#1d4ed8;' : '' ?>">Page <?= $p ?></a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </main>
    <script>
        function openAddPaymentModal() {
            document.getElementById('addPaymentModal').classList.add('active');
        }
        function closeAddPaymentModal() {
            document.getElementById('addPaymentModal').classList.remove('active');
        }
        function openUpdatePaymentModal(id, amount) {
            document.getElementById('updatePaymentId').value = id;
            document.getElementById('updatePaymentAmount').value = amount;
            document.getElementById('updatePaymentModal').classList.add('active');
        }
        function closeUpdatePaymentModal() {
            document.getElementById('updatePaymentModal').classList.remove('active');
        }
        // Autocomplete for student search
        const students = <?php echo json_encode($students); ?>;
        const searchInput = document.getElementById('studentSearch');
        const suggestionsBox = document.getElementById('studentSuggestions');
        const studentIdHidden = document.getElementById('studentIdHidden');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                suggestionsBox.innerHTML = '';
                if (val.length < 1) return;
                const matches = students.filter(s => s.name.toLowerCase().includes(val) || s.id.toLowerCase().includes(val));
                matches.slice(0, 8).forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-suggestion';
                    div.textContent = s.name + ' (' + s.id + ')';
                    div.onclick = function() {
                        searchInput.value = s.name + ' (' + s.id + ')';
                        studentIdHidden.value = s.id;
                        suggestionsBox.innerHTML = '';
                    };
                    suggestionsBox.appendChild(div);
                });
            });
            searchInput.addEventListener('blur', function() {
                setTimeout(() => { suggestionsBox.innerHTML = ''; }, 200);
            });
        }
        // On submit, ensure studentIdHidden is set
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (form.querySelector('#studentSearch') && !studentIdHidden.value) {
                    e.preventDefault();
                    alert('Please select a student from the suggestions.');
                }
            });
        });
    </script>
    <style>
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.3); align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: #fff; border-radius: 10px; padding: 2rem; min-width: 320px; max-width: 95vw; box-shadow: 0 2px 16px rgba(0,0,0,0.15); }
        .modal-header { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; }
        .modal-close { float: right; font-size: 1.3rem; cursor: pointer; color: #888; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.3rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 1rem; }
        .btn { background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.6rem 1.2rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .msg-success { background: #d1fae5; color: #065f46; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
        .msg-error { background: #fee2e2; color: #991b1b; border-radius: 6px; padding: 0.7rem 1.2rem; margin-bottom: 1.2rem; font-size: 1rem; display: inline-block; }
    </style>
</body>
</html>
