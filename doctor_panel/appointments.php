<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty(session_id())) {
    die("Session initialization failed");
}

require_once('../connection.php');

if (!isset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['doctor_data']['name']) || 
    $_SESSION['user_role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_name = $_SESSION['doctor_data']['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $status = $_POST['status'];
    $valid_statuses = ['scheduled', 'completed', 'cancelled'];

    if (in_array($status, $valid_statuses)) {
        $stmt = $connection->prepare("UPDATE appointments SET status = ? WHERE id = ? AND Doctor = ?");
        $stmt->bind_param("sis", $status, $appointment_id, $doctor_name);
        $stmt->execute();
    }
}

$column_check = $connection->query("SHOW COLUMNS FROM appointments LIKE 'status'");
if ($column_check->num_rows == 0) {
    $connection->query("ALTER TABLE appointments ADD COLUMN status ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled'");
}

try {
    $stmt = $connection->prepare("SELECT * FROM appointments WHERE Doctor = ? ORDER BY Date1 DESC");
    $stmt->bind_param("s", $doctor_name);
    $stmt->execute();
    $appointments = $stmt->get_result();
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Appointments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f7;
            color: #333;
        }

        .main-content {
            margin-left: 280px;
            padding: 30px 20px;
            transition: margin 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background-color: #007BFF;
            color: #fff;
            padding: 20px;
            font-size: 1.3rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-new {
            background-color: #fff;
            color: #007BFF;
            padding: 8px 14px;
            border-radius: 5px;
            border: 1px solid #007BFF;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
            transition: 0.2s ease;
        }

        .btn-new:hover {
            background-color: #007BFF;
            color: #fff;
        }

        .card-body {
            padding: 20px;
        }

        .alert {
            padding: 12px 15px;
            background-color: #dff0ff;
            border-left: 5px solid #007BFF;
            color: #0d47a1;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.95rem;
        }

        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f0f2f5;
            color: #333;
        }

        tr:hover {
            background-color: #f9fbfd;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 0.75rem;
            border-radius: 50px;
            font-weight: bold;
        }

        .badge-primary { background-color: #d0e9ff; color: #007BFF; }
        .badge-success { background-color: #d4edda; color: #218838; }
        .badge-secondary { background-color: #e2e3e5; color: #6c757d; }

        select {
            padding: 5px 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 0.85rem;
                padding: 8px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .btn-new {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>

<?php
define('INCLUDE_SIDEBAR', true);
require_once('sidebar.php');
?>
<?php include('header.php'); ?>

<div class="main-content">
    <div class="card">
        <div class="card-header">
            Doctor's Appointments
            <a href="new_appointment.php" class="btn-new">+ New Appointment</a>
        </div>
        <div class="card-body">
            <?php if ($appointments->num_rows === 0): ?>
                <div class="alert">No appointments found.</div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Date & Time</th>
                        <th>Phone</th>
                        <th>Note</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($appointment = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($appointment['Name']); ?></strong><br>
                            <small><?= htmlspecialchars($appointment['Email']); ?></small>
                        </td>
                        <td><?= date('M j, Y g:i A', strtotime($appointment['Date1'])); ?></td>
                        <td><?= htmlspecialchars($appointment['Phone']); ?></td>
                        <td><?= htmlspecialchars($appointment['Note']); ?></td>
                        <td><?= htmlspecialchars($appointment['city']); ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                $s = $appointment['status'] ?? 'scheduled';
                                echo $s === 'scheduled' ? 'primary' : ($s === 'completed' ? 'success' : 'secondary');
                            ?>">
                                <?= ucfirst($s); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="appointment_id" value="<?= $appointment['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="scheduled" <?= $s === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="completed" <?= $s === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?= $s === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="change_status" value="1">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
