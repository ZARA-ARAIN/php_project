<?php
session_start();

include("connection.php");

if (isset($_REQUEST['registerdata'])) {
    $Name = $_REQUEST['name'];
    $Email = $_REQUEST['email'];
    $Password = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);
    $confirmPassword = password_hash($_REQUEST['confirmPassword'], PASSWORD_DEFAULT);
    $Role = 'User';


    if ($_REQUEST['password'] !== $_REQUEST['confirmPassword']) {
        header("Location: registers.php?error=2");
        exit();
    }

    // Check if email already exists
    $checkemailquery = "SELECT * FROM datamanage WHERE EmailAddress = '$Email'";
    $emailresult = mysqli_query($connection, $checkemailquery);

    if (!$emailresult) {
        die("Email Check Query Failed: " . mysqli_error($connection));
    }

    if (mysqli_num_rows($emailresult) > 0) {
        // Email already exists
        header("Location: registers.php?error=1");
        exit();
    } else {
        // Insert new user
        $Saveregisterquery = "INSERT INTO `datamanage`(`FirstName`, `EmailAddress`, `UserPassword`, `ConfirmPassword`, `Roles`) 
        VALUES ('$Name', '$Email', '$Password', '$confirmPassword', '$Role')";

        $result = mysqli_query($connection, $Saveregisterquery);

        if ($result) {
            header("Location: registers.php?success=1");
            exit();
        } else {
            die("Registration Failed: " . mysqli_error($connection));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Care</title>
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

        .benefits {
            margin: 1.5rem 0;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: var(--dark);
        }

        .benefit-item i {
            color: var(--success);
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

        .form-group:nth-child(3) {
            animation-delay: 0.6s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.7s;
        }

        .benefits {
            animation-delay: 0.8s !important;
        }

        .btn {
            animation-delay: 0.9s !important;
        }

        .divider {
            animation-delay: 1s !important;
        }

        .social-login {
            animation-delay: 1.1s !important;
        }

        .form-footer {
            animation-delay: 1.2s !important;
        }

        .success-alert {
            position: relative;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
            width: 100%;
            text-align: center;
            z-index: 999;
        }

        .error-alert {
            position: relative;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 5px;
            margin-top: -50px;
            width: 100%;
            text-align: center;
            
        }

        .success-alert,
        .error-alert {
            position: fixed;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 300px;
            z-index: 9999;
            text-align: center;
            padding: 12px;
            border-radius: 30px;
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <h1>Welcome to Care <i class="fa-solid fa-house-chimney-medical"></i></h1>
        <p>"Book Appointments Seamlessly – Your Health, Our Priority"</p>
    </div>

    <div class="form-container">
        <div class="logo">
            <i class="fa-solid fa-stethoscope"></i>
            <span>CARE</span>
        </div>

        <h1 class="form-title">Create your account</h1>
        <p class="form-subtitle">Book a Visit in Just a Few Clicks!</p>

        <form id="registerForm" action="registers.php" method="POST" novalidate>
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-input" required placeholder="John Doe">
                <div class="error-message" id="nameError">Please enter your full name</div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required placeholder="your@email.com">
                <div class="error-message" id="emailError">Please enter a valid email address</div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required minlength="8" placeholder="At least 8 characters">
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                <div class="error-message" id="passwordError">Password must be at least 8 characters long</div>
            </div>

            <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" required placeholder="Confirm your password">
                <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
            </div>

            <div class="benefits">
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Fast checkout and order tracking</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Exclusive members-only deals</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Personalized recommendations</span>
                </div>
            </div>

            <button type="submit" class="btn" name="registerdata">Create Account</button>


            <?php if (isset($_GET['success'])) { ?>
                <div class="alert animate__animated animate__fadeInDown success-alert" role="alert">
                    <strong>🎉 Registration Successful!</strong>
                </div>
            <?php } ?>

            <?php if (isset($_GET['error'])) { ?>
                <div class="alert animate__animated animate__fadeInDown error-alert" role="alert">
                    <strong>⚠️ Error:</strong> Email already exists!
                </div>
            <?php } ?>







            <div class="divider">OR</div>

            <div class="social-login">
                <button type="button" class="social-btn">
                   <a href="https://myaccount.google.com/"> <i class="fab fa-google" style="color: #DB4437;"></i></a>
                </button>
                <button type="button" class="social-btn">
                  <a href="https://www.facebook.com/"><i class="fab fa-facebook-f" style="color: #4267B2;"></i></a>  
                </button>
                <button type="button" class="social-btn">
                   <a href="https://support.apple.com/en-us/111001?device-type=iphone"> <i class="fab fa-apple"></i></a>
                </button>
            </div>

            <div class="form-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            // Form validation
            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validate name
                const name = document.getElementById('name').value.trim();
                if (name === '') {
                    document.getElementById('nameError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('nameError').style.display = 'none';
                }

                // Validate email
                const email = document.getElementById('email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById('emailError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('emailError').style.display = 'none';
                }

                // Validate password
                const password = document.getElementById('password').value;
                if (password.length < 8) {
                    document.getElementById('passwordError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('passwordError').style.display = 'none';
                }

                // Validate confirm password
                const confirmPassword = document.getElementById('confirmPassword').value;
                if (password !== confirmPassword) {
                    document.getElementById('confirmPasswordError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('confirmPasswordError').style.display = 'none';
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });

            setTimeout(() => {
                const alerts = document.querySelectorAll('.success-alert, .error-alert');
                alerts.forEach(alert => alert.style.top = '20px');

                setTimeout(() => {
                    alerts.forEach(alert => alert.style.top = '-100px');
                }, 4000);
            }, 500);


            // Real-time validation
            form.addEventListener('input', function(event) {
                const target = event.target;

                if (target.id === 'name') {
                    if (target.value.trim() !== '') {
                        document.getElementById('nameError').style.display = 'none';
                    }
                }

                if (target.id === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailRegex.test(target.value.trim())) {
                        document.getElementById('emailError').style.display = 'none';
                    }
                }

                if (target.id === 'password') {
                    if (target.value.length >= 8) {
                        document.getElementById('passwordError').style.display = 'none';

                        // Also check confirm password if it has value
                        const confirmPassword = document.getElementById('confirmPassword').value;
                        if (confirmPassword !== '' && confirmPassword !== target.value) {
                            document.getElementById('confirmPasswordError').style.display = 'block';
                        } else {
                            document.getElementById('confirmPasswordError').style.display = 'none';
                        }
                    }
                }

                if (target.id === 'confirmPassword') {
                    const password = document.getElementById('password').value;
                    if (target.value === password) {
                        document.getElementById('confirmPasswordError').style.display = 'none';
                    }
                }
            });
        });
    </script>
</body>

</html>