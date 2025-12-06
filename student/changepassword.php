<?php
// changepassword.php - Student change password and edit profile
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('main.php');

$success = $error = '';

// Fetch student profile
$stmt = $mysqli->prepare("SELECT name, phone, email, dob, address FROM students WHERE id = ? LIMIT 1");
$stmt->bind_param("s", $check);
$stmt->execute();
$stmt->bind_result($name, $phone, $email, $dob, $address);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_name = trim($_POST['name']);
        $new_phone = trim($_POST['phone']);
        $new_email = trim($_POST['email']);
        $new_dob = trim($_POST['dob']);
        $new_address = trim($_POST['address']);
        $stmt = $mysqli->prepare("UPDATE students SET name=?, phone=?, email=?, dob=?, address=? WHERE id=?");
        $stmt->bind_param("ssssss", $new_name, $new_phone, $new_email, $new_dob, $new_address, $check);
        if ($stmt->execute()) {
            $success = 'Profile updated successfully!';
            $name = $new_name; $phone = $new_phone; $email = $new_email; $dob = $new_dob; $address = $new_address;
        } else {
            $error = 'Failed to update profile.';
        }
        $stmt->close();
    } elseif (isset($_POST['change_password'])) {
        $old = $_POST['old_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        if ($new !== $confirm) {
            $error = 'New passwords do not match!';
        } else {
            $stmt = $mysqli->prepare("SELECT password FROM users WHERE userid = ? AND usertype = 'student' LIMIT 1");
            $stmt->bind_param("s", $check);
            $stmt->execute();
            $stmt->bind_result($dbpass);
            $stmt->fetch();
            $stmt->close();
            if ($dbpass !== $old) {
                $error = 'Old password is incorrect!';
            } else {
                $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE userid = ? AND usertype = 'student'");
                $stmt->bind_param("ss", $new, $check);
                if ($stmt->execute()) {
                    $success = 'Password changed successfully!';
                } else {
                    $error = 'Failed to change password.';
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Change Password & Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
    <?php include('includes/topbar.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <div class='main-content'>
        <h2>Change Password & Edit Profile</h2>
        <?php if ($success): ?><div class='alert-success'><?php echo $success; ?></div><?php endif; ?>
        <?php if ($error): ?><div class='alert-error'><?php echo $error; ?></div><?php endif; ?>
        <div class="profile-password-row">
            <div class='bg-white p-4 rounded shadow-sm me-4'>
                <h3>Edit Profile</h3>
                <form method='post' class='modern-form'>
                    <input type='hidden' name='update_profile' value='1'>
                    <label>Name: <input type='text' name='name' value='<?php echo htmlspecialchars($name); ?>' required></label>
                    <label>Phone: <input type='text' name='phone' value='<?php echo htmlspecialchars($phone); ?>' required></label>
                    <label>Email: <input type='email' name='email' value='<?php echo htmlspecialchars($email); ?>' required></label>
                    <label>Date of Birth: <input type='date' name='dob' value='<?php echo htmlspecialchars($dob); ?>' required></label>
                    <label>Address: <input type='text' name='address' value='<?php echo htmlspecialchars($address); ?>' required></label>
                    <button type='submit' class='btn btn-success'>Update Profile</button>
                </form>
            </div>
            <div class='bg-white p-4 rounded shadow-sm'>
                <h3>Change Password</h3>
                <form method='post' class='modern-form'>
                    <input type='hidden' name='change_password' value='1'>
                    <label>Old Password: <input type='password' name='old_password' required></label>
                    <label>New Password: <input type='password' name='new_password' required></label>
                    <label>Confirm New Password: <input type='password' name='confirm_password' required></label>
                    <button type='submit' class='btn btn-success'>Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
