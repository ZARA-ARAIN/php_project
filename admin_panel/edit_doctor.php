<?php
include '../connection.php';

// Initialize variables
$error = '';
$success = false;
$current_doctor = [];

if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];
    
    // Fetch doctor details for the given ID
    $query = "SELECT * FROM doctors WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_doctor = $result->fetch_assoc();

    if (!$current_doctor) {
        $error = "Doctor not found.";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate input data
        $name = trim($_POST['name']);
        $specialization = trim($_POST['specialization']);
        $city = trim($_POST['city']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $available_times = trim($_POST['available_times'] ?? '');
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        // Handle image upload
        $image = $current_doctor['image']; // Default to current image
        
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
            // Validate uploaded file
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (in_array($_FILES['image']['type'], $allowed_types) && 
                $_FILES['image']['size'] <= $max_size) {
                
                $imageExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageNewName = uniqid('', true) . '.' . $imageExt;
                $imageUploadPath = '../images/doctor/' . $imageNewName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $imageUploadPath)) {
                    // Delete old image if it exists
                    if (!empty($current_doctor['image']) && 
                        file_exists('../images/doctor/' . $current_doctor['image'])) {
                        unlink('../images/doctor/' . $current_doctor['image']);
                    }
                    $image = $imageNewName;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image file. Only JPG, PNG, or GIF up to 2MB are allowed.";
            }
        } elseif ($_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $error = "Image upload error: " . $_FILES['image']['error'];
        }

        // Handle password update
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $password = $current_doctor['password']; // Keep old password if not updated
        }

        // Update doctor data in the database
        if (empty($error)) {
            $updateQuery = "UPDATE doctors SET 
                            name = ?, 
                            specialization = ?, 
                            city = ?, 
                            email = ?, 
                            contact = ?, 
                            password = ?, 
                            image = ?, 
                            available_times = ?, 
                            is_available = ? 
                            WHERE id = ?";
            
            $stmt = $connection->prepare($updateQuery);
            $stmt->bind_param("ssssssssii", 
                $name, 
                $specialization, 
                $city, 
                $email, 
                $contact, 
                $password, 
                $image, 
                $available_times, 
                $is_available, 
                $doctor_id
            );

            if ($stmt->execute()) {
                $success = true;
                // Refresh doctor data after update
                $query = "SELECT * FROM doctors WHERE id = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("i", $doctor_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $current_doctor = $result->fetch_assoc();
            } else {
                $error = "Error updating record: " . $stmt->error;
            }
        }
    }
} else {
    $error = "No doctor ID provided.";
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
            --warning: #f59e0b;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
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
            margin-left: 30px;
            transition: var(--transition);
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
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-edit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
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
            color: var(--primary);
        }

        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.3s ease;
            border-left: 4px solid;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-left-color: var(--danger);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left-color: var(--success);
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
            width: 150px;
            height: 150px;
            margin-bottom: 1.5rem;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .profile-image:hover {
            transform: scale(1.05);
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
            font-size: 3.5rem;
            box-shadow: var(--shadow);
        }

        .image-upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: 3px solid white;
            box-shadow: var(--shadow-sm);
        }

        .image-upload-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .image-upload-btn i {
            font-size: 1.1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
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
            padding: 0.75rem 1.25rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius-sm);
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-label {
            font-weight: 500;
            color: var(--dark);
            cursor: pointer;
            font-size: 0.95rem;
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
            padding: 1.5rem;
            border: 2px dashed var(--light-gray);
            border-radius: var(--border-radius-sm);
            background-color: white;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            flex-direction: column;
            gap: 0.75rem;
        }

        .file-upload-label:hover {
            border-color: var(--primary-light);
            background-color: rgba(79, 70, 229, 0.05);
        }

        .file-upload-label i {
            font-size: 1.5rem;
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
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
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
            font-size: 1rem;
        }

        /* Floating labels for better UX */
        .floating-label-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label {
            position: absolute;
            top: 0.75rem;
            left: 1.25rem;
            color: var(--gray);
            font-size: 0.95rem;
            pointer-events: none;
            transition: var(--transition);
            background: white;
            padding: 0 0.25rem;
            transform-origin: left center;
        }

        .floating-input:focus + .floating-label,
        .floating-input:not(:placeholder-shown) + .floating-label {
            transform: translateY(-1.25rem) scale(0.85);
            color: var(--primary);
        }

        /* Success animation */
        @keyframes successPulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .success-pulse {
            animation: successPulse 1.5s infinite;
        }

        /* Responsive adjustments */
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
          /* Sidebar - Fixed Top Gap */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px 15px;
            position: fixed;
            overflow-y: auto;
            z-index: 1000;
            top: 0;
            /* Added to fix top gap */
            left: 0;
        }

        .sidebar h2 {
            margin-bottom: 25px;
            font-size: 24px;
            color: #2c3e50;
            padding-top: 10px;
            /* Added padding */
        }

        .sidebar .section-title {
            font-size: 12px;
            color: #888;
            margin: 20px 0 10px;
            text-transform: uppercase;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #2c3e50;
            text-decoration: none;
            padding: 10px 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 16px;
        }

        .sidebar a:hover {
            background: #ecf0f1;
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
            font-size: 18px;
            margin: 0;
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            margin-top: 80px;
            /* Increased from 60px */
            padding: 25px;
            width: calc(100% - 250px);
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
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }

        /* Toggle switch for availability */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: var(--transition);
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: var(--transition);
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--success);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            margin-left: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        /* Card hover effect */
        .profile-edit-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body>
   
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
        <h3>Manage Doctors</h3>
        <div>
            <i class="fas fa-bell"></i>
            <i class="fas fa-envelope" style="margin: 0 15px;"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>
    <div class="main">
        <div class="dashboard-container">
            <div class="main-content">
                <div class="profile-edit-card">
                    <div class="profile-header">
                        <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Profile updated successfully!
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="profile-image-section">
                            <div class="profile-image-container">
                                <?php if (!empty($current_doctor['image']) && file_exists("../images/doctor/" . $current_doctor['image'])): ?>
                                    <img src="../images/doctor/<?php echo htmlspecialchars($current_doctor['image']); ?>" class="profile-image" id="profileImage">
                                <?php else: ?>
                                    <div class="profile-image-placeholder">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group floating-label-group">
                                <input type="text" name="name" class="form-control floating-input" 
                                       value="<?php echo htmlspecialchars($current_doctor['name'] ?? ''); ?>" 
                                       placeholder=" " required>
                                <label class="floating-label required">Full Name</label>
                            </div>

                            <div class="form-group floating-label-group">
                                <input type="email" name="email" class="form-control floating-input" 
                                       value="<?php echo htmlspecialchars($current_doctor['email'] ?? ''); ?>" 
                                       placeholder=" " required>
                                <label class="floating-label required">Email</label>
                            </div>

                            <div class="form-group floating-label-group">
                                <input type="text" name="specialization" class="form-control floating-input" 
                                       value="<?php echo htmlspecialchars($current_doctor['specialization'] ?? ''); ?>" 
                                       placeholder=" " required>
                                <label class="floating-label required">Specialization</label>
                            </div>

                            <div class="form-group floating-label-group">
                                <input type="text" name="city" class="form-control floating-input" 
                                       value="<?php echo htmlspecialchars($current_doctor['city'] ?? ''); ?>" 
                                       placeholder=" ">
                                <label class="floating-label">City</label>
                            </div>

                            <div class="form-group floating-label-group">
                                <input type="text" name="contact" class="form-control floating-input" 
                                       value="<?php echo htmlspecialchars($current_doctor['contact'] ?? ''); ?>" 
                                       placeholder=" ">
                                <label class="floating-label">Contact Number</label>
                            </div>

                            <div class="form-group floating-label-group">
                                <input type="password" name="password" class="form-control floating-input" 
                                       placeholder=" ">
                                <label class="floating-label">New Password</label>
                                <span class="helper-text">Leave blank to keep current password</span>
                            </div>

                            <div class="form-group floating-label-group">
                                <input type="text" name="available_times" class="form-control floating-input" 
                                       value="<?php echo htmlspecialchars($current_doctor['available_times'] ?? ''); ?>" 
                                       placeholder=" ">
                                <label class="floating-label">Available Times</label>
                                <span class="helper-text">Example: Monday to Friday, 9:00 AM - 5:00 PM</span>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_available" class="checkbox-input" 
                                               <?php echo ($current_doctor['is_available'] ?? 0) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span class="toggle-label">Currently available for appointments</span>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">Profile Image</label>
                                <div class="file-upload-wrapper">
                                    <label class="file-upload-label" id="fileUploadLabel">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span class="file-upload-text">Click to upload or drag and drop</span>
                                        <span class="helper-text">JPG, PNG or GIF (Max. 2MB)</span>
                                        <input type="file" name="image" class="file-upload-input" id="fileUploadInput" accept="image/*">
                                    </label>
                                </div>
                                <div id="fileNameDisplay" style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--primary); display: none;"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary <?php echo $success ? 'success-pulse' : ''; ?>">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
 <script>
        // Enhanced image preview functionality
        const imageUpload = document.getElementById('imageUpload');
        const fileUploadInput = document.getElementById('fileUploadInput');
        const fileUploadLabel = document.getElementById('fileUploadLabel');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        function handleImagePreview(e) {
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
                        // Reattach event listener to the new image upload button
                        document.getElementById('imageUpload').addEventListener('change', handleImagePreview);
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        imageUpload.addEventListener('change', handleImagePreview);
        fileUploadInput.addEventListener('change', function(e) {
            handleImagePreview(e);
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = `Selected file: ${e.target.files[0].name}`;
                fileNameDisplay.style.display = 'block';
            }
        });

        // Drag and drop functionality for file upload
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
            fileUploadLabel.style.backgroundColor = 'white';
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
            
            // Remove success pulse after 5 seconds
            setTimeout(() => {
                const successBtn = document.querySelector('.btn-primary.success-pulse');
                if (successBtn) {
                    successBtn.classList.remove('success-pulse');
                }
            }, 5000);
        });

        // Floating label functionality
        document.querySelectorAll('.floating-input').forEach(input => {
            // Initialize labels based on existing values
            if (input.value) {
                input.nextElementSibling.classList.add('floating-label-active');
            }
            
            input.addEventListener('focus', () => {
                input.nextElementSibling.classList.add('floating-label-active');
            });
            
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.nextElementSibling.classList.remove('floating-label-active');
                }
            });
        });
    </script>
</body>
</html>