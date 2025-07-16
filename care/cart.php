<?php
session_start();
require 'connection.php'; // Database connection

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to handle cart operations
function handleCartOperations($connection) {
    // Add to Cart
    if (isset($_GET['action']) && $_GET['action'] == 'add') {
        if (!empty($_GET['name']) && is_numeric($_GET['price']) && !empty($_GET['image'])) {
            $product = [
                'name' => htmlspecialchars($_GET['name']),
                'price' => floatval($_GET['price']),
                'image' => htmlspecialchars($_GET['image']),
                'quantity' => 1
            ];

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $productExists = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['name'] === $product['name']) {
                    $item['quantity']++;
                    $productExists = true;
                    break;
                }
            }

            if (!$productExists) {
                $_SESSION['cart'][] = $product;
            }

            header("Location: cart.php");
            exit();
        }
    }

    // Remove Item
    if (isset($_GET['remove'])) {
        $remove_name = $_GET['remove'];
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['name'] == $remove_name) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        header("Location: cart.php");
        exit();
    }

    // Handle Checkout
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
        // CSRF protection
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['checkout_error'] = "Security token mismatch. Please try again.";
            header("Location: cart.php");
            exit();
        }

        // Only process if cart isn't empty
        if (!empty($_SESSION['cart'])) {
            // Validate required fields
            $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'country', 'payment_method'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['checkout_error'] = "Please fill in all required fields.";
                    header("Location: cart.php");
                    exit();
                }
            }

            // Process order
            processOrder($connection);
        }
    }

    // Handle Quantity Update
    if (isset($_POST['update_cart'])) {
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $name => $quantity) {
                $name = urldecode($name);
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['name'] == $name) {
                            $item['quantity'] = max(1, intval($quantity));
                            break;
                        }
                    }
                }
            }
        }
        header("Location: cart.php");
        exit();
    }
}

// Function to process the order
function processOrder($connection) {
    // Get form data with proper sanitization
    $fields = [
        'first_name', 'last_name', 'email', 'phone', 'address', 
        'address2', 'city', 'state', 'zip', 'country', 'payment_method'
    ];
    
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = isset($_POST[$field]) ? mysqli_real_escape_string($connection, $_POST[$field]) : '';
    }
    
    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Prepare products data as JSON
    $products_json = json_encode($_SESSION['cart']);
    
    // Insert into database using prepared statement
    $query = "INSERT INTO orders (
        first_name, last_name, email, phone, address, address2, 
        city, state, zip, country, payment_method, products, total_amount, order_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $connection->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param(
            "ssssssssssssd",
            $data['first_name'], $data['last_name'], $data['email'], $data['phone'], 
            $data['address'], $data['address2'], $data['city'], $data['state'], 
            $data['zip'], $data['country'], $data['payment_method'], $products_json, $total
        );
        
        if ($stmt->execute()) {
            $_SESSION['show_thank_you'] = true;
            $_SESSION['last_order_id'] = $connection->insert_id;
            unset($_SESSION['cart']);
        } else {
            $_SESSION['checkout_error'] = "Error processing your order. Please try again.";
            error_log("Database error: " . $stmt->error);
        }
        
        $stmt->close();
    } else {
        $_SESSION['checkout_error'] = "Database connection error. Please try again.";
        error_log("Prepare error: " . $connection->error);
    }
    
    header("Location: cart.php");
    exit();
}

// Handle cart operations
handleCartOperations($connection);

// Check if we should show thank you message
$show_thank_you = isset($_SESSION['show_thank_you']) && $_SESSION['show_thank_you'];
if ($show_thank_you) {
    unset($_SESSION['show_thank_you']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | MediCare</title>
    <link rel="shortcut icon" href="/project/care/images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Base Styles */
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
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        a {
            text-decoration: none !important;
        }

        /* Header Styles */
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .breadcrumbs {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .breadcrumbs li {
            display: flex;
            align-items: center;
        }

        .breadcrumbs a {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }

        .breadcrumbs a:hover {
            color: white;
        }

        .breadcrumbs li:not(:last-child)::after {
            content: "›";
            margin-left: 10px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Cart Table */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .cart-table th {
            background: var(--light);
            padding: 18px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid var(--light-gray);
        }

        .cart-table td {
            padding: 18px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        .cart-table tr:last-child td {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
        }

        .product-thumbnail {
            width: 90px;
            height: 90px;
            margin-right: 20px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .product-thumbnail img {
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
        }

        .product-name {
            font-weight: 500;
            color: var(--dark);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: var(--light);
        }

        .qty-input {
            width: 50px;
            height: 32px;
            padding: 0;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-weight: 500;
        }

        .remove-btn {
            color: var(--danger);
            border: none;
            background: none;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
            padding: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .remove-btn:hover {
            background: #f8d7da;
            color: var(--danger-dark);
        }

        /* Cart Actions */
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .coupon-box {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .coupon-input {
            padding: 12px 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            min-width: 250px;
            transition: all 0.3s;
        }

        .coupon-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .btn-success {
            background: var(--secondary);
            color: white;
        }

        .btn-success:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
        }

        /* Cart Totals */
        .cart-totals {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        .cart-totals h2 {
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 10px;
        }

        .cart-totals h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }

        .totals-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .totals-table th {
            text-align: left;
            padding: 12px 0;
            font-weight: 500;
            color: var(--gray);
        }

        .totals-table td {
            text-align: right;
            padding: 12px 0;
            font-weight: 500;
        }

        .order-total th,
        .order-total td {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid var(--light-gray);
            padding-top: 15px;
            color: var(--dark);
        }

        .checkout-btn {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            border-radius: 8px;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 60px 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin: 30px 0;
        }

        .empty-cart i {
            font-size: 60px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .empty-cart p {
            color: var(--gray);
            margin-bottom: 25px;
            font-size: 16px;
        }

        /* Thank You Message */
        .thank-you {
            background: var(--secondary);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            animation: fadeIn 0.5s;
            box-shadow: 0 4px 20px rgba(46, 204, 113, 0.3);
        }

        .thank-you h3 {
            margin-top: 0;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .thank-you p {
            margin-bottom: 0;
            font-size: 16px;
            opacity: 0.9;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Base Styles */
        .checkout-form {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .checkout-form h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin: 25px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeff5;
        }

        /* Error Message */
        .error-message {
            background: #feecec;
            color: #d32f2f;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .error-message i {
            font-size: 1.2rem;
        }

        /* Form Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Form Group Styles */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
            font-size: 0.95rem;
        }

        .form-group .required {
            color: #e53e3e;
        }

        .form-control {
            width: 90%;
            padding: 12px 14px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
            background-color: #fff;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 35px;
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .payment-method {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
        }

        .payment-method:hover {
            border-color: #4299e1;
            background: #fff;
        }

        .payment-method input[type="radio"] {
            margin: 0;
            accent-color: #4299e1;
        }

        .payment-method .icon {
            font-size: 1.5rem;
            color: #4a5568;
        }

        .payment-method .details h4 {
            margin: 0 0 4px;
            font-size: 1rem;
            color: #2d3748;
        }

        .payment-method .details p {
            margin: 0;
            font-size: 0.85rem;
            color: #718096;
        }

        .payment-method input[type="radio"]:checked+.icon+.details {
            color: #4299e1;
        }

        .payment-method input[type="radio"]:checked~* {
            color: #4299e1;
        }

        .payment-method input[type="radio"]:checked {
            border-color: #4299e1;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                flex-direction: column;
                gap: 15px;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }
        }

        /* Focus States */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }

        /* Validation States */
        input:invalid,
        select:invalid {
            border-color: #e53e3e;
        }

        input:valid,
        select:valid {
            border-color: #38a169;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 30px 0;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .product-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .product-thumbnail {
                margin-bottom: 15px;
                margin-right: 0;
            }

            .cart-actions {
                flex-direction: column;
            }

            .coupon-box {
                width: 100%;
            }

            .coupon-input {
                min-width: 0;
                width: 100%;
            }

            .cart-table th,
            .cart-table td {
                padding: 12px;
            }

            .cart-table thead {
                display: none;
            }

            .cart-table tr {
                display: block;
                margin-bottom: 15px;
                border-bottom: 2px solid var(--light-gray);
            }

            .cart-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid var(--light-gray);
            }

            .cart-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: 600;
                color: var(--dark);
            }

            .quantity-controls {
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Your Shopping Cart</h1>
            <ul class="breadcrumbs">
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li>Cart</li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if ($show_thank_you): ?>
            <div class="thank-you">
                <h3><i class="fas fa-check-circle"></i> Thank you for your purchase!</h3>
                <p>Your order #<?php echo $_SESSION['last_order_id']; ?> has been received and will be processed shortly.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['checkout_error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $_SESSION['checkout_error']; ?></span>
            </div>
            <?php unset($_SESSION['checkout_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['cart'])): ?>
            <form method="post" action="cart.php" id="checkout-form" class="checkout-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- Cart Table -->
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grand_total = 0;
                        foreach ($_SESSION['cart'] as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $grand_total += $subtotal;
                        ?>
                            <tr>
                                <td data-label="Product">
                                    <div class="product-info">
                                        <div class="product-thumbnail">
                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                        </div>
                                        <span class="product-name"><?php echo $item['name']; ?></span>
                                    </div>
                                </td>
                                <td data-label="Price" class="price">$<?php echo number_format($item['price'], 2); ?></td>
                                <td data-label="Quantity">
                                    <div class="quantity-controls">
                                        <button type="button" class="qty-btn qty-minus" data-name="<?php echo $item['name']; ?>">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="quantity[<?php echo urlencode($item['name']); ?>]"
                                            class="qty-input" value="<?php echo $item['quantity']; ?>" min="1">
                                        <button type="button" class="qty-btn qty-plus" data-name="<?php echo $item['name']; ?>">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td data-label="Total" class="subtotal">$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo urlencode($item['name']); ?>" class="remove-btn" title="Remove item">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Cart Actions -->
                <div class="cart-actions">
                    <div class="coupon-box">
                        <input type="text" name="coupon_code" class="coupon-input" placeholder="Enter coupon code">
                        <button type="submit" name="apply_coupon" class="btn btn-primary">
                            <i class="fas fa-tag"></i> Apply Coupon
                        </button>
                    </div>
                    <div>
                        <button type="submit" name="update_cart" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Update Cart
                        </button>
                        <a href="shop.php" class="btn btn-primary" style="margin-left: 10px;">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Shipping Information -->
                <h2>Shipping Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required
                            value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required
                            value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" required
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Street Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" class="form-control" required
                            value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="address2">Apartment, Suite, etc. (Optional)</label>
                        <input type="text" id="address2" name="address2" class="form-control"
                            value="<?php echo isset($_POST['address2']) ? htmlspecialchars($_POST['address2']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <input type="text" id="city" name="city" class="form-control" required
                            value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="state">State/Province <span class="required">*</span></label>
                            <input type="text" id="state" name="state" class="form-control" required
                                value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="zip">ZIP/Postal Code <span class="required">*</span></label>
                            <input type="text" id="zip" name="zip" class="form-control" required
                                value="<?php echo isset($_POST['zip']) ? htmlspecialchars($_POST['zip']) : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="country">Country <span class="required">*</span></label>
                        <select id="country" name="country" class="form-control" required>
                            <option value="">Select Country</option>
                            <option value="US" <?php echo (isset($_POST['country']) && $_POST['country'] == 'US') ? 'selected' : ''; ?>>United States</option>
                            <option value="CA" <?php echo (isset($_POST['country']) && $_POST['country'] == 'CA') ? 'selected' : ''; ?>>Canada</option>
                            <option value="UK" <?php echo (isset($_POST['country']) && $_POST['country'] == 'UK') ? 'selected' : ''; ?>>United Kingdom</option>
                            <option value="AU" <?php echo (isset($_POST['country']) && $_POST['country'] == 'AU') ? 'selected' : ''; ?>>Australia</option>
                            <option value="IN" <?php echo (isset($_POST['country']) && $_POST['country'] == 'IN') ? 'selected' : ''; ?>>India</option>
                        </select>
                    </div>
                </div>

                <!-- Payment Method -->
               <h2>Payment Method</h2>
                <div class="payment-methods">
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Credit Card" checked>
                        <div class="icon"><i class="far fa-credit-card"></i></div>
                        <div class="details">
                            <h4>Credit Card</h4>
                            <p>Pay with Visa, Mastercard, American Express, etc.</p>
                        </div>
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="PayPal">
                        <div class="icon"><i class="fab fa-paypal"></i></div>
                        <div class="details">
                            <h4>PayPal</h4>
                            <p>Pay with your PayPal account</p>
                        </div>
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Bank Transfer">
                        <div class="icon"><i class="fas fa-university"></i></div>
                        <div class="details">
                            <h4>Bank Transfer</h4>
                            <p>Direct bank transfer</p>
                        </div>
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Cash on Delivery">
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="details">
                            <h4>Cash on Delivery</h4>
                            <p>Pay with cash when you receive your order</p>
                        </div>
                    </label>
                </div>

                <!-- Cart Totals -->
                <div class="cart-totals">
                    <h2>Cart Totals</h2>
                    <table class="totals-table">
                        <tr>
                            <th>Subtotal</th>
                            <td id="grand-total">$<?php echo number_format($grand_total, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Shipping</th>
                            <td>Free Shipping</td>
                        </tr>
                        <tr class="order-total">
                            <th>Total</th>
                            <td id="order-total">$<?php echo number_format($grand_total, 2); ?></td>
                        </tr>
                    </table>
                    <button type="submit" name="buy_now" class="btn btn-success checkout-btn">
                        <i class="fas fa-shopping-bag"></i> Proceed to Checkout
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Empty Cart Message -->
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="shop.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity controls functionality
            document.querySelectorAll('.qty-plus').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentNode.querySelector('.qty-input');
                    input.value = parseInt(input.value) + 1;
                    updatePrice(this);
                });
            });

            document.querySelectorAll('.qty-minus').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentNode.querySelector('.qty-input');
                    if (parseInt(input.value) > 1) {
                        input.value = parseInt(input.value) - 1;
                        updatePrice(this);
                    }
                });
            });

            // Input field changes
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value < 1) this.value = 1;
                    updatePrice(this);
                });
            });

            // Remove button confirmation
            document.querySelectorAll('.remove-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to remove this item from your cart?')) {
                        e.preventDefault();
                    }
                });
            });

            // Form validation
            document.querySelector('#checkout-form')?.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#e53e3e';
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields marked with *');
                }
            });

            // Update price function
            function updatePrice(element) {
                const row = element.closest('tr');
                const price = parseFloat(row.querySelector('.price').textContent.replace('$', ''));
                const quantity = parseInt(row.querySelector('.qty-input').value);
                const subtotal = price * quantity;

                row.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
                updateGrandTotal();
            }

            // Update grand total
            function updateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.subtotal').forEach(subtotal => {
                    grandTotal += parseFloat(subtotal.textContent.replace('$', ''));
                });

                document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
                document.getElementById('order-total').textContent = '$' + grandTotal.toFixed(2);
            }
        });
    </script>
</body>
</html>