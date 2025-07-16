<?php
include '../connection.php';

// Initialize message variables
$success_message = '';
$error_message = '';

// Handle deletion of an appointment
if (isset($_GET['delete_id'])) {
    $appointment_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM appointments WHERE id = ?";
    $stmt = $connection->prepare($deleteQuery);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        $success_message = "Appointment deleted successfully.";
    } else {
        $error_message = "Error deleting appointment: " . $stmt->error;
    }
    $stmt->close();
}

// Handle updating the appointment status
if (isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("si", $status, $appointment_id);

    if ($stmt->execute()) {
        $success_message = "Status updated successfully.";
    } else {
        $error_message = "Error updating status: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all patient appointments
$query = "SELECT * FROM appointments ORDER BY Date1 DESC";
$result = $connection->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Appointments</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fc;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            padding: 20px 15px;
            position: fixed;
            overflow-y: auto;
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
        .main-container {
            margin-left: 250px;
            padding: 80px 20px 20px 20px;
        }
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-bottom: none;
            padding: 1rem 1.35rem;
            border-radius: 0.35rem 0.35rem 0 0 !important;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
            padding: 1rem;
            vertical-align: middle;
            background: #f8f9fc;
        }
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #eee;
        }
        .form-select-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            height: calc(1.5em + 0.5rem + 2px);
        }
        .btn-status {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
        <h3>Manage Appointments</h3>
        <div>
            <i class="fas fa-bell"></i>
            <i class="fas fa-envelope" style="margin: 0 15px;"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <div class="main-container">
        <h2 class="page-title">Appointment Management</h2>

        <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">Appointments List</h6>
                <div>
                    <a href="add_appointment.php" class="btn btn-success btn-sm">
                        <i class="fas fa-plus-circle"></i> New Appointment
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Doctor']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($row['Date1'])); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="Scheduled" <?php echo ($row['status'] == 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                            <option value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Canceled" <?php echo ($row['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                                        </select>
                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this appointment?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php $connection->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-close alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Automatically submit form when status changes
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
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