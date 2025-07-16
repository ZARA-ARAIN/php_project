<?php
// sidebar.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection and functions
require_once('../connection.php');

/**
 * Fetches doctor data from database
 */
function getDoctorData($connection, $doctor_id) {
    $stmt = $connection->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function getAppointmentStats($connection, $doctor_name) {
    $today = date('Y-m-d');
    
    // Today's appointments
    $stmt = $connection->prepare("SELECT COUNT(*) FROM appointments WHERE Doctor = ? AND DATE(Date1) = ?");
    $stmt->bind_param("ss", $doctor_name, $today);
    $stmt->execute();
    $today_appointments = $stmt->get_result()->fetch_row()[0];

    // Total patients
    $stmt = $connection->prepare("SELECT COUNT(DISTINCT Name) FROM appointments WHERE Doctor = ?");
    $stmt->bind_param("s", $doctor_name);
    $stmt->execute();
    $total_patients = $stmt->get_result()->fetch_row()[0];

    return [
        'today' => $today_appointments,
        'total' => $total_patients
    ];
}

// Load doctor data if not in session
if (!isset($_SESSION['current_doctor']) && isset($_SESSION['user_id'])) {
    $_SESSION['current_doctor'] = getDoctorData($connection, $_SESSION['user_id']);
}

// Verify we have doctor data
if (!isset($_SESSION['current_doctor'])) {
    die("Doctor data not available");
}

$current_doctor = $_SESSION['current_doctor'];
$stats = getAppointmentStats($connection, $current_doctor['name']);
$today_appointments = $stats['today'];
$total_patients = $stats['total'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Dashboard</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Modern Medical Sidebar */
        .medical-sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 2px 0 20px rgba(0,0,0,0.08);
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 25px 0;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        /* Profile Card */
        .profile-card {
            padding: 0 25px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 20px;
        }

        .avatar-badge {
            position: relative;
            margin-right: 15px;
        }

        .doctor-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }

        .availability-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .availability-badge.available {
            background: #2ecc71;
        }

        .availability-badge.unavailable {
            background: #95a5a6;
        }

        .availability-badge i {
            font-size: 0.5rem;
            color: white;
        }

        .doctor-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .specialty-badge {
            background: #e3f2fd;
            color: #1976d2;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Stats Overview */
        .stats-overview {
            display: flex;
            justify-content: space-between;
            padding: 0 25px 20px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }

        .stat-item {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 10px;
            padding: 12px;
            width: calc(50% - 5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            background: #f1f8fe;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: #3498db;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-number {
            font-weight: 700;
            font-size: 1.2rem;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #7f8c8d;
            margin-top: 3px;
        }

        /* Navigation */
        .dashboard-nav {
            padding: 0 15px;
            margin-bottom: 20px;
        }

        .section-header {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            margin-bottom: 10px;
            color: #7f8c8d;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .section-header i {
            margin-right: 10px;
            font-size: 0.9rem;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-menu li {
            margin-bottom: 5px;
        }

        .nav-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #34495e;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-menu li a:hover {
            background: #f1f8fe;
            transform: translateX(3px);
        }

        .nav-menu li.active a {
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 500;
        }

        .menu-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: inherit;
        }

        .menu-text {
            flex: 1;
            font-size: 0.95rem;
        }

        .menu-arrow {
            color: #bdc3c7;
            font-size: 0.8rem;
        }

        .notification-bubble {
            background: #e74c3c;
            color: white;
            font-size: 0.7rem;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            font-weight: 600;
        }

        /* Quick Actions */
        .quick-actions {
            padding: 0 15px;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .action-btn {
            flex: 1;
            border: none;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .action-btn.new-appointment {
            background: #3498db;
            color: white;
        }

        .action-btn.new-appointment:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .action-btn.urgent-case {
            background: #f8f9fa;
            color: #e74c3c;
            border: 1px solid #eee;
        }

        .action-btn.urgent-case:hover {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        /* Contact Section */
        .contact-section {
            padding: 0 15px;
            margin-top: auto;
        }

        .contact-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 8px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .contact-icon {
            width: 24px;
            height: 24px;
            background: #f1f8fe;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: #3498db;
            font-size: 0.9rem;
        }

        .contact-text {
            font-size: 0.85rem;
            color: #34495e;
            flex: 1;
        }

        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .medical-sidebar {
                width: 70px;
                overflow: hidden;
                padding: 15px 0;
            }
            
            .profile-card, .stats-overview, .section-header span, 
            .menu-text, .contact-text, .action-btn span {
                display: none;
            }
            
            .section-header, .nav-menu li a {
                justify-content: center;
            }
            
            .menu-icon, .contact-icon {
                margin-right: 0;
                font-size: 1.1rem;
            }
            
            .doctor-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .nav-menu li a {
                padding: 12px 0;
                justify-content: center;
            }
            
            .action-btn {
                padding: 10px 5px;
                justify-content: center;
            }
            
            .action-btn i {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="medical-sidebar">
        <!-- Doctor Profile Card -->
        <div class="profile-card">
            <div class="avatar-badge">
                <div class="doctor-avatar">
                    <?php 
                    $initials = 'DR';
                    if (!empty($current_doctor['name'])) {
                        $names = explode(' ', trim($current_doctor['name']));
                        $initials = '';
                        foreach ($names as $name) {
                            if (!empty($name)) {
                                $initials .= strtoupper(substr($name, 0, 1));
                            }
                        }
                        $initials = substr($initials, 0, 3);
                    }
                    echo $initials;
                    ?>
                </div>
                <div class="availability-badge <?php echo (!empty($current_doctor['is_available'])) && $current_doctor['is_available'] ? 'available' : 'unavailable'; ?>">
                    <i class="fas fa-circle"></i>
                </div>
            </div>
            
            <div class="doctor-info">
                <h3> <?php echo !empty($_SESSION['doctor_data']['name']) ? htmlspecialchars($_SESSION['doctor_data']['name']) : 'Name Not Set'; ?></h3>
                <div class="specialty-badge"><?php echo !empty($_SESSION['doctor_data']['specialization']) ? htmlspecialchars($_SESSION['doctor_data']['specialization']) : 'General Practitioner'; ?></div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo !empty($today_appointments) ? htmlspecialchars($today_appointments) : '0'; ?></div>
                    <div class="stat-label">Today's Appts</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo !empty($total_patients) ? htmlspecialchars($total_patients) : '0'; ?></div>
                    <div class="stat-label">Total Patients</div>
                </div>
            </div>
        </div>

        <!-- Dashboard Navigation -->
        <div class="dashboard-nav">
            <div class="section-header">
                <i class="fas fa-clipboard-list"></i>
                <span>MEDICAL DASHBOARD</span>
            </div>
            
            <ul class="nav-menu">
                <li class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <a href="index.php">
                        <div class="menu-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <span class="menu-text">Dashboard</span>
                        <div class="menu-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </li>
                
                <li class="<?php echo $current_page === 'appointments.php' ? 'active' : ''; ?>">
                    <a href="appointments.php">
                        <div class="menu-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span class="menu-text">Appointments</span>
                        <?php if (!empty($today_appointments) && $today_appointments > 0): ?>
                            <span class="notification-bubble"><?php echo htmlspecialchars($today_appointments); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <li class="<?php echo $current_page === 'edit_profile.php' ? 'active' : ''; ?>">
                    <a href="edit_profile.php">
                        <div class="menu-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <span class="menu-text">Edit Profile</span>
                        <div class="menu-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="section-header">
                <i class="fas fa-bolt"></i>
                <span>QUICK ACTIONS</span>
            </div>
            <div class="action-buttons">
                <a href="new_appointment.php" class="action-btn new-appointment">
                    <i class="fas fa-plus"></i>
                    <span>New Appointment</span>
                </a>
                <a href="" class="action-btn urgent-case">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Urgent Case</span>
                </a>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="contact-section">
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <span class="contact-text"><?php echo !empty($current_doctor['email']) ? htmlspecialchars($current_doctor['email']) : 'email@example.com'; ?></span>
            </div>
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <span class="contact-text"><?php echo !empty($current_doctor['city']) ? htmlspecialchars($current_doctor['city']) : 'City Not Set'; ?></span>
            </div>
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="contact-text"><?php echo !empty($current_doctor['available_times']) ? htmlspecialchars($current_doctor['available_times']) : 'Mon-Fri 9am-5pm'; ?></span>
            </div>
        </div>
    </div>
</body>
</html>