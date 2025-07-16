<?php
session_start();
include("../connection.php");
// Verify admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get current admin details from database
$admin_id = $_SESSION['user_id'];
$query = "SELECT * FROM datamanage WHERE id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

// Update session with latest data
$_SESSION['FirstName'] = $admin['FirstName'];
$_SESSION['user_email'] = $admin['EmailAddress'];
$_SESSION['profile_pic'] = $admin['profile_pic'] ?? 'assets/images/user.png';

// Fetch total doctors
$doctorResult = $connection->query("SELECT COUNT(*) AS totalDoctors FROM doctors");
$doctorData = $doctorResult->fetch_assoc();
$totalDoctors = $doctorData['totalDoctors'];

// Fetch total customers
$customerResult = $connection->query("SELECT COUNT(*) AS totalCustomers FROM messages");
$customerData = $customerResult->fetch_assoc();
$totalCustomers = $customerData['totalCustomers'];

// Fetch pending orders
$orderResult = $connection->query("SELECT COUNT(*) AS pendingOrders FROM orders ");
$orderData = $orderResult->fetch_assoc();
$pendingOrders = $orderData['pendingOrders'];

// Fetch total patients
$patientResult = $connection->query("SELECT COUNT(*) AS totalPatients FROM appointments");
$patientData = $patientResult->fetch_assoc();
$totalPatients = $patientData['totalPatients'];
?>



<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
     content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>ADMIN</title>
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/plugins/charts-c3/c3.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/theme1.css" />
    <style>
        .profile-display {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            z-index: 1050;
        }

        .profile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .profile-display.active,
        .profile-overlay.active {
            display: block;
        }
    </style>
</head>

<body class="font-montserrat">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
        </div>
    </div>

    <!-- Profile Display Modal -->
    <div class="profile-overlay" id="profileOverlay"></div>
    <div class="profile-display" id="profileDisplay">
        <div class="text-right">
            <button class="btn btn-sm btn-light" onclick="closeProfile()"><i class="fa fa-times"></i></button>
        </div>
        <div class="text-center mb-3">
            <img src="<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" class="rounded-circle" width="100" alt="Profile">
        </div>
        <div class="text-center">
            <h4><?php echo htmlspecialchars($_SESSION['FirstName'])  ?></h4>
            <p class="text-muted"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <p class="text-muted">Administrator</p>
        </div>
        <hr>
        <div class="text-center">
            <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <div id="main_content">
        <div id="header_top" class="header_top">
            <div class="container">
                <div class="hleft">
                    <a class="header-brand"><i class="fa-solid fa-stethoscope"></i></a>
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="nav-link user_btn">
                            <img class="avatar" src="<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" alt="User" data-toggle="tooltip" data-placement="right" title="User Menu" />
                        </a>
                    </div>
                </div>
                <div class="hright">
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="nav-link icon menu_toggle"><i class="fa fa-align-left"></i></a>
                    </div>
                </div>
            </div>
        </div>




        <div class="user_div">
            <h5 class="brand-name mb-4">Care<a href="javascript:void(0)" class="user_btn"><i class="fa-solid fa-stethoscope"></i></a></h5>
            <div class="card-body">
                <a href="profile.html"><img class="card-profile-img" src="assets/images/user.png" alt=""></a>
                <h6 class="mb-0">
                    <?php echo htmlspecialchars($_SESSION['FirstName'] ?? 'Admin'); ?>
                </h6>

                <span>
                    <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@example.com'); ?>
                </span>

                <div class="d-flex align-items-baseline mt-3">
                    <h3 class="mb-0 mr-2">9.8</h3>
                    <p class="mb-0">
                        <span class="text-success">1.6% <i class="fa fa-arrow-up"></i></span>
                    </p>
                </div>
            </div>
            <div class="progress progress-xs">
                <div class="progress-bar" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-orange" role="progressbar" style="width: 5%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-indigo" role="progressbar" style="width: 13%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <h6 class="text-uppercase font-10 mt-1">Performance Score</h6>
            <hr>
            <p>Activity</p>
            <ul class="new_timeline">
                <li>
                    <div class="bullet pink"></div>
                    <div class="time">11:00am</div>
                    <div class="desc">
                        <h3>Attendance</h3>
                        <h4>Computer Class</h4>
                    </div>
                </li>
                <li>
                    <div class="bullet pink"></div>
                    <div class="time">11:30am</div>
                    <div class="desc">
                        <h3>Added an interest</h3>
                        <h4>“Volunteer Activities”</h4>
                    </div>
                </li>
                <li>
                    <div class="bullet green"></div>
                    <div class="time">12:00pm</div>
                    <div class="desc">
                        <h3>Developer Team</h3>
                        <h4>Hangouts</h4>
                        <ul class="list-unstyled team-info margin-0 p-t-5">
                            <li><img src="assets/images/user.png" alt="Avatar"></li>
                            <li><img src="assets/images/xs/avatar2.jpg" alt="Avatar"></li>
                            <li><img src="assets/images/xs/avatar3.jpg" alt="Avatar"></li>
                            <li><img src="assets/images/xs/avatar4.jpg" alt="Avatar"></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="bullet green"></div>
                    <div class="time">2:00pm</div>
                    <div class="desc">
                        <h3>Responded to need</h3>
                        <a href="javascript:void(0)">“In-Kind Opportunity”</a>
                    </div>
                </li>
                <li>
                    <div class="bullet orange"></div>
                    <div class="time">1:30pm</div>
                    <div class="desc">
                        <h3>Lunch Break</h3>
                    </div>
                </li>
                <li>
                    <div class="bullet green"></div>
                    <div class="time">2:38pm</div>
                    <div class="desc">
                        <h3>Finish</h3>
                        <h4>Go to Home</h4>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div id="left-sidebar" class="sidebar ">
        <h5 class="brand-name">Care <a href="javascript:void(0)" class="menu_option float-right"><i class="fa-solid fa-stethoscope"></i><a></h5>
        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul class="metismenu">
                <li class="g_heading">Project</li>
                <li class="active"><a href="index.php"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>

                <li class="g_heading">Management</li>
                <li><a href="managedoc.php"><i class="fa fa-user-md"></i><span>Manage Doctors</span></a></li>
                <li><a href="manage_patients.php"><i class="fa fa-procedures"></i><span>Manage Patients</span></a></li>
                <li><a href="manage_cities.php"><i class="fa fa-city"></i><span>Manage Cities</span></a></li>

                <li class="g_heading">Orders</li>
                <li><a href="admin_orders.php"><i class="fa fa-edit"></i><span>Orders Details</span></a></li>

                <li class="g_heading">Support</li>
                <li> <a href="contact.php"><i class="fa-regular fa-message"></i><span>Contacts</span></a></li>
                <li><a href="javascript:void(0)"><i class="fa fa-support"></i><span>Need Help?</span></a></li>
                <li><a href="../logout.php"><i class="fa fa-sign-out"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </div>

    <div class="page">
        <div id="page_top" class="section-body top_dark">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="left">
                        <a href="javascript:void(0)" class="icon menu_toggle mr-3"><i class="fa  fa-align-left"></i></a>
                        <h1 class="page-title">Dashboard</h1>
                    </div>
                    <div class="right">
                        <div class="notification d-flex">

                            <div class="dropdown d-flex">
                                <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2" data-toggle="dropdown"><i class="fa fa-envelope"></i><span class="badge badge-success nav-unread"></span></a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <ul class="right_chat list-unstyled w350 p-0">
                                        <li class="online">
                                            <a href="javascript:void(0);" class="media">
                                                <img class="media-object" src="assets/images/xs/avatar4.jpg" alt="">
                                                <div class="media-body">
                                                    <span class="name">Donald Gardner</span>
                                                    <div class="message">It is a long established fact that a reader</div>
                                                    <small>11 mins ago</small>
                                                    <span class="badge badge-outline status"></span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="online">
                                            <a href="javascript:void(0);" class="media">
                                                <img class="media-object " src="assets/images/xs/avatar5.jpg" alt="">
                                                <div class="media-body">
                                                    <span class="name">Wendy Keen</span>
                                                    <div class="message">There are many variations of passages of Lorem Ipsum</div>
                                                    <small>18 mins ago</small>
                                                    <span class="badge badge-outline status"></span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="offline">
                                            <a href="javascript:void(0);" class="media">
                                                <img class="media-object " src="assets/images/xs/avatar2.jpg" alt="">
                                                <div class="media-body">
                                                    <span class="name">Matt Rosales</span>
                                                    <div class="message">Contrary to popular belief, Lorem Ipsum is not simply</div>
                                                    <small>27 mins ago</small>
                                                    <span class="badge badge-outline status"></span>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="online">
                                            <a href="javascript:void(0);" class="media">
                                                <img class="media-object " src="assets/images/xs/avatar3.jpg" alt="">
                                                <div class="media-body">
                                                    <span class="name">Phillip Smith</span>
                                                    <div class="message">It has roots in a piece of classical Latin literature from 45 BC</div>
                                                    <small>33 mins ago</small>
                                                    <span class="badge badge-outline status"></span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0)" class="dropdown-item text-center text-muted-dark readall">Mark all as read</a>
                                </div>
                            </div>
                            <div class="dropdown d-flex">
                                <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2" data-toggle="dropdown"><i class="fa fa-bell"></i><span class="badge badge-primary nav-unread"></span></a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <ul class="list-unstyled feeds_widget">
                                        <li>
                                            <div class="feeds-left"><i class="fa fa-check"></i></div>
                                            <div class="feeds-body">
                                                <h4 class="title text-danger">Issue Fixed <small class="float-right text-muted">11:05</small></h4>
                                                <small>WE have fix all Design bug with Responsive</small>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="feeds-left"><i class="fa fa-user"></i></div>
                                            <div class="feeds-body">
                                                <h4 class="title">New User <small class="float-right text-muted">10:45</small></h4>
                                                <small>I feel great! Thanks team</small>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="feeds-left"><i class="fa fa-thumbs-o-up"></i></div>
                                            <div class="feeds-body">
                                                <h4 class="title">7 New Feedback <small class="float-right text-muted">Today</small></h4>
                                                <small>It will give a smart finishing to your site</small>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="feeds-left"><i class="fa fa-question-circle"></i></div>
                                            <div class="feeds-body">
                                                <h4 class="title text-warning">Server Warning <small class="float-right text-muted">10:50</small></h4>
                                                <small>Your connection is not private</small>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="feeds-left"><i class="fa fa-shopping-cart"></i></div>
                                            <div class="feeds-body">
                                                <h4 class="title">7 New Orders <small class="float-right text-muted">11:35</small></h4>
                                                <small>You received a new oder from Tina.</small>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0)" class="dropdown-item text-center text-muted-dark readall">Mark all as read</a>
                                </div>
                            </div>
                            <div class="dropdown d-flex">
                                <a class="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-2" data-toggle="dropdown">
                                    <?php if (isset($_SESSION['profile_pic'])) { ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" class="avatar" alt="User">
                                    <?php } else { ?>
                                        <i class="fa fa-user"></i>
                                    <?php } ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <div class="dropdown-header">
                                        <h6 class="text-overflow m-0">Welcome <?php echo htmlspecialchars($_SESSION['FirstName']); ?>!</h6>
                                        <div class="text-muted small"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="showProfile()"><i class="fa-solid fa-user-pen"></i></i>
                                        View Profile</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <h4>Welcome <?php echo isset($_SESSION['FirstName']) ? htmlspecialchars($_SESSION['FirstName']) : 'Admin'; ?>!</h4> <small class="text-muted">Measure How Fast You're Growing Monthly Recurring Revenue. <a href="#">Learn More</a></small>
                        </div>
                    </div>
                </div>


                <!-- Quick Stats Row -->
                <div class="container my-4">
                    <div class="row clearfix">
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Doctors</h6>
                                            <h4 class="mt-2"><?php echo $totalDoctors; ?></h4>
                                            <span class="text-success">+3.5% <i class="fa fa-arrow-up"></i></span>
                                        </div>
                                        <div class="bg-primary text-white rounded p-3">
                                            <i class="fa fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Customers</h6>
                                            <h4 class="mt-2"><?php echo $totalCustomers; ?></h4>
                                            <span class="text-success">+8.2% <i class="fa fa-arrow-up"></i></span>
                                        </div>
                                        <div class="bg-success text-white rounded p-3">
                                            <i class="fa-solid fa-people-arrows"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Pending Orders</h6>
                                            <h4 class="mt-2"><?php echo $pendingOrders; ?></h4>
                                            <span class="text-danger">-1.2% <i class="fa fa-arrow-down"></i></span>
                                        </div>
                                        <div class="bg-warning text-white rounded p-3">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Patients</h6>
                                            <h4 class="mt-2"><?php echo $totalPatients; ?></h4>
                                            <span class="text-success">+2.1% <i class="fa fa-arrow-up"></i></span>
                                        </div>
                                        <div class="bg-info text-white rounded p-3">
                                            <i class="fa-solid fa-bed-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Management and Content Sections -->
                <div class="row clearfix mt-4">
                    <!-- Management Section (Full Width Below) -->
                    <div class="col-lg-12 col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Management</h3>
                                <div class="card-options">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <!-- Doctor Management -->
                                    <a href="managedoc.php" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Doctor Management</h5>
                                            <small><i class="fa fa-chevron-right"></i></small>
                                        </div>
                                        <p class="mb-1">Manage all system users and permissions</p>
                                    </a>
                                    <!-- Patient Management -->
                                    <a href="manage_patients.php" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Patient Management</h5>
                                            <small><i class="fa fa-chevron-right"></i></small>
                                        </div>
                                        <p class="mb-1">Manage patient records and information</p>
                                    </a>
                                    <!-- Cities Management -->
                                    <a href="manage_cities.php" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Cities Management</h5>
                                            <small><i class="fa fa-chevron-right"></i></small>
                                        </div>
                                        <p class="mb-1">Manage available cities and locations</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders and Clients Section Side-by-Side -->
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Orders</h3>
                                <div class="card-options">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="admin_orders.php" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Orders Detail</h5>
                                            <small><i class="fa fa-chevron-right"></i></small>
                                        </div>
                                        <p class="mb-1">See your latest order!</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Clients Messages</h3>
                                <div class="card-options">
                                    <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="contact.php" class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">View Messages</h5>
                                            <small><i class="fa fa-chevron-right"></i></small>
                                        </div>
                                        <p class="mb-1">See latest client messages!</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="footer mt-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <p class="mb-0">Copyright © 2025 Your Care group. All rights reserved.</p>
                            </div>
                            <div class="col-md-6 col-sm-12 text-md-right">
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item"><a href="javascript:void(0)">Documentation</a></li>
                                    <li class="list-inline-item"><a href="javascript:void(0)">FAQ</a></li>
                                    <li class="list-inline-item"><a href="javascript:void(0)">Support</a></li>
                                    <li class="list-inline-item"><a href="javascript:void(0)" class="btn btn-outline-primary btn-sm">Upgrade Plan</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>


        <script src="assets/bundles/lib.vendor.bundle.js"></script>

        <script src="assets/bundles/apexcharts.bundle.js"></script>
        <script src="assets/bundles/counterup.bundle.js"></script>
        <script src="assets/bundles/knobjs.bundle.js"></script>
        <script src="assets/bundles/c3.bundle.js"></script>

        <script src="assets/js/core.js"></script>
        <script src="assets/js/page/project-index.js"></script>
        <script>
            // Force reload if user presses back after logout
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                    window.location.reload();
                }
            });
        </script>

        <script>
            // Profile display functions
            function showProfile() {
                document.getElementById('profileOverlay').classList.add('active');
                document.getElementById('profileDisplay').classList.add('active');
            }

            function closeProfile() {
                document.getElementById('profileOverlay').classList.remove('active');
                document.getElementById('profileDisplay').classList.remove('active');
            }

            // Close when clicking outside
            document.getElementById('profileOverlay').addEventListener('click', closeProfile);
        </script>
</body>

</html>