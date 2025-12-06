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
// Handle form submission
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prog_id = trim($_POST['prog_id']?? '');
    $name = trim($_POST['name'] ?? '');
    $duration = intval($_POST['duration'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    if ($name && $duration > 0) {
        $stmt = $mysqli->prepare("INSERT INTO programs (id,name, duration_years, description) VALUES (?,?, ?, ?)");
        $stmt->bind_param("ssis",$prog_id, $name, $duration, $description);
        if ($stmt->execute()) {
            $success = "Program registered successfully.";
        } else {
            $error = "Failed to register program. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Program - School Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <style>
        :root {
            --primary: #4e54c8;
            --primary-dark: #3f43a9;
            --secondary: #8f94fb;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --bg-light: #f7fafc;
            --white: #ffffff;
            --gray-100: #f7fafc;
            --gray-200: #edf2f7;
            --gray-300: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 8px;
            --radius-lg: 12px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            color: var(--text-primary);
            line-height: 1.5;
        }

        .main-content {
            margin-left: 260px;
            padding: 90px 24px 24px 24px;
            transition: var(--transition);
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .breadcrumb {
            display: flex;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: var(--shadow);
            max-width: 700px;
            margin: 0 auto;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
            color: var(--primary);
            text-align: center;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-size: 1rem;
            font-family: inherit;
            background: var(--white);
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.2);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            box-shadow: var(--shadow-md);
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #f0fff4;
            color: #2d7849;
            border: 1px solid #9ae6b4;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #e53e3e;
            border: 1px solid #feb2b2;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 100px 16px 24px 16px;
            }
        }

        @media (max-width: 768px) {
            .card {
                padding: 24px 16px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <?php include_once('includes/topbar.php'); ?>
    
    <!-- Sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="page-header">
            <h1 class="page-title">Register New Program</h1>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span>/</span>
                <a href="course.php">Programs</a>
                <span>/</span>
                <span>Register</span>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Program Registration Form</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post" autocomplete="off">
                  <div class="form-group">
                    <label for="name" class="form-label">Program code *</label>
                    <input type="text" id="prog_id" name="prog_id" class="form-control" placeholder="Enter program code" required>
                </div>
                <div class="form-group">
                    <label for="name" class="form-label">Program Name *</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter program name" required>
                </div>
                
                <div class="form-group">
                    <label for="duration" class="form-label">Duration (years) *</label>
                    <input type="number" id="duration" name="duration" class="form-control" min="1" max="10" placeholder="Enter duration in years" required>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Enter program description (optional)"></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-plus"></i> Register Program
                </button>
            </form>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle (if needed)
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
        }
        
        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const duration = document.getElementById('duration').value;
                
                if (!name) {
                    e.preventDefault();
                    alert('Please enter a program name.');
                    document.getElementById('name').focus();
                    return false;
                }
                
                if (!duration || duration < 1) {
                    e.preventDefault();
                    alert('Please enter a valid duration (at least 1 year).');
                    document.getElementById('duration').focus();
                    return false;
                }
            });
        }
    </script>
</body>
</html>