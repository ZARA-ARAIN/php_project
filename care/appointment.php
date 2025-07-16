<?php
// appointment.php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Cancel appointment
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];

    // Validate appointment ID
    if (!is_numeric($appointment_id)) {
        $_SESSION['error_message'] = "Invalid appointment ID";
        header("Location: appointment.php");
        exit;
    }

    $delete_stmt = $connection->prepare("DELETE FROM appointments WHERE id = ? AND Email = ?");
    $delete_stmt->bind_param("is", $appointment_id, $_SESSION['user_email']);

    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = "Appointment cancelled successfully!";
    } else {
        $_SESSION['error_message'] = "Error cancelling appointment. Please try again.";
    }
    header("Location: appointment.php");
    exit;
}

$email = $_SESSION['user_email'];
$user_name = $_SESSION['Name'] ?? explode('@', $email)[0];

// Get appointments with doctor specialization
$stmt = $connection->prepare("
    SELECT a.*, d.specialization 
    FROM appointments a
    LEFT JOIN doctors d ON a.Doctor = d.name 
    WHERE a.Email = ? 
    ORDER BY COALESCE(a.Date1, a.created_at), a.created_at
");

if ($stmt === false) {
    die("Error preparing statement: " . $connection->error);
}

$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Error getting result set: " . $stmt->error);
}

// Group appointments by date
$appointments_by_date = [];
$today = date('Y-m-d');

while ($row = $result->fetch_assoc()) {
    // Determine the appointment date - use Date1 if valid, otherwise use created_at date
    $appointment_date = null;

    // First try Date1
    if (!empty($row['Date1']) && $row['Date1']) {
        try {
            $date_obj = new DateTime($row['Date1']);
            $appointment_date = $date_obj->format('Y-m-d');
        } catch (Exception $e) {
            error_log("Invalid Date format for appointment ID: " . $row['id']);
        }
    }

    // If Date1 is invalid or empty, use created_at date
    if (empty($appointment_date) && !empty($row['created_at'])) {
        try {
            $date_obj = new DateTime($row['created_at']);
            $appointment_date = $date_obj->format('Y-m-d');
        } catch (Exception $e) {
            error_log("Invalid created_at format for appointment ID: " . $row['id']);
        }
    }

    // If still no valid date, mark as unscheduled
    if (empty($appointment_date)) {
        $appointments_by_date['unscheduled'][] = $row;
        continue;
    }

    $appointments_by_date[$appointment_date][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments | MediCare</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Link to your external CSS file -->
    <style>
        :root {
            --primary-color: #6c5ce7;
            --primary-light: #a29bfe;
            --secondary-color: #5d4aec;
            --accent-color: #00cec9;
            --light-bg: #f9f9ff;
            --card-bg: #ffffff;
            /* Pure white for cards */
            --card-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            --glass-effect: rgba(255, 255, 255, 0.92);
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --past-color: rgba(255, 255, 255, 0.9);
            /* Changed to white with slight transparency */
            --today-color: rgba(255, 255, 255, 0.95);
            /* Brighter white for today */
            --future-color: rgba(255, 255, 255, 0.9);
            --border-color: rgba(0, 0, 0, 0.08);
            /* Subtle border */
        }

        .appointment-card {
            border: 1px solid var(--border-color) !important;
            /* Added border */
            background: var(--card-bg) !important;
            box-shadow: var(--card-shadow);
            position: relative;
            z-index: 1;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color) !important;
            top: 20px;
        }

        .appointment-card.past {
            background-color: var(--past-color) !important;
            opacity: 0.95;
            /* Increased opacity */
            border-left: 4px solid #b2bec3 !important;
            /* Grey border for past */
        }

        .appointment-card.today {
            background-color: var(--today-color) !important;
            border-left: 4px solid var(--accent-color) !important;
            box-shadow: 0 10px 30px -10px rgba(0, 206, 201, 0.2);
            /* Special shadow for today */
        }

        .appointment-card.future {
            background-color: var(--future-color) !important;
            border-left: 4px solid var(--primary-color) !important;
        }

        /* Enhanced card content contrast */
        .card-body {
            color: var(--text-dark);
        }

        /* Darker text for better readability */
        .card-title {
            color: var(--text-dark) !important;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-dark);
            background-image:
                radial-gradient(circle at 15% 50%, rgba(108, 92, 231, 0.03) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(0, 206, 201, 0.03) 0%, transparent 25%);
        }

        .navbar {
            backdrop-filter: blur(12px);
            background: var(--glass-effect) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
            font-size: 1.5rem;
        }

        .user-avatar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.25);
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .user-avatar:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(108, 92, 231, 0.35);
        }

        .date-section {
            position: relative;
            margin: 2rem 0;
            padding-left: 60px;
        }

        .date-circle {
            position: absolute;
            left: 0;
            top: 0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
            z-index: 2;
        }

        .date-day {
            font-size: 1.2rem;
            line-height: 1;
        }

        .date-month {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .date-title {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }

        .date-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 40px;
            height: 2px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }


        .appointment-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-5px);
        }

        .time-badge {
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            font-weight: 600;
            padding: 6px 12px;
            margin-top: 20px;
            margin-right: -10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(108, 92, 231, 0.1);
        }

        .card-icon {
            color: var(--primary-color);
            font-size: 1rem;
            width: 20px;
            text-align: center;
            opacity: 0.8;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 500;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 10px rgba(108, 92, 231, 0.25);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.35);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }

        .btn-outline-danger {
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 500;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            border-width: 1.5px;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.15);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .empty-state-icon {
            font-size: 4.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border-radius: 4px;
        }

        .floating-action-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 15px 35px rgba(108, 92, 231, 0.35);
            z-index: 100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .floating-action-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.4);
            color: white;
        }

        .appointment-note {
            background: rgba(248, 249, 250, 0.8);
            border-left: 3px solid var(--primary-color);
            padding: 12px;
            border-radius: 0 8px 8px 0;
            position: relative;
            margin-top: 15px;
        }

        .appointment-note::before {
            content: '';
            position: absolute;
            left: -3px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, var(--primary-color), var(--accent-color));
            border-radius: 3px 0 0 3px;
        }

        .doctor-specialty {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 5px;
            margin-bottom: 20px;
            padding-left: 40px;
            font-style: italic;
        }

        .card-footer {
            background: rgba(248, 249, 250, 0.7);
            backdrop-filter: blur(5px);
            border-top: 1px solid rgba(0, 0, 0, 0.03) !important;
        }

        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 3;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-upcoming {
            background: rgba(110, 231, 183, 0.2);
            color: #0e9f6e;
        }

        .status-completed {
            background: rgba(159, 122, 234, 0.2);
            color: #7e3af2;
        }

        .status-cancelled {
            background: rgba(248, 113, 113, 0.2);
            color: #e02424;
        }

        .date-line {
            position: absolute;
            left: 24px;
            top: 48px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary-color), var(--accent-color));
            z-index: 1;
            opacity: 0.2;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 1.8rem;
            }

            .floating-action-btn {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .date-section {
                padding-left: 50px;
            }

            .date-circle {
                width: 42px;
                height: 42px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="fas fa-heartbeat me-2"></i>MediCare
            </a>
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3 rounded-circle"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Profile">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
                <span class="fw-bold text-dark d-none d-md-inline"><?= htmlspecialchars(explode(' ', $user_name)[0]) ?></span>
            </div>
        </div>
    </nav>


    <div class="container py-5">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown mb-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4"></i>
                    <div class="flex-grow-1"><?= $_SESSION['success_message'] ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown mb-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                    <div class="flex-grow-1"><?= $_SESSION['error_message'] ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="section-title animate__animated animate__fadeIn">My Appointments</h2>
                <p class="text-muted mb-0">Your scheduled medical consultations</p>
            </div>
            <a href="home.php#book-appointment.php" class="btn btn-primary d-none d-md-flex align-items-center animate__animated animate__fadeIn">
                <i class="fas fa-plus me-2"></i> New Appointment
            </a>
        </div>

        <?php if (!empty($appointments_by_date)): ?>
            <?php foreach ($appointments_by_date as $date => $appointments):
                if ($date === 'unscheduled') continue;

                try {
                    $date_obj = new DateTime($date);
                    $is_today = ($date == $today);
                    $is_past = ($date < $today);
                    $date_class = $is_today ? 'today' : ($is_past ? 'past' : 'future');
                    $date_title = $is_today ? 'Today' : ($is_past ? $date_obj->format('F j, Y') : $date_obj->format('F j, Y'));
                    $display_date = $date_obj->format('l, F j, Y');
                } catch (Exception $e) {
                    continue; // Skip invalid dates
                }
            ?>
                <div class="date-section animate__animated animate__fadeIn">
                    <div class="date-circle">
                        <span class="date-day"><?= $date_obj->format('d') ?></span>
                        <span class="date-month"><?= $date_obj->format('M') ?></span>
                    </div>
                    <div class="date-line"></div>
                    <h3 class="date-title"><?= $date_title ?></h3>
                    <p class="text-muted mb-3"><?= $display_date ?></p>

                    <div class="row g-4">
                        <?php foreach ($appointments as $row):
                            $status = $is_past ? 'completed' : ($is_today ? 'upcoming' : 'upcoming');
                            $status_class = 'status-' . $status;
                            $status_text = ucfirst($status);
                            $specialization = !empty($row['specialization']) ? $row['specialization'] : 'General Practitioner';

                            // Get display time from created_at
                            $display_time = 'Time not specified';
                            if (!empty($row['created_at'])) {
                                try {
                                    $time_obj = new DateTime($row['created_at']);
                                    $display_time = $time_obj->format('h:i A');
                                } catch (Exception $e) {
                                    error_log("Invalid time format for appointment ID: " . $row['id']);
                                }
                            }
                        ?>
                            <div class="col-12">
                                <div class="appointment-card card <?= $date_class ?>">
                                    <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title fw-bold mb-1">
                                                    <i class="fas fa-user-md card-icon me-2"></i>
                                                    <?= htmlspecialchars($row['Doctor']) ?>
                                                </h5>
                                                <p class="doctor-specialty mb-0"><?= htmlspecialchars($specialization) ?></p>
                                            </div>
                                            <span class="time-badge">
                                                <i class="far fa-clock me-1"></i>
                                                <?= $display_time ?>
                                            </span>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-map-marker-alt card-icon me-2"></i>
                                                <span><?= !empty($row['city']) ? htmlspecialchars($row['city']) : 'Location not specified' ?></span>
                                            </div>
                                            <?php if (!empty($row['Hospital'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-hospital card-icon me-2"></i>
                                                    <span><?= htmlspecialchars($row['Hospital']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($row['Note'])): ?>
                                            <div class="appointment-note">
                                                <h6 class="text-muted small mb-2 fw-bold">NOTES</h6>
                                                <p class="mb-0"><?= htmlspecialchars($row['Note']) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer p-3">
                                        <div class="d-flex justify-content-between">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary flex-grow-1 me-2">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" class="flex-grow-1">
                                                <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                                <button type="submit" name="cancel_appointment" class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="fas fa-trash-alt me-1"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (isset($appointments_by_date['unscheduled'])): ?>
                <div class="date-section animate__animated animate__fadeIn">
                    <h3 class="date-title">Unscheduled Appointments</h3>
                    <p class="text-muted mb-3">Appointments without a specific date</p>

                    <div class="row g-4">
                        <?php foreach ($appointments_by_date['unscheduled'] as $row):
                            $specialization = !empty($row['specialization']) ? $row['specialization'] : 'General Practitioner';
                        ?>
                            <div class="col-12">
                                <div class="appointment-card card">
                                    <span class="status-badge status-upcoming">Unscheduled</span>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title fw-bold mb-1">
                                                    <i class="fas fa-user-md card-icon me-2"></i>
                                                    <?= htmlspecialchars($row['Doctor']) ?>
                                                </h5>
                                                <p class="doctor-specialty mb-0"><?= htmlspecialchars($specialization) ?></p>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-map-marker-alt card-icon me-2"></i>
                                                <span><?= !empty($row['city']) ? htmlspecialchars($row['city']) : 'Location not specified' ?></span>
                                            </div>
                                            <?php if (!empty($row['Hospital'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-hospital card-icon me-2"></i>
                                                    <span><?= htmlspecialchars($row['Hospital']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($row['Note'])): ?>
                                            <div class="appointment-note">
                                                <h6 class="text-muted small mb-2 fw-bold">NOTES</h6>
                                                <p class="mb-0"><?= htmlspecialchars($row['Note']) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer p-3">
                                        <div class="d-flex justify-content-between">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary flex-grow-1 me-2">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" class="flex-grow-1">
                                                <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                                <button type="submit" name="cancel_appointment" class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="fas fa-trash-alt me-1"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state animate__animated animate__fadeIn">
                    <div class="empty-state-icon">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <h4 class="mb-3">No Appointments Scheduled</h4>
                    <p class="text-muted mb-4 px-3">You don't have any medical appointments yet. Schedule your first consultation to get started.</p>
                    <a href="home.php#book-appointment.php" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-plus me-2"></i> Book Appointment
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <a href="home.php#book-appointment.php" class="floating-action-btn d-md-none animate__animated animate__fadeInUp">
        <i class="fas fa-plus"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            gsap.from('.date-section', {
                y: 30,
                opacity: 0,
                duration: 0.6,
                stagger: 0.15,
                ease: "power2.out"
            });

            gsap.from('.appointment-card', {
                y: 20,
                opacity: 0,
                duration: 0.5,
                stagger: 0.1,
                delay: 0.3,
                ease: "back.out"
            });
        });
    </script>
</body>

</html>

<?php
$connection->close();
?>