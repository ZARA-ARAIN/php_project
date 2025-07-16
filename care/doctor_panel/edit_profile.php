<?php
session_start();
require_once('../connection.php');

// Stronger cache control
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

// Get current doctor data from session if available
$current_doctor = $_SESSION['doctor_data'] ?? [];
$doctor_id = $_SESSION['user_id'];

// If not in session, fetch from database
if (empty($current_doctor)) {
    $stmt = $connection->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_doctor = $result->fetch_assoc() ?: [];
    $_SESSION['doctor_data'] = $current_doctor;
}

// Form processing
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'specialization' => trim($_POST['specialization'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'available_times' => trim($_POST['available_times'] ?? ''),
        'is_available' => isset($_POST['is_available']) ? 1 : 0,
        'description' => trim($_POST['description'] ?? '')
    ];

    // Validation
    if (empty($inputs['name']) || empty($inputs['email']) || empty($inputs['specialization'])) {
        $error = "Name, email, and specialization are required.";
    } elseif (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Image upload handling
        $image_path = $current_doctor['image'] ?? '';
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                $target_dir = "../images/doctor/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);

                // Delete old image
                if (!empty($image_path) && file_exists("../" . $image_path)) {
                    unlink("../" . $image_path);
                }

                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'doctor_' . $doctor_id . '_' . time() . '.' . $file_ext;
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = 'images/doctor/' . $filename;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Only JPG, PNG, or GIF under 2MB allowed.";
            }
        }

        // Update database if no error
        if (empty($error)) {
            $stmt = $connection->prepare("UPDATE doctors SET 
                name=?, email=?, specialization=?, city=?, 
                available_times=?, is_available=?, image=?, description=? 
                WHERE id=?");

            $stmt->bind_param(
                "sssssissi",
                $inputs['name'],
                $inputs['email'],
                $inputs['specialization'],
                $inputs['city'],
                $inputs['available_times'],
                $inputs['is_available'],
                $image_path,
                $inputs['description'],
                $doctor_id
            );

            if ($stmt->execute()) {
                // Clear session doctor data to force refresh
                unset($_SESSION['doctor_data']);

                // Update common session values
                $_SESSION['FirstName'] = $inputs['name'];
                $_SESSION['user_name'] = $inputs['name'];
                $_SESSION['user_email'] = $inputs['email'];
                $_SESSION['specialization'] = $inputs['specialization'];
                // print_r($_SESSION['doctor_data']); exit();

                // Set success flag
                $_SESSION['profile_update_success'] = true;

                // Finish session and redirect
                session_write_close();
                header("Location: edit_profile.php");
                exit();
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Doctor Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --light: #f9fafb;
            --dark: #1f2937;
            --gray: #6b7280;
            --light-gray: #e5e7eb;
            --danger: #ef4444;
            --success: #10b981;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: var(--dark);
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
            transition: all 0.3s ease;
        }

        .profile-edit-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 2.5rem;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        .profile-edit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .profile-header h2 {
            color: var(--primary-dark);
            font-size: 1.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-header h2 i {
            font-size: 1.5rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius);
            background: #fef2f2;
            color: var(--danger);
            border-left: 4px solid var(--danger);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.3s ease;
        }

        .alert i {
            font-size: 1.25rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-image-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .profile-image-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin-bottom: 1.5rem;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .profile-image-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            box-shadow: var(--shadow);
        }

        .image-upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid white;
            box-shadow: var(--shadow-sm);
        }

        .image-upload-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .image-upload-btn i {
            font-size: 1.25rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-label.required::after {
            content: '*';
            color: var(--danger);
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background-color: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-label {
            font-weight: 500;
            color: var(--dark);
            cursor: pointer;
        }

        .helper-text {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray);
            line-height: 1.4;
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border: 2px dashed var(--light-gray);
            border-radius: var(--border-radius);
            background-color: var(--light);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            flex-direction: column;
            gap: 0.75rem;
        }

        .file-upload-label:hover {
            border-color: var(--primary-light);
            background-color: rgba(79, 70, 229, 0.05);
        }

        .file-upload-label i {
            font-size: 1.75rem;
            color: var(--primary);
        }

        .file-upload-text {
            font-size: 0.95rem;
            color: var(--dark);
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--light-gray);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 1.75rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 0.5rem;
            border: none;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background: rgba(79, 70, 229, 0.1);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn i {
            font-size: 1.1rem;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }

            .profile-edit-card {
                padding: 1.75rem;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        /* Animation for form elements */
        .form-group {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Delay animations for each form group */
        .form-group:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-group:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.4s;
        }

        .form-group:nth-child(5) {
            animation-delay: 0.5s;
        }

        .form-group:nth-child(6) {
            animation-delay: 0.6s;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="dashboard-container">
        <div class="main-content">
            <div class="profile-edit-card">
                <div class="profile-header">
                    <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="profile-image-section">
                        <div class="profile-image-container">
                            <?php if (!empty($current_doctor['image']) && file_exists("../" . $current_doctor['image'])): ?>
                                <img src="../<?php echo htmlspecialchars($current_doctor['image']); ?>" class="profile-image" id="profileImage">
                            <?php else: ?>
                                <div class="profile-image-placeholder">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            <?php endif; ?>
                            <label class="image-upload-btn">
                                <input type="file" name="image" id="imageUpload" accept="image/*" style="display:none;">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label required">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['doctor_data']['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['doctor_data']['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Specialization</label>
                            <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($_SESSION['doctor_data']['specialization']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($_SESSION['doctor_data']['city']); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Available Times</label>
                            <input type="text" name="available_times" class="form-control" value="<?php echo htmlspecialchars($_SESSION['doctor_data']['available_times']); ?>">
                            <span class="helper-text">Example: Monday to Friday, 9:00 AM - 5:00 PM</span>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_available" id="is_available" class="checkbox-input" <?php echo $_SESSION['doctor_data']['is_available'] ? 'checked' : ''; ?>>
                                <label for="is_available" class="checkbox-label">Currently available for appointments</label>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Profile Image</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span class="file-upload-text">Click to upload or drag and drop</span>
                                    <span class="helper-text">JPG, PNG or GIF (Max. 2MB)</span>
                                    <input type="file" name="image" class="file-upload-input" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Enhanced image preview functionality
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('profileImage');
                    if (img) {
                        img.src = e.target.result;
                    } else {
                        const placeholder = document.querySelector('.profile-image-placeholder');
                        placeholder.innerHTML = `<img src="${e.target.result}" class="profile-image" id="profileImage">`;
                        // Reattach event listener
                        document.getElementById('imageUpload').addEventListener('change', arguments.callee);
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality for file upload
        const fileUploadLabel = document.querySelector('.file-upload-label');
        const fileUploadInput = document.querySelector('.file-upload-input');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadLabel.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadLabel.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadLabel.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            fileUploadLabel.style.borderColor = 'var(--primary)';
            fileUploadLabel.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
        }

        function unhighlight() {
            fileUploadLabel.style.borderColor = 'var(--light-gray)';
            fileUploadLabel.style.backgroundColor = 'var(--light)';
        }

        fileUploadLabel.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileUploadInput.files = files;

            // Trigger change event
            const event = new Event('change');
            fileUploadInput.dispatchEvent(event);
        }

        // Animation on load
        document.addEventListener('DOMContentLoaded', () => {
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.animationDelay = `${0.1 + (index * 0.1)}s`;
            });
        });
    </script>
</body>

</html>