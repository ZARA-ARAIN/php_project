<?php
session_start();
require '../connection.php';

// Check if user is logged in as admin (you should implement proper authentication)
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Handle message deletion
if ($isAdmin && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $connection->real_escape_string($_GET['delete']);
    $connection->query("DELETE FROM messages WHERE id = $id");
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

$result = $connection->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Messages Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-color: #5a5c69;
            --light-gray: #e3e6f0;
        }

        body {
            background-color: #f8f9fc;
            color: var(--text-color);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Sidebar Styles (unchanged) */
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

        /* Header Styles (unchanged) */
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header h3 {
            font-size: 18px;
            margin: 55px;
        }

        .header-icons i {
            margin-left: 15px;
            cursor: pointer;
            font-size: 18px;
        }

        /* Main Content Styles */
        .main {
            margin-left: 250px;
            margin-top: 60px;
            padding: 25px;
            min-height: calc(100vh - 120px);
        }



        .message-card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
        }

        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.5rem 0 rgba(58, 59, 69, 0.2);
        }

        .message-header {
            border-bottom: 1px solid var(--light-gray);
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }

        .message-card:hover .message-actions {
            opacity: 1;
        }

        .message-content {
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .message-timestamp {
            font-size: 0.85rem;
            color: #858796;
        }

        .btn-delete {
            color: #e74a3b;
            background: none;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .btn-delete:hover {
            background: rgba(231, 74, 59, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .empty-state i {
            font-size: 3rem;
            color: #dddfeb;
            margin-bottom: 1rem;
        }

        .badge-new {
            background-color: #1cc88a;
            margin-left: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }

        .message-meta {
            display: flex;
            align-items: center;
        }

        .message-sender {
            font-weight: 600;
            color: var(--primary-color);
        }

        .message-email {
            color: #858796;
            font-size: 0.9rem;
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
    <div class="main">
        <div class="container py-4">
            <div class="header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-envelope-open-text me-2"></i>Client Messages
                    </h1>
                    <?php if ($isAdmin): ?>
                        <div>
                            <span class="badge bg-primary">
                                <i class="fas fa-user-shield me-1"></i> Admin Mode
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">All Messages</h6>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $result->num_rows; ?> message(s)
                                </span>
                            </div>
                            <div class="card-body">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <div class="message-card p-4">
                                        <div class="message-header">
                                            <div class="message-meta">
                                                <div class="user-avatar">
                                                    <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="message-sender">
                                                        <?php echo htmlspecialchars($row['name']); ?>
                                                        <?php if (strtotime($row['created_at']) > time() - 86400): ?>
                                                            <span class="badge badge-new">NEW</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="message-email">
                                                        <?php echo htmlspecialchars($row['email']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($isAdmin): ?>
                                                <div class="message-actions">
                                                    <button class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="message-content">
                                            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                        </div>

                                        <div class="message-timestamp">
                                            <i class="far fa-clock me-1"></i>
                                            <?php echo date('M j, Y \a\t g:i A', strtotime($row['created_at'])); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="far fa-envelope-open"></i>
                    <h3 class="h4 text-gray-800 mb-3">No Messages Found</h3>
                    <p class="mb-0">You don't have any client messages yet. When clients contact you, their messages will appear here.</p>
                </div>
            <?php endif; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function confirmDelete(id) {
                if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
                    window.location.href = '?delete=' + id;
                }
            }

            // Auto-refresh the page every 5 minutes to check for new messages
            setTimeout(function() {
                window.location.reload();
            }, 300000);
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