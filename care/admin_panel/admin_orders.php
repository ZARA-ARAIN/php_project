<?php
include("../connection.php");

// If order_id is passed (AJAX request), return only that order's detail
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Using MySQLi prepared statement
    $stmt = $connection->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        echo "<div class='order-header'>";
        echo "<h3><i class='fas fa-receipt'></i> Order #{$order['order_id']}</h3>";
        echo "<div class='order-status'>";
        // Add status management if you have it in your database
        echo "</div>";
        echo "</div>";

        echo "<div class='order-details-grid'>";

        // Customer Information
        echo "<div class='order-section customer-info'>";
        echo "<h4><i class='fas fa-user'></i> Customer Information</h4>";
        echo "<div class='detail-row'><span class='detail-label'>Name:</span> " . htmlspecialchars($order['first_name']) . " " . htmlspecialchars($order['last_name']) . "</div>";
        echo "<div class='detail-row'><span class='detail-label'>Email:</span> " . htmlspecialchars($order['email']) . "</div>";
        if (!empty($order['phone'])) {
            echo "<div class='detail-row'><span class='detail-label'>Phone:</span> " . htmlspecialchars($order['phone']) . "</div>";
        }
        if (!empty($order['address'])) {
            echo "<div class='detail-row'><span class='detail-label'>Address:</span> " . htmlspecialchars($order['address']) . "</div>";
        }
        echo "</div>";

        // Order Information
        echo "<div class='order-section order-info'>";
        echo "<h4><i class='fas fa-info-circle'></i> Order Information</h4>";
        echo "<div class='detail-row'><span class='detail-label'>Order Date:</span> " . date('M d, Y h:i A', strtotime($order['order_date'])) . "</div>";
        echo "<div class='detail-row'><span class='detail-label'>Payment Method:</span> " . htmlspecialchars($order['payment_method']) . "</div>";
        echo "<div class='detail-row'><span class='detail-label'>Total Amount:</span> $" . number_format($order['total_amount'], 2) . "</div>";
        echo "</div>";

        echo "</div>"; // Close grid

        // Products Section
        $products = json_decode($order['products'], true);
        if (!empty($products)) {
            echo "<div class='order-section'>";
            echo "<h4><i class='fas fa-box-open'></i> Products (" . count($products) . ")</h4>";
            echo "<div class='products-grid'>";
            foreach ($products as $product) {
                echo "<div class='product-card'>";
                // Check both possible image field names
                $image_path = $product['image_path'] ?? $product['image'] ?? '';
                if (!empty($image_path)) {
                    // Ensure the path is correct and add base URL if needed
                    $full_image_path = (strpos($image_path, 'http') === 0) ? $image_path : '../' . ltrim($image_path, '/');
                    echo "<div class='product-image-container'>";
                    echo "<img src='{$full_image_path}' alt='{$product['name']}' class='product-image'>";
                    echo "</div>";
                } else {
                    echo "<div class='product-image-placeholder'>";
                    echo "<i class='fas fa-image'></i>";
                    echo "</div>";
                }
                echo "<div class='product-details'>";
                echo "<h5>" . htmlspecialchars($product['name']) . "</h5>";
                echo "<div class='product-meta'>";
                echo "<span>Price: $" . number_format($product['price'], 2) . "</span>";
                echo "<span>Qty: " . $product['quantity'] . "</span>";
                echo "</div>";
                echo "<div class='product-subtotal'>Subtotal: $" . number_format($product['price'] * $product['quantity'], 2) . "</div>";
                echo "</div>";
                echo "</div>"; // Close product-card
            }
            echo "</div>"; // Close products-grid
            echo "</div>"; // Close order-section
        }

        // Add action buttons
        echo "<div class='order-actions'>";
        echo "<button class='btn print-btn' onclick='window.print()'><i class='fas fa-print'></i> Print</button>";
        echo "<button class='btn close-btn' onclick='closeModal()'><i class='fas fa-times'></i> Close</button>";
        echo "</div>";
    } else {
        echo "<p class='error-message'>Order not found.</p>";
    }
    exit;
}

// Load all orders
$result = $connection->query("SELECT * FROM orders ORDER BY order_date DESC");
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Care Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2ecc71;
            --secondary-dark: #27ae60;
            --danger: #e74c3c;
            --danger-dark: #c0392b;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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

        .container {
            max-width: 1200px;
            margin: 50px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-top: 0;
            display: flex;
            align-items: center;
        }

        h1 i {
            margin-right: 10px;
            color: var(--primary);
        }

        /* Enhanced Table Styles */
        .table-container {
            overflow-x: auto;
            margin-top: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f5f7fa;
        }

        /* Button Styles */
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 8px;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .btn i {
            margin-right: 5px;
        }

        .view-btn {
            background-color: var(--primary);
            color: white;
        }

        .view-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        /* Modal Styles */
        #orderModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 85%;
            max-width: 900px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.4s;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            margin: 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }

        .modal-header h2 i {
            margin-right: 10px;
            color: var(--primary);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #777;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #333;
        }

        /* Order Details Styles */
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            background: #e3f2fd;
            color: var(--primary-dark);
        }

        .order-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        @media (max-width: 768px) {
            .order-details-grid {
                grid-template-columns: 1fr;
            }
        }

        .order-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .order-section h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }

        .order-section h4 i {
            margin-right: 8px;
            color: var(--primary);
            font-size: 16px;
        }

        .detail-row {
            margin-bottom: 10px;
            display: flex;
        }

        .detail-label {
            font-weight: 500;
            color: #555;
            min-width: 120px;
            display: inline-block;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-image-container {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            overflow: hidden;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-image-placeholder {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
            color: #ccc;
            font-size: 40px;
        }

        .product-details {
            padding: 15px;
        }

        .product-details h5 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }

        .product-subtotal {
            font-weight: 600;
            color: var(--primary-dark);
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }

        /* Order Actions */
        .order-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .print-btn {
            background-color: var(--gray);
            color: white;
        }

        .print-btn:hover {
            background-color: #5a6268;
        }



        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .container {
                padding: 20px 15px;
            }

            table {
                font-size: 14px;
            }

            th,
            td {
                padding: 12px 10px;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                padding: 20px 15px;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <!-- Sidebar (unchanged) -->
    <div class="sidebar">
        <h2><i class="fas fa-stethoscope"></i> Care</h2>
        <a href="index.php" class="active"><i class="fa fa-dashboard"></i> Dashboard</a>

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


    <!-- Header (unchanged) -->
    <div class="header">
        <h3>Order Management</h3>
        <div class="header-icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-envelope"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="container">
            <h1><i class="fas fa-shopping-bag"></i> Order Management</h1>

            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Orders Found</h3>
                    <p>There are currently no orders in the system.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order):
                                $products = json_decode($order['products'], true);
                                $product_count = count($products);
                            ?>
                                <tr>
                                    <td>#<?= $order['order_id'] ?></td>
                                    <td><?= htmlspecialchars($order['first_name']) ?> <?= htmlspecialchars($order['last_name']) ?></td>
                                    <td><?= htmlspecialchars($order['email']) ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></td>
                                    <td>
                                        <button class="btn view-btn" onclick="viewOrder(<?= $order['order_id'] ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <!-- Order Details Modal -->
    <div id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle"><i class="fas fa-receipt"></i> Order Details</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div id="orderDetails"></div>
        </div>
    </div>

    <script>
        function viewOrder(orderId) {
            // Show loading state
            document.getElementById('orderDetails').innerHTML = `
                <div style="text-align: center; padding: 30px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary);"></i>
                    <p>Loading order details...</p>
                </div>
            `;

            document.getElementById('orderModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';

            fetch('?order_id=' + orderId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderDetails').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('orderDetails').innerHTML = `
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Error loading order details. Please try again.</p>
                        </div>
                    `;
                });
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('orderModal')) {
                closeModal();
            }
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