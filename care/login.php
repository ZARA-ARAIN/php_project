<?php
session_start();
include("connection.php");

if (isset($_REQUEST['logindata'])) {
    $email = trim($_REQUEST['email']);
    $password = trim($_REQUEST['password']);

    // Normalize email input
    $email = strtolower($email);

    // 1. First check doctors table
    $doctor_query = "SELECT * FROM doctors WHERE email = ?";
    $stmt = mysqli_prepare($connection, $doctor_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $doctor_result = mysqli_stmt_get_result($stmt);

    if ($doctor_result && mysqli_num_rows($doctor_result) > 0) {
        $doctor = mysqli_fetch_assoc($doctor_result);

        if (password_verify($password, $doctor['password'])) {
            // Set doctor session variables
            $_SESSION['user_id'] = $doctor['id'];
            $_SESSION['FirstName'] = $doctor['name'];
            $_SESSION['user_email'] = $doctor['email'];
            $_SESSION['user_role'] = 'doctor';
            $_SESSION['specialization'] = $doctor['specialization'];
            $_SESSION['doctor_data'] = $doctor;

            header("Location: doctor_panel/index.php");
            session_regenerate_id(true);

            exit();
        }
    }
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    // 2. If not a doctor, check regular users (optional)
    $user_query = "SELECT * FROM datamanage WHERE EmailAddress = ?";
    $stmt = mysqli_prepare($connection, $user_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);

        if (password_verify($password, $user['UserPassword'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['FirstName'] = $user['FirstName'];
            $_SESSION['user_email'] = $user['EmailAddress'];
            $_SESSION['user_role'] = $user['Roles'];

            // Add this line to store admin name specifically
            $_SESSION['admin_name'] = $user['FirstName'];
            if ($user['Roles'] === 'admin') {
                header("Location: admin_panel/index.php");
            } else {
                header("Location: home.php");
            }
            exit();
        }
    }

    // If authentication fails
    header("Location: login.php?error=1");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary: #f97316;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #94a3b8;
            --success: #10b981;
            --error: #ef4444;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            display: flex;
            min-height: 100vh;
        }

        .hero-section {
            width: 50%;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('https://i.pinimg.com/736x/8a/45/d1/8a45d16a8e8ff9c3d39b460d680f1cb9.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-container {
            width: 50%;
            background: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            color: var(--secondary);
        }

        .form-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--gray);
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: var(--transition);
            background-color: #f8fafc;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: white;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 2.25rem;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--dark);
        }

        .error-message {
            color: var(--error);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 0.5rem;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--gray);
            font-size: 0.75rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider::before {
            margin-right: 1rem;
        }

        .divider::after {
            margin-left: 1rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .social-btn {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            background: white;
        }

        .social-btn:hover {
            border-color: var(--gray);
        }

        .social-btn i {
            font-size: 1.25rem;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--gray);
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }

        .forgot-password a {
            color: var(--primary);
            font-size: 0.875rem;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .login-error {
            display: none;
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
        }

        @media (max-width: 1023px) {
            body {
                flex-direction: column;
            }

            .hero-section,
            .form-container {
                width: 100%;
                padding: 2rem;
            }

            .hero-section {
                min-height: 200px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-container>* {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .form-container>*:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-container>*:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-container>*:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-group {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.4s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.5s;
        }

        .btn {
            animation-delay: 0.6s !important;
        }

        .divider {
            animation-delay: 0.7s !important;
        }

        .social-login {
            animation-delay: 0.8s !important;
        }

        .form-footer {
            animation-delay: 0.9s !important;
        }

        .error-alert {
            position: fixed;
            top: 30px;
            left: 70%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 300px;
            z-index: 9999;
            text-align: center;
            padding: 12px;
            border-radius: 30px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <h1>Welcome Back <i class="fa-solid fa-handshake-simple"></i></h1>
        <p>"Book Appointments Seamlessly – Your Health, Our Priority"</p>
    </div>

    <div class="form-container">
        <div class="logo">
            <i class="fa-solid fa-stethoscope"></i>
            <span>CARE</span>
        </div>

        <h1 class="form-title">SIGN IN</h1>
        <p class="form-subtitle">Login to access your account</p>


        <form id="loginForm" action="login.php" method="POST" novalidate>
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required placeholder="your@email.com">
                <div class="error-message" id="emailError">Please enter your email address</div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required placeholder="Enter your password">
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                <div class="error-message" id="passwordError">Please enter your password</div>
            </div>

            <div class="forgot-password">
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn" name="logindata">Sign In</button>
        </form>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert animate__animated animate__fadeInDown error-alert" role="alert">
                <strong>⚠️ Error:</strong> Invalid email or password!
            </div>
        <?php } ?>

        <div class="divider">OR</div>

        <div class="social-login">
            <button type="button" class="social-btn">
                <i class="fab fa-google" style="color: #DB4437;"></i>
            </button>
            <button type="button" class="social-btn">
                <i class="fab fa-facebook-f" style="color: #4267B2;"></i>
            </button>
            <button type="button" class="social-btn">
                <i class="fab fa-apple"></i>
            </button>
        </div>

        <div class="form-footer">
            Don't have an account? <a href="registers.php">Sign up</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const loginError = document.getElementById('loginError');

            // Check for error parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                loginError.style.display = 'block';

                // Show and animate error alert
                const errorAlert = document.querySelector('.error-alert');
                if (errorAlert) {
                    errorAlert.style.top = '20px';

                    setTimeout(() => {
                        errorAlert.style.top = '-100px';
                    }, 4000);
                }
            }

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            // Form validation
            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validate email
                const email = document.getElementById('email').value.trim();
                if (email === '') {
                    document.getElementById('emailError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('emailError').style.display = 'none';
                }

                // Validate password
                const password = document.getElementById('password').value;
                if (password === '') {
                    document.getElementById('passwordError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('passwordError').style.display = 'none';
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });

            // Real-time validation
            form.addEventListener('input', function(event) {
                const target = event.target;

                if (target.id === 'email' && target.value.trim() !== '') {
                    document.getElementById('emailError').style.display = 'none';
                }

                if (target.id === 'password' && target.value !== '') {
                    document.getElementById('passwordError').style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>