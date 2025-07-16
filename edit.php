<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

$userEmail = $_SESSION['user_email'];
$user_name = $_SESSION['Name'] ?? explode('@', $userEmail)[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $date = $_POST['Date1'];
    $doctor = $_POST['doctor'];
    $city = $_POST['city'];
    $note = $_POST['note'];

    $stmt = $connection->prepare("UPDATE appointments SET Date1=?, Doctor=?, city=?, Note=? WHERE id=? AND Email=?");
    $stmt->bind_param("ssssis", $date, $doctor, $city, $note, $id, $userEmail);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Appointment updated successfully.";
        header("Location: appointment.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update appointment.";
    }
    $stmt->close();
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $connection->prepare("SELECT * FROM appointments WHERE id=? AND email=?");
    $stmt->bind_param("is", $id, $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    if (!$appointment) {
        $_SESSION['error_message'] = "Appointment not found.";
        header("Location: appointment.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: appointment.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Appointment | MediCare</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --primary-light: #a29bfe;
            --accent-color: #00cec9;
            --light-bg: #f9f9ff;
            --card-bg: #ffffff;
            --card-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
            --text-dark: #2d3436;
            --text-muted: #636e72;
        }

         body {
            background-color: var(--light-bg);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-dark);
            background-image:
                radial-gradient(circle at 15% 50%, rgba(108, 92, 231, 0.03) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(0, 206, 201, 0.03) 0%, transparent 25%);
        }


        .container {
            max-width: 800px;
        
        }

        .edit-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-top: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 15px;
        }

        h2:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border-radius: 4px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(108, 92, 231, 0.25);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.35);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }

        .btn-secondary {
            border-radius: 10px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .alert {
            border-radius: 10px;
        }

        .card-icon {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
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
            margin-left: -170px;
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
            margin-right: -50px;
            margin-left: 100px;
            
        }

        .user-avatar:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(108, 92, 231, 0.35);
        }
        .top{
            margin-right: -350px;
            margin-left: 50px;
            padding: 10px;
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
                <div class="user-avatar me-9 rounded-circle"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Profile">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
                <div class="top">
                <span class="fw-bold text-dark d-none d-md-inline"><?= htmlspecialchars(explode(' ', $user_name)[0]) ?></span>
            </div>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="edit-card">
            <h2 class="mb-4">Edit Appointment</h2>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3"></i>
                        <div><?= $_SESSION['error_message'] ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($appointment['id']) ?>">

                <div class="mb-4">
                    <label for="date" class="form-label">
                        <i class="far fa-calendar-alt card-icon me-2"></i> Date
                    </label>
                    <input type="date" id="date" name="Date1" value="<?= htmlspecialchars($appointment['Date1']) ?>" class="form-control form-control-lg" required>
                </div>

                <div class="mb-4">
                    <label for="doctor" class="form-label">
                        <i class="fas fa-user-md card-icon me-2"></i> Doctor
                    </label>
                    <input type="text" id="doctor" name="doctor" value="<?= htmlspecialchars($appointment['Doctor']) ?>" class="form-control form-control-lg" required>
                </div>

                <div class="mb-4">
                    <label for="city" class="form-label">
                        <i class="fas fa-map-marker-alt card-icon me-2"></i> City
                    </label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($appointment['city']) ?>" class="form-control form-control-lg" required>
                </div>

                <div class="mb-4">
                    <label for="note" class="form-label">
                        <i class="far fa-sticky-note card-icon me-2"></i> Note
                    </label>
                    <textarea id="note" name="note" class="form-control" rows="4"><?= htmlspecialchars($appointment['Note']) ?></textarea>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <a href="appointment.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>

</html>

<?php
$connection->close();
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>