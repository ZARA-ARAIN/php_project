<?php
include '../connection.php';

$message = '';

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($connection->query("DELETE FROM cities WHERE id=$id")) {
        header("Location: manage_cities.php?msg=deleted");
        exit;
    }
}

// Handle insert request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city_name'])) {
    $city_name = trim($_POST['city_name']);

    if (!empty($city_name)) {
        // Normalize city name (case-insensitive check)
        $city_name = ucwords(strtolower($city_name));

        $check = $connection->prepare("SELECT id FROM cities WHERE LOWER(city_name) = LOWER(?)");
        $check->bind_param("s", $city_name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = '<div class="alert alert-warning">City already exists.</div>';
        } else {
            $stmt = $connection->prepare("INSERT INTO cities (city_name) VALUES (?)");
            $stmt->bind_param("s", $city_name);
            if ($stmt->execute()) {
                header("Location: manage_cities.php?msg=added");
                exit;
            } else {
                $message = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-warning">City name cannot be empty.</div>';
    }
}

// Handle messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $message = '<div class="alert alert-success">City deleted successfully.</div>';
    } elseif ($_GET['msg'] === 'added') {
        $message = '<div class="alert alert-success">City added successfully.</div>';
    }
}

$result = $connection->query("SELECT * FROM cities ORDER BY city_name");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Cities</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        /* Sidebar */
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

        .main-content {
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
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
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Manage Cities</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">+ Add City</button>
            </div>

            <?= $message ?>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th width="10%">#</th>
                        <th>City Name</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= htmlspecialchars($row['city_name']) ?></td>
                                <td>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this city?')">Delete</a>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="3" class="text-center">No cities found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add City Modal -->
        <div class="modal fade" id="addCityModal" tabindex="-1" aria-labelledby="addCityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCityModalLabel">Add New City</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="city_name" class="form-control" placeholder="Enter city name" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add City</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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