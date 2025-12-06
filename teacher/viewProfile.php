<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');

// Handle profile update
$success = $error = '';
$edit_mode = false;
if (isset($_POST['edit_profile'])) {
    $edit_mode = true;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $sex = trim($_POST['sex']);
    $dob = trim($_POST['dob']);
    $hiredate = trim($_POST['hiredate']);
    $salary = trim($_POST['salary']);
    $update_pass = false;
    $new_password = '';
    if (!empty($_POST['password'])) {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $new_password = $_POST['password'];
            $update_pass = true;
        } else {
            $error = 'Passwords do not match!';
            $edit_mode = true;
        }
    }
    if (!$error) {
        $stmt = $mysqli->prepare("UPDATE teachers SET name=?, phone=?, email=?, sex=?, dob=?, hiredate=?, salary=? WHERE id=?");
        $stmt->bind_param("ssssssds", $name, $phone, $email, $sex, $dob, $hiredate, $salary, $check);
        if ($stmt->execute()) {
            if ($update_pass) {
                $stmt2 = $mysqli->prepare("UPDATE users SET password=? WHERE userid=?");
                $stmt2->bind_param("ss", $new_password, $check);
                $stmt2->execute();
            }
            $success = 'Profile updated successfully.';
        } else {
            $error = 'Failed to update profile.';
            $edit_mode = true;
        }
    }
}

// Fetch teacher profile
$stmt = $mysqli->prepare("SELECT id, name, phone, dob, email, sex, hiredate, salary FROM teachers WHERE id = ?");
$stmt->bind_param("s", $check);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="icon" type="image/x-icon" href="../../source/favicon.ico">
    <style>
        .profile-label { font-weight: bold; display: inline-block; width: 140px; }
        .profile-value { display: inline-block; margin-bottom: 8px; }
        .profile-actions { margin-top: 1rem; }
        .alert-success { color: #155724; background: #05c615ff; border: 1px solid #c3e6cb; padding: 8px; border-radius: 4px; margin-bottom: 10px; }
        .alert-error { color: #721c24; background: #e60013ff; border: 1px solid #f5c6cb; padding: 8px; border-radius: 4px; margin-bottom: 10px; }
        .modern-btn { background: #e3e1e1ff; color: #fff; border: none; padding: 8px 18px; border-radius: 4px; cursor: pointer; margin-right: 8px; }
        .modern-btn:hover { background: #a30909ff; }
        .modern-input { padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; width: 60%; margin-bottom: 10px; }
        .profile-info { max-width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
        <div class="main-content bg-light text-success min-vh-100 p-4" id="mainContent" >
            <div class="page-header text-center">
                <h1 class="page-title fw-bold">My Profile</h1>
            </div>
            <div class="dashboard-grid">
                <div class="stat-card" style="background:white; color:#000; border-radius:14px;">
                   
                    <?php if ($success): ?>
                        <div class="alert-success"><?php echo $success; ?></div>
                    <?php elseif ($error): ?>
                        <div class="alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($row): ?>
                        <ul class="nav nav-tabs mb-3 justify-content-center text-success" id="profileTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo !$edit_mode ? 'active' : ''; ?>" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button" role="tab" aria-controls="view" aria-selected="<?php echo !$edit_mode ? 'true' : 'false'; ?>">Profile Info</button>
                            </li>
                           
                        </ul>
                        <div class="tab-content mt-3 text-success" id="profileTabContent">
                            <div class="tab-pane fade <?php echo !$edit_mode ? 'show active' : ''; ?>" id="view" role="tabpanel" aria-labelledby="view-tab">
                                <div class="profile-info mb-3 w-100">
                                    <div><span class="profile-label">ID:</span> <span class="profile-value"><?php echo htmlspecialchars($row['id']); ?></span></div>
                                    <div><span class="profile-label">Name:</span> <span class="profile-value"><?php echo htmlspecialchars($row['name']); ?></span></div>
                                    <div><span class="profile-label">Phone:</span> <span class="profile-value"><?php echo htmlspecialchars($row['phone']); ?></span></div>
                                    <div><span class="profile-label">Date of Birth:</span> <span class="profile-value"><?php echo htmlspecialchars($row['dob']); ?></span></div>
                                    <div><span class="profile-label">Email Address:</span> <span class="profile-value"><?php echo htmlspecialchars($row['email']); ?></span></div>
                                    <div><span class="profile-label">Sex:</span> <span class="profile-value"><?php echo htmlspecialchars($row['sex']); ?></span></div>
                                    <div><span class="profile-label">Hire Date:</span> <span class="profile-value"><?php echo htmlspecialchars($row['hiredate']); ?></span></div>
                                    <div><span class="profile-label">Salary:</span> <span class="profile-value"><?php echo htmlspecialchars($row['salary']); ?></span></div>
                                    <form method="post" class="profile-actions">
                                        <button type="submit" name="edit_profile" class="btn btn-success rounded-pill px-4"><i class="fas fa-edit"></i> Edit Profile</button>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade <?php echo $edit_mode ? 'show active' : ''; ?>" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                                <form method="post" class="profile-info" style="margin-top:1rem;">
                                    <div><span class="profile-label">ID:</span> <span class="profile-value"><?php echo htmlspecialchars($row['id']); ?></span></div>
                                    <label for="name" class="profile-label ">Name:</label>
                                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="modern-input" required><br />
                                    <label for="phone" class="profile-label">Phone:</label>
                                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" class="modern-input" required><br />
                                    <label for="dob" class="profile-label">Date of Birth:</label>
                                    <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($row['dob']); ?>" class="modern-input" required><br />
                                    <label for="email" class="profile-label">Email Address:</label>
                                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="modern-input" required><br />
                                    <label for="sex" class="profile-label">Sex:</label>
                                    <select name="sex" id="sex" class="modern-input" required>
                                        <option value="Male" <?php if ($row['sex'] === 'Male') echo 'selected'; ?>>Male</option>
                                        <option value="Female" <?php if ($row['sex'] === 'Female') echo 'selected'; ?>>Female</option>
                                    </select><br />
                                    <label for="hiredate" class="profile-label">Hire Date:</label>
                                    <input type="date" name="hiredate" id="hiredate" value="<?php echo htmlspecialchars($row['hiredate']); ?>" class="modern-input" required><br />
                                    <label for="salary" class="profile-label">Salary:</label>
                                    <input type="number" name="salary" id="salary" value="<?php echo htmlspecialchars($row['salary']); ?>" class="modern-input" required><br />
                                    <hr>
                                    <label for="password" class="profile-label">New Password:</label>
                                    <input type="password" name="password" id="password" class="modern-input"><br />
                                    <label for="confirm_password" class="profile-label">Confirm Password:</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="modern-input"><br />
                                    <div class="profile-actions">
                                        <button type="submit" name="update_profile" class="btn btn-danger rounded-pill px-4"><i class="fas fa-save"></i> Save Changes</button>
                                        <a href="viewProfile.php" class="modern-btn" style="background:#6c757d;">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class='stat-card-title' style='color:red;'>Profile not found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
</body>
</html>