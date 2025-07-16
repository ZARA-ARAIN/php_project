<?php
session_start();
require_once('../connection.php');

// Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$current_doctor = $_SESSION['doctor_data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $available_times = $_POST['available_times'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    $stmt = $connection->prepare("UPDATE doctors SET available_times = ?, is_available = ? WHERE id = ?");
    $stmt->bind_param("sii", $available_times, $is_available, $doctor_id);

    if ($stmt->execute()) {
        $stmt = $connection->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $_SESSION['doctor_data'] = $result->fetch_assoc();
        header("Location: schedule.php?success=1");
        exit();
    } else {
        $error = "Error updating availability: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Availability - Doctor Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
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
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #007BFF;
            color: white;
            padding: 15px 20px;
            font-size: 1.2rem;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 10px 12px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-left: 5px solid;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #e7f8ed;
            border-color: #28a745;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8e7e7;
            border-color: #dc3545;
            color: #721c24;
        }

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

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #007BFF;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .switch-label {
            margin-left: 10px;
            font-weight: 500;
            display: inline-block;
            vertical-align: middle;
        }

        small {
            display: block;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<?php include('sidebar.php'); ?>
<?php include('header.php'); ?>

<div class="main-content">
    <div class="card">
        <div class="card-header">
            Set Availability
        </div>
        <div class="card-body">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Availability updated successfully!</div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Available Times</label>
                    <textarea name="available_times" rows="3" required><?= htmlspecialchars($_SESSION['doctor_data']['available_times']) ?></textarea>
                    <small>Example: Monday: 9am-5pm, Tuesday: 10am-4pm, etc.</small>
                </div>

                <div class="form-group">
                    <label class="switch-label">Currently Available for Appointments:</label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_available" <?= $_SESSION['doctor_data']['is_available'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
