<?php
include '../connection.php';

// Handle availability toggle
if (isset($_GET['toggle_availability'])) {
    $doctor_id = $_GET['toggle_availability'];
    $current_availability = $_GET['current_availability'];
    $new_availability = ($current_availability == 1) ? 0 : 1;

    $updateQuery = "UPDATE doctors SET is_available = ? WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("ii", $new_availability, $doctor_id);

    if ($stmt->execute()) {
        $success_message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            Doctor availability updated successfully.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } else {
        $error_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Error updating availability: " . $stmt->error . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
}

// Handle deletion of a doctor
if (isset($_GET['delete_id'])) {
    $doctor_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM doctors WHERE id = ?";
    $stmt = $connection->prepare($deleteQuery);
    $stmt->bind_param("i", $doctor_id);

    if ($stmt->execute()) {
        $success_message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            Doctor record deleted successfully.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } else {
        $error_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Error deleting doctor: " . $stmt->error . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
}

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$query = "SELECT * FROM doctors WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR specialization LIKE ? OR city LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    $types .= 'sss';
}

if ($filter != 'all') {
    $query .= " AND is_available = ?";
    $params[] = ($filter == 'available') ? 1 : 0;
    $types .= 'i';
}

$stmt = $connection->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Doctors</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
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

        .page-title {
            color: #5a5c69;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        /* Alert Messages */
        .alert-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1100;
            width: 350px;
        }

        /* Table Styles */
        .table-responsive {
            margin-top: 15px;
        }

        .table td,
        .table th {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .table {
            margin-bottom: 0;
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

        .doctor-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e3e6f0;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            /* Added gap between buttons */
            justify-content: center;
        }

        .action-btns .btn {
            min-width: 70px;
            padding: 6px 10px;
            font-size: 14px;
        }

        /* Badges */
        .badge-available {
            background-color: var(--accent-color);
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-unavailable {
            background-color: var(--danger-color);
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* Search and Filter */
        .search-filter-container {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .search-box {
            max-width: 300px;
        }

        .doctor-info-cell {
            min-width: 200px;
        }

        .doctor-info-cell h6 {
            margin-bottom: 3px;
            font-size: 15px;
        }

        .doctor-info-cell small {
            font-size: 13px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1rem 1.35rem;
            border-radius: 0.35rem 0.35rem 0 0 !important;
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

            .action-btns {
                flex-direction: column;
                gap: 5px;
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

    <!-- Alert Messages -->
    <?php if (isset($success_message)): ?>
        <div class="alert-container">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert-container">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main">
        <div class="container-fluid">
            <h2 class="page-title">Doctor Management</h2>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Doctors List</h6>
                    <div>
                        <a href="add_doctor.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle"></i> Add New Doctor
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="search-filter-container">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="input-group search-box">
                                        <input type="text" class="form-control" name="search" placeholder="Search doctors..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group float-md-end" role="group">
                                        <button type="submit" name="filter" value="all" class="btn btn-outline-secondary <?php echo $filter == 'all' ? 'active' : ''; ?>">All</button>
                                        <button type="submit" name="filter" value="available" class="btn btn-outline-secondary <?php echo $filter == 'available' ? 'active' : ''; ?>">Available</button>
                                        <button type="submit" name="filter" value="unavailable" class="btn btn-outline-secondary <?php echo $filter == 'unavailable' ? 'active' : ''; ?>">Unavailable</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Location</th>
                                    <th>Contact</th>
                                    <th>Availability</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td class="doctor-info-cell">
                                            <div class="d-flex align-items-center">
                                                <img src="../images/doctor/<?php echo $row['image']; ?>" class="doctor-img me-3" alt="Doctor Image">
                                                <div>
                                                    <h6><?php echo $row['name']; ?></h6>
                                                    <small class="text-muted"><?php echo $row['email']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $row['specialization']; ?></td>
                                        <td><?php echo $row['city']; ?></td>
                                        <td><?php echo $row['contact']; ?></td>
                                        <td>
                                            <span class="badge <?php echo ($row['is_available'] ?? 0) ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo ($row['is_available'] ?? 0) ? 'Available' : 'Unavailable'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="edit_doctor.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="?delete_id=<?php echo $row['id']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this doctor?');">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($result->num_rows == 0): ?>
                        <div class="alert alert-info mt-3">No doctors found matching your criteria.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Function to confirm before delete
        function confirmDelete(doctorName) {
            return confirm('Are you sure you want to delete ' + doctorName + '?');
        }
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