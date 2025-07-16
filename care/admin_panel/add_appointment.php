<?php
include '../connection.php';

// Initialize variables to avoid undefined variable warnings
$patient_name = $doctor_name = $appointment_date = $status = $city = $email = $phone = $note = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data with proper null checks
    $patient_name = isset($_POST['patient_name']) ? trim($_POST['patient_name']) : '';
    $doctor_name = isset($_POST['doctor_name']) ? trim($_POST['doctor_name']) : '';
    $appointment_date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $note = isset($_POST['note']) ? trim($_POST['note']) : null;

    // Validate required fields
    $errors = [];
    if (empty($patient_name)) {
        $errors[] = "Patient name is required";
    }
    if (empty($doctor_name)) {
        $errors[] = "Doctor name is required";
    }
    if (empty($appointment_date)) {
        $errors[] = "Appointment date is required";
    }
    if (empty($status)) {
        $errors[] = "Status is required";
    }

    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($errors)) {
        try {
            $query = "INSERT INTO appointments 
                     (Name, Doctor, Date1, status, city, Email, Phone, Note) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param(
                "ssssssss",
                $patient_name,
                $doctor_name,
                $appointment_date,
                $status,
                $city,
                $email,
                $phone,
                $note
            );

            if ($stmt->execute()) {
                $success_message = "Appointment added successfully.";
                // Clear form after successful submission
                $patient_name = $doctor_name = $appointment_date = $status = $city = $email = $phone = $note = '';
            } else {
                throw new Exception("Database error: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "<strong>Please fix the following errors:</strong><ul>";
        foreach ($errors as $error) {
            $error_message .= "<li>" . htmlspecialchars($error) . "</li>";
        }
        $error_message .= "</ul>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add New Appointment</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --error-color: #f72585;
            --light-gray: #f8f9fa;
            --dark-gray: #6c757d;
            --white: #ffffff;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        /* Sidebar */
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
            left: 0;
        }

        .sidebar h2 {
            margin-bottom: 25px;
            font-size: 24px;
            color: #2c3e50;
            padding-top: 10px;
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
            padding: 25px;
            width: calc(100% - 250px);
        }

        .main-content {
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        /* Alert Messages */
        .alert-container {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 350px;
            z-index: 1100;
        }

        /* Form Styles */
        .form-container {
            max-width: 700px;
            margin: 0 auto;
        }

        .form-title {
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            height: 45px;
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .form-select {
            height: 45px;
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
        }

        .input-icon input,
        .input-icon select {
            padding-left: 40px;
        }

        .datetime-picker {
            position: relative;
        }

        .datetime-picker i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
            pointer-events: none;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .form-col {
            padding-right: 15px;
            padding-left: 15px;
            flex: 0 0 50%;
            max-width: 50%;
        }

        @media (max-width: 768px) {
            .form-col {
                flex: 0 0 100%;
                max-width: 100%;
            }
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
            }

            .alert-container {
                width: calc(100% - 40px);
                left: 20px;
                right: 20px;
            }
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
        <h3>Add New Appointment</h3>
        <div>
            <i class="fas fa-bell"></i>
            <i class="fas fa-envelope" style="margin: 0 15px;"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Alert Messages Container -->
    <div class="alert-container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <div class="main">
        <div class="main-content">
            <div class="form-container">
                <h3 class="form-title"><i class="fas fa-calendar-plus me-2"></i>Add New Appointment</h3>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="patient_name" class="form-label">Patient Name *</label>
                                <div class="input-icon">
                                    <i class="fas fa-user-injured"></i>
                                    <input type="text" class="form-control" id="patient_name" name="patient_name"
                                        placeholder="Enter patient name" required
                                        value="<?php echo htmlspecialchars($patient_name); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="doctor_name" class="form-label">Doctor Name *</label>
                                <div class="input-icon">
                                    <i class="fas fa-user-md"></i>
                                    <input type="text" class="form-control" id="doctor_name" name="doctor_name"
                                        placeholder="Enter doctor name" required
                                        value="<?php echo htmlspecialchars($doctor_name); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="appointment_date" class="form-label">Appointment Date & Time *</label>
                                <div class="datetime-picker">
                                    <input type="datetime-local" class="form-control" id="appointment_date"
                                        name="appointment_date" required
                                        value="<?php echo htmlspecialchars($appointment_date); ?>">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="" disabled <?php echo empty($status) ? 'selected' : ''; ?>>Select appointment status</option>
                                    <option value="Scheduled" <?php echo $status === 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="Completed" <?php echo $status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Canceled" <?php echo $status === 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="city" class="form-label">City</label>
                                <div class="input-icon">
                                    <i class="fas fa-city"></i>
                                    <input type="text" class="form-control" id="city" name="city"
                                        placeholder="Enter city"
                                        value="<?php echo htmlspecialchars($city); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter email"
                                        value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="Enter phone number"
                                        value="<?php echo htmlspecialchars($phone); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note"
                            placeholder="Any additional notes..."><?php echo htmlspecialchars($note); ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Add Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum datetime to current date/time
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('appointment_date').min = minDateTime;

            // Phone number input formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                const x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
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