<?php
// Start session and generate CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$login_code = isset($_REQUEST['login']) ? $_REQUEST['login'] : '1';
if ($login_code == "false") {
    $login_message = "Wrong Credentials! Please try again.";
    $color = "#e53e3e"; // Modern red color
} else {
    $login_message = "Please login to continue to your account";
    $color = "#38a169"; // Modern green color
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIU SCIENCE FACULTY PORTAL | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-VkzQbN4Z1cdq9VHtV6nRvWmvMRGsiE9z1FMvx6bMpiKFFitvolG/Gp2gbf28pQ5Q" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">



</head>
<body>
    <div class="login-wrapper d-flex bg-light">
        <div class="login-visual bg-white d-flex flex-column justify-content-center align-items-center p-5">
            <div class="login-visual-content">
                <i class="fas fa-graduation-cap" style="color: #080808ff !important; font-size:2.5rem;"></i>
                <h2 style="color:#de2626ff !important; font-weight:400 !important; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);">MIU SCIENCE FACULTY PORTAL</h2>
                <p style="color: black; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);">Empowering educators, engaging students, and administrators globally through telecommunication</p>
            </div>
        </div>
        
        <div class="login-container">
            <div class="login-header">
                <div class="logo-container">
                    <img src="source/miu/img.png" alt="MIU Logo" class="logo" >
                    
                </div>
                <h1 class="login-title" style="color:#b00 !important; font-weight:700 !important;">MIU SCIENCE FACULTY PORTAL LOGIN</h1>
                <p class="login-subtitle">Access your dashboard</p>
            </div>
            
            <div class="login-message" style="background-color: <?php echo $color; ?>20; color: <?php echo $color; ?>;">
                <i class="fas <?php echo $login_code == 'false' ? 'fa-exclamation-circle' : 'fa-info-circle'; ?>"></i>
                <span><?php echo $login_message; ?></span>
            </div>
            
            <form class="login-form" action="service/check.access.php" onsubmit="return loginValidate();" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="myid" class="form-label text-success">Login ID</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-user"></i>
                        <input type="text" class="form-control" id="myid" name="myid" placeholder="Enter your login ID" autofocus required />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mypassword" class="form-label text-success">Password</label>
                    <div class="input-with-icon">
                        <i class="input-icon fas fa-lock"></i>
                        <input type="password" class="form-control" id="mypassword" name="mypassword" placeholder="Enter your password" required />
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt" style="color:#b00 !important;"></i> Login
                </button>
            </form>
            
            <div class="login-links">
                <a href="#"><i class="fas fa-key" style="color:#b00 !important;"></i> Forgot Password?</a>
                <a href="#"><i class="fas fa-question-circle" style="color:#b00 !important;"></i> Need Help?</a>
            </div>
            
            <div class="footer text-center">
                <p>&copy; 2025 MIU Science Faculty Portal. All rights reserved.</p>
            </div>
        </div>
    </div>

        <script>
        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('mypassword');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        // Enhanced form validation
        function loginValidate() {
            const myid = document.getElementById('myid').value.trim();
            const mypassword = document.getElementById('mypassword').value;
            
            if (!myid) {
                showError('Login ID is required');
                return false;
            }
            
            if (!mypassword) {
                showError('Password is required');
                return false;
            }
            
            return true;
        }
        
        function showError(message) {
            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className = 'login-message';
            errorDiv.style.backgroundColor = '#e53e3e20';
            errorDiv.style.color = '#e53e3e';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> <span>${message}</span>`;
            
            // Insert after the form
            document.querySelector('.login-form').parentNode.insertBefore(errorDiv, document.querySelector('.login-form'));
            
            // Remove after 5 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>