<?php
session_start();
require_once('../connection.php');

// Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

// Get current doctor data
$current_doctor = $_SESSION['doctor_data'] ?? [];

// Verify doctor still exists in database
$stmt = $connection->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: ../login.php?error=doctor_not_found");
    exit();
}

// Refresh doctor data from database
$current_doctor = $result->fetch_assoc();
$_SESSION['doctor_data'] = $current_doctor;

// Image path handling
$imagePath = '';
$fullImagePath = '';
$imageExists = false;
$displayImagePath = ''; // This will be the path used in HTML

if (!empty($current_doctor['image'])) {
    // Normalize the stored path
    $imagePath = ltrim($current_doctor['image'], '/.');

    // Remove duplicate 'images/doctor/' if it exists
    $imagePath = str_replace('images/doctor/', '', $imagePath);

    // Define possible base paths
    $possiblePaths = [
        '../images/doctor/' . $imagePath,
        '../images/doctor/images/doctor/' . $imagePath
    ];

    // Check which path exists
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullImagePath = $path;
            $imageExists = true;
            $displayImagePath = str_replace('../', '', $path); // For use in HTML src attribute
            break;
        }
    }

    // If no image found, use default
    if (!$imageExists) {
        $displayImagePath = 'images/doctor/default.jpg';
    }
} else {
    // No image in database, use default
    $displayImagePath = 'images/doctor/default.jpg';
}
$_SESSION['doctor_data']['name']

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Panel - <?php echo htmlspecialchars($current_doctor['name']); ?></title>
    <!-- Consolidated CSS -->
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/theme1.css">
    <link rel="stylesheet" href="assets/plugins/charts-c3/c3.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2ecc71;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --gray: #95a5a6;
        }

        .doctor-profile-img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .doctor-profile-img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .profile-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .specialization-badge {
            font-size: 1rem;
            padding: 8px 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            letter-spacing: 1px;
            font-weight: 500;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .dashboard-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
        }

        .feature-list li {
            padding: 10px 0;
            display: flex;
            align-items: center;
        }

        .feature-list li i {
            margin-right: 10px;
            color: var(--primary);
        }

        .welcome-header {
            position: relative;
            padding: 20px;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(41, 128, 185, 0.1) 100%);
            border-left: 5px solid var(--primary);
        }

        .default-profile-icon {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 4rem;
            margin: 0 auto 15px auto;
        }

        @media (max-width: 768px) {
            .doctor-profile-img {
                width: 120px;
                height: 120px;
            }

            .default-profile-icon {
                width: 120px;
                height: 120px;
                font-size: 3rem;
            }
        }
    </style>
</head>

<body class="font-montserrat">
    <?php include('header.php'); ?>
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader"></div>
    </div>

    <div id="main_content">
        <!-- Header -->
        <div id="header_top" class="header_top bg-white">
            <div class="container">
                <div class="hleft">
                    <a class="header-brand text-dark"><i class="fa-solid fa-stethoscope text-primary"></i> <strong>Doctor</strong>Panel</a>
                </div>
                <div class="hright">
                    <div class="dropdown">
                        <?php include('sidebar.php'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="page">
            <div class="section-body mt-3">
                <div class="container-fluid">
                    <div class="row clearfix">
                        <div class="col-lg-12">
                            <div class="welcome-header">
                                <h4 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['doctor_data']['name']); ?>!</h4>
                                <p class="mb-0 text-muted">
                                    <i class="far fa-clock mr-1"></i> Last login: <?php echo date('F j, Y, g:i a'); ?>
                                    <?php if ($_SESSION['doctor_data']['is_available']): ?>
                                        <span class="badge badge-success ml-2"><i class="fas fa-circle mr-1"></i> Currently Available</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary ml-2"><i class="fas fa-circle mr-1"></i> Not Available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Doctor Profile Card -->
                        <div class="col-lg-4 col-md-12">
                            <div class="card profile-card">
                                <div class="card-body text-center pt-4">
                                    <div class="mb-4 position-relative">
                                        <?php if ($imageExists): ?>
                                            <img src="<?php echo htmlspecialchars($fullImagePath); ?>" class="doctor-profile-img mb-3" alt="Doctor Profile Image">
                                        <?php else: ?>
                                            <div class="default-profile-icon">
                                                <i class="fas fa-user-md"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="badge specialization-badge text-white px-3 py-2">
                                            <?php echo htmlspecialchars($_SESSION['doctor_data']['specialization']); ?>
                                        </span>
                                    </div>

                                    <h4 class="mb-4"><?php echo htmlspecialchars($_SESSION['doctor_data']['name']); ?></h4>

                                    <div class="text-left px-3">
                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Email</small>
                                                <?php echo htmlspecialchars($_SESSION['doctor_data']['email']); ?>
                                            </div>
                                        </div>

                                        <?php if (!empty($_SESSION['doctor_data']['city'])): ?>
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Location</small>
                                                    <?php echo htmlspecialchars($current_doctor['city']); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($_SESSION['doctor_data']['available_times'])): ?>
                                            <div class="info-item">
                                                <div class="info-icon">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Availability</small>
                                                    <?php echo htmlspecialchars($_SESSION['doctor_data']['available_times']); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="info-item">
                                            <div class="info-icon">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Status</small>
                                                <?php echo ($_SESSION['doctor_data']['is_available'] ? 'Available for appointments' : 'Not currently available'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <a href="edit_profile.php" class="btn btn-primary px-4 py-2">
                                            <i class="fas fa-edit mr-2"></i> Edit Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Content Area -->
                        <div class="col-lg-8 col-md-12">
                            <div class="card dashboard-card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-4 dashboard-card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-circle bg-primary text-white mr-3">
                                                            <i class="fas fa-calendar-check fa-2x"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Appointments</h6>
                                                            <p class="text-muted mb-0">Manage your schedule</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-4 dashboard-card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-circle bg-success text-white mr-3">
                                                            <i class="fas fa-users fa-2x"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Patients</h6>
                                                            <p class="text-muted mb-0">View patient records</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3"><i class="fas fa-bolt mr-2 text-warning"></i> Quick Actions</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <a href="appointments.php" class="btn btn-outline-primary btn-block py-3">
                                                <i class="fas fa-calendar-plus mr-2"></i> New Appointment
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="schedule.php" class="btn btn-outline-success btn-block py-3">
                                                <i class="fas fa-clock mr-2"></i> Set Availability
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="edit_profile.php" class="btn btn-outline-info btn-block py-3">
                                                <i class="fas fa-user-edit mr-2"></i> Update Profile
                                            </a>
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3"><i class="fas fa-star mr-2 text-warning"></i> Recent Activity</h5>
                                    <div class="list-group">
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-calendar-check text-primary mr-2"></i> New appointment with Asif</span>
                                                <small class="text-muted">2 hours ago</small>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    <i class="fas fa-file-medical text-success mr-2"></i>
                                                    Medical record updated for <?php echo htmlspecialchars($_SESSION['doctor_data']['name']); ?>
                                                </span>
                                                <small class="text-muted">Yesterday</small>
                                            </div>
                                        </div>

                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-comment-medical text-info mr-2"></i> New patient message received</span>
                                                <small class="text-muted">2 days ago</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript includes -->
    <script>
        // Force reload if user presses back after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/bundles/lib.vendor.bundle.js"></script>
    <script src="../assets/js/core.js"></script>
    <script>
        // Page loader
        window.addEventListener('load', function() {
            document.querySelector('.page-loader-wrapper').style.display = 'none';
        });

        // Initialize tooltips
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>

</html>