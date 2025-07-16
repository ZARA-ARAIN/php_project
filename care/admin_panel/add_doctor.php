<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate form data
    $errors = [];

    if (empty($name)) $errors[] = "Name is required";
    if (empty($specialization)) $errors[] = "Specialization is required";
    if (empty($city)) $errors[] = "City is required";

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($contact)) {
        $errors[] = "Contact number is required";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {
        $errors[] = "Invalid contact number (10-15 digits required)";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email exists only if no errors so far
    if (empty($errors)) {
        $emailCheckQuery = "SELECT * FROM doctors WHERE email = ?";
        $emailCheckStmt = $connection->prepare($emailCheckQuery);
        $emailCheckStmt->bind_param("s", $email);
        $emailCheckStmt->execute();
        $emailCheckResult = $emailCheckStmt->get_result();

        if ($emailCheckResult->num_rows > 0) {
            $errors[] = "The email is already registered with another doctor";
        }
    }

    // Handle image upload if no errors
    $imageNewName = 'default_doctor.png'; // Default image
    if (empty($errors) && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExt, $allowedExts)) {
            $errors[] = "Invalid image type. Only JPG, JPEG, and PNG are allowed";
        } elseif ($imageSize > 2097152) { // 2MB limit
            $errors[] = "Image size must be less than 2MB";
        } else {
            $imageNewName = uniqid('doctor_', true) . '.' . $imageExt;
            $imageUploadPath = '../images/doctor/' . $imageNewName;

            if (!move_uploaded_file($imageTmpName, $imageUploadPath)) {
                $errors[] = "Error uploading image";
                $imageNewName = 'default_doctor.png';
            }
        }
    } elseif (empty($errors) && (!isset($_FILES['image']) || $_FILES['image']['error'] != 0)) {
        $errors[] = "Doctor image is required";
    }

    // Insert data if no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO doctors (name, specialization, city, email, contact, image, password) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sssssss", $name, $specialization, $city, $email, $contact, $imageNewName, $hashedPassword);

        if ($stmt->execute()) {
            $success_message = "Doctor added successfully!";
            // Clear form fields
            $name = $specialization = $city = $email = $contact = '';
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Doctor</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #e6e9ff;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --error-color: #f72585;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --white: #ffffff;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px 15px;
            position: fixed;
            overflow-y: auto;
            z-index: 1000;
            top: 0;
            left: 0;
            transition: var(--transition);
        }

        .sidebar h2 {
            margin-bottom: 25px;
            font-size: 24px;
            color: #2c3e50;
            padding-top: 10px;
            display: flex;
            align-items: center;
        }

        .sidebar h2 i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .sidebar .section-title {
            font-size: 12px;
            color: #888;
            margin: 20px 0 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #2c3e50;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 6px;
            transition: var(--transition);
            font-size: 14px;
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        /* Header */
        .header {
            position: fixed;
            left: 250px;
            top: 0;
            right: 0;
            height: 60px;
            background: #2c3e50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 999;
        }

        .header h3 {
            font-size: 20px;
            margin: 0;
            font-weight: 600;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icons i {
            font-size: 18px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--dark-gray);
        }

        .header-icons i:hover {
            color: var(--primary-color);
        }

        /* Alert Messages Container */
        .alert-container {
            position: fixed;
            top: 80px;
            right: 30px;
            width: 350px;
            z-index: 1100;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
            transition: var(--transition);
            min-height: calc(100vh - 70px);
        }

        .main-content {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .form-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }

        .form-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-gray);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            font-size: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: var(--transition);
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background-color: white;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
            padding-right: 40px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background-color: #f8fafc;
            border: 2px dashed #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            flex-direction: column;
        }

        .file-input-label:hover {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }

        .file-input-icon {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .file-input-text {
            font-size: 14px;
            color: var(--dark-gray);
        }

        .file-input-text span {
            color: var(--primary-color);
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 14px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            width: 100%;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }

        .btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(67, 97, 238, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .password-strength {
            margin-top: 8px;
            height: 6px;
            background: var(--medium-gray);
            border-radius: 3px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: var(--transition);
        }

        .weak {
            background-color: #ff6b6b;
            width: 33%;
        }

        .medium {
            background-color: #ffd166;
            width: 66%;
        }

        .strong {
            background-color: #06d6a0;
            width: 100%;
        }

        .password-match {
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .password-match.valid {
            color: #06d6a0;
        }

        .password-match.invalid {
            color: var(--error-color);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-gray);
            font-size: 14px;
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .preview-image {
            max-width: 150px;
            max-height: 150px;
            margin-top: 15px;
            border-radius: 8px;
            display: none;
            border: 2px solid #eee;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }

            .header,
            .main {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                transform: translateX(-100%);
                z-index: 1001;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .header,
            .main {
                margin-left: 0;
                width: 100%;
            }

            .header {
                position: relative;
            }

            .main {
                margin-top: 20px;
                padding: 20px;
            }

            .alert-container {
                width: calc(100% - 40px);
                left: 20px;
                right: 20px;
                top: 90px;
            }

            .menu-toggle {
                display: block !important;
            }
        }

        .menu-toggle {
            display: none;
            font-size: 20px;
            cursor: pointer;
            margin-right: 15px;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }

        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }
    </style>
</head>

<body>
    <!-- Overlay for mobile menu -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fas fa-stethoscope"></i> Care</h2>
        <a href="index.php" class="active"><i class="fa fa-dashboard"></i>DASHBOARD</a>

        <div class="section-title">Management</div>
        <a href="managedoc.php"><i class="fas fa-user-md"></i> Manage Doctors</a>
        <a href="manage_patients.php"><i class="fas fa-user-injured"></i> Manage Patients</a>
        <a href="manage_cities.php"><i class="fas fa-city"></i> Manage Cities</a>

        <div class="section-title">Orders</div>
        <a href="admin_orders.php"><i class="fa fa-edit"></i><span>Orders Details</span></a>
        <div class="section-title">Support</div>
        <a href="contact.php"><i class="fa-regular fa-message"></i><span>Contacts</span></a>
        <a href="#"><i class="fas fa-question-circle"></i> Need Help?</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="d-flex align-items-center">
            <div class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </div>
            <h3>Add New Doctor</h3>
        </div>
        <div class="header-icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-envelope"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Alert Messages Container -->
    <div class="alert-container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul class="mt-2 mb-0 ps-3">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong><?php echo htmlspecialchars($success_message); ?></strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <div class="main">
        <div class="main-content">
            <form method="POST" enctype="multipart/form-data" id="doctorForm" class="needs-validation" novalidate>
                <div class="form-title">
                    <i class="fas fa-user-plus"></i> Doctor Registration
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            <div class="invalid-feedback">Please enter the doctor's name</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="specialization">Specialization</label>
                            <input type="text" class="form-control" id="specialization" name="specialization"
                                value="<?php echo htmlspecialchars($specialization ?? ''); ?>" required>
                            <div class="invalid-feedback">Please enter the specialization</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city">City</label>
                            <select class="form-control" id="city" name="city" required>
                                <option value="">Select City</option>
                                <?php
                                $cityQuery = $connection->query("SELECT * FROM cities ORDER BY city_name");
                                while ($row = $cityQuery->fetch_assoc()) {
                                    $selected = (isset($city) && $city == $row['city_name']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($row['city_name']) . "' $selected>" .
                                        htmlspecialchars($row['city_name']) . "</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Please select a city</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            <div class="invalid-feedback">Please enter a valid email address</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact">Contact Number</label>
                            <input type="tel" class="form-control" id="contact" name="contact"
                                value="<?php echo htmlspecialchars($contact ?? ''); ?>" required>
                            <div class="invalid-feedback">Please enter a valid contact number (10-15 digits)</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="password-strength">
                                <div class="password-strength-bar" id="passwordStrength"></div>
                            </div>
                            <div class="invalid-feedback">Password must be at least 8 characters</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="password-match invalid" id="passwordMatch">Passwords do not match</div>
                            <div class="invalid-feedback">Please confirm your password</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Profile Image</label>
                            <div class="file-input-wrapper">
                                <label class="file-input-label" for="image">
                                    <div class="file-input-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                    <div class="file-input-text">
                                        <span>Click to upload</span> or drag and drop<br>
                                        JPG, PNG (max 2MB)
                                    </div>
                                </label>
                                <input type="file" id="image" name="image" accept="image/jpeg, image/png" required>
                                <img id="imagePreview" class="preview-image" src="#" alt="Preview">
                            </div>
                            <div class="invalid-feedback">Please upload a profile image</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn mt-3">
                    <i class="fas fa-user-plus me-2"></i> Register Doctor
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.style.display = 'none';
        });

        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // Custom password validation
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;

                    if (password.length > 0 && password.length < 8) {
                        document.getElementById('password').classList.add('is-invalid');
                        event.preventDefault();
                    }

                    if (confirmPassword.length > 0 && password !== confirmPassword) {
                        document.getElementById('confirm_password').classList.add('is-invalid');
                        event.preventDefault();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');

            // Reset
            strengthBar.style.width = '0';
            strengthBar.className = 'password-strength-bar';

            if (password.length === 0) return;

            // Calculate strength
            let strength = 0;
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

            // Update UI
            if (password.length < 8) {
                strengthBar.style.width = '33%';
                strengthBar.className = 'password-strength-bar weak';
            } else if (strength < 3) {
                strengthBar.style.width = '66%';
                strengthBar.className = 'password-strength-bar medium';
            } else {
                strengthBar.style.width = '100%';
                strengthBar.className = 'password-strength-bar strong';
            }
        });

        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchMessage = document.getElementById('passwordMatch');

            if (confirmPassword.length === 0) {
                matchMessage.style.display = 'none';
                return;
            }

            if (password === confirmPassword) {
                matchMessage.textContent = 'Passwords match!';
                matchMessage.classList.remove('invalid');
                matchMessage.classList.add('valid');
            } else {
                matchMessage.textContent = 'Passwords do not match';
                matchMessage.classList.remove('valid');
                matchMessage.classList.add('invalid');
            }
            matchMessage.style.display = 'block';
        });

        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const fileLabel = document.querySelector('.file-input-text span');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    fileLabel.textContent = file.name;
                }

                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                fileLabel.textContent = 'Click to upload';
            }
        });

        // Contact number validation
        document.getElementById('contact').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Drag and drop for image upload
        const fileInput = document.getElementById('image');
        const fileLabel = document.querySelector('.file-input-label');

        fileLabel.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileLabel.style.borderColor = 'var(--primary-color)';
            fileLabel.style.backgroundColor = 'rgba(67, 97, 238, 0.1)';
        });

        fileLabel.addEventListener('dragleave', () => {
            fileLabel.style.borderColor = '#ddd';
            fileLabel.style.backgroundColor = '#f8fafc';
        });

        fileLabel.addEventListener('drop', (e) => {
            e.preventDefault();
            fileLabel.style.borderColor = '#ddd';
            fileLabel.style.backgroundColor = '#f8fafc';

            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    </script>
      <script>
            // Force reload if user presses back after logout
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                    window.location.reload();
                }
            });
        </script>

</body>

</html>