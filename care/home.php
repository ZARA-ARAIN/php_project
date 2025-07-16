<?php
session_start();
include("connection.php");

if (isset($_POST['submit'])) {
    // Validate and sanitize inputs
    if (!isset($_SESSION['user_email'])) {
        header("Location: home.php?error_form=" . urlencode("Please login first to book an appointment"));
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date = trim($_POST['Date1'] ?? '');
    $doctor = trim($_POST['doctor'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $note = trim($_POST['note'] ?? '');

    $errors = [];

    if (count($errors) > 0) {
        $errorMessage = implode(", ", $errors);
        header("Location: home.php?error=" . urlencode($errorMessage));
        exit();
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $connection->prepare("INSERT INTO appointments (`Name`, `Phone`, `Email`, `Date1`, `Note`, `city`, `Doctor`) VALUES (?, ?, ?, ?, ?,?,?)");
        $stmt->bind_param("sssssss", $name, $phone, $email, $date, $note, $city, $doctor);

        if ($stmt->execute()) {
            header("Location: home.php?success=1");
            exit();
        } else {
            header("Location: home.php?error=" . urlencode("Database error: " . $conn->error));
            exit();
        }
    }
}


$doctorName = '';

if (isset($_GET['doctor_id'])) {
    $doctor_id = intval($_GET['doctor_id']);
    $query = "SELECT name FROM doctors WHERE id = $doctor_id";
    $result = mysqli_query($connection, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $doctorName = $row['name'];
    }
}


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="shortcut icon" type="image/x-icon" href="/project/care/images/favicon.png" />
    <link rel="stylesheet" href="/project/care/plugin/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/project/care/plugin/bootstrap/css/bootstrap-theme.css">
    <link rel="stylesheet" href="/project/care/fonts/poppins/poppins.css">
    <link rel="stylesheet" href="/project/care/plugin/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/project/care/plugin/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="/project/care/plugin/process-bar/tox-progress.css">
    <link rel="stylesheet" href="/project/care/plugin/owl-carouse/owl.carousel.min.css">
    <link rel="stylesheet" href="/project/care/plugin/owl-carouse/owl.theme.default.min.css">
    <link rel="stylesheet" href="/project/care/plugin/animsition/css/animate.css">
    <link rel="stylesheet" href="/project/care/plugin/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="/project/care/plugin/mediaelement/mediaelementplayer.css">
    <link rel="stylesheet" href="/project/care/plugin/datetimepicker/bootstrap-datepicker3.css">
    <link rel="stylesheet" href="/project/care/plugin/datetimepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/project/care/plugin/lightgallery/lightgallery.css">
    <link rel="stylesheet" href="/project/care/css/style.css">

    <style>
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 3.25rem;
            margin-left: 10px;
            display: none;
        }


        .error-message1 {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            margin-left: 55px;
            display: none;
        }

        .wrapper-logo {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 15px;
        }

        .user-icon {
            margin-left: -70px;
        }

        .user-icon i {
            font-size: 50px;
            color: rgb(29, 138, 255);
            transition: 0.3s;
        }

        .user-icon:hover i {
            color: #007bff;
        }

        .logo-default img {
            max-width: 180px;
            height: auto;
            width: auto;
            transition: width 0.3s ease;
        }

        @media (max-width: 767px) {
            .logo-default img {
                max-width: 150px;
            }
        }

        /* Adjustments for the search and appointment buttons container */
        .uni-search-appointment {
            display: flex;
            justify-content: flex-end;
            /* Aligns items to the right */
            align-items: center;
        }

        /* Styling for the search button */
        .un-btn-search {
            margin-right: 20px;
            /* Add some space between the search button and the user icon */
            cursor: pointer;
        }

        /* Styling for the user dropdown */
        .user-dropdown {
            position: relative;
            display: flex;
            align-items: center;
        }

        /* User icon styling */
        .user-icon {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #fff;
            gap: 10px;
        }

        /* Ensure dropdown menu appears correctly */
        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 160px;
            z-index: 10;
        }

        /* Show dropdown when user hovers over the icon */
        .user-dropdown:hover .user-dropdown-menu {
            display: block;
        }

        /* Styling for the dropdown items */
        .user-dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }

        .user-dropdown-menu a:hover {
            background-color: rgb(98, 127, 255);
            color: white;
        }


        html {
            scroll-behavior: smooth;
        }

        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-icon {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #333;
            text-decoration: none;
        }

        .user-name {
            margin-left: 5px;
            display: inline-block;
        }
    </style>


</head>

<body>

    <!--load page-->
    <div class="load-page">
        <div class="sk-wave">
            <div class="sk-rect sk-rect1"></div>
            <div class="sk-rect sk-rect2"></div>
            <div class="sk-rect sk-rect3"></div>
            <div class="sk-rect sk-rect4"></div>
            <div class="sk-rect sk-rect5"></div>
        </div>
    </div>

    <!-- Mobile nav -->
    <nav class="visible-sm visible-xs mobile-menu-container mobile-nav">
        <div class="menu-mobile-nav navbar-toggle">
            <span class="icon-bar"><i class="fa fa-bars" aria-hidden="true"></i></span>
        </div>
        <div id="cssmenu" class="animated">
            <div class="uni-icons-close"><i class="fa fa-times" aria-hidden="true"></i></div>
            <ul class="nav navbar-nav animated">
                <li><a href="home.php">Home</a></li>
                <li class="has-sub"><a href='#'>Information</a>
                    <ul>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </li>
                <li><a href="doctors.php">Doctors</a></li>
                <li><a href="departments.php">Departments</a></li>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </nav>
    <!-- End mobile menu -->

    <div class="uni-home-1">
        <div id="wrapper-container" class="site-wrapper-container">
            <header>
                <div class="uni-medicare-header sticky-menu">
                    <div class="container">
                        <div class="uni-medicare-header-main">
                            <div class="row">
                                <!-- START col-md-2 for Logo -->
                                <div class="col-md-2">
                                    <div class="wrapper-logo d-flex justify-content-start align-items-center">
                                        <a class="logo-default" href="index.php">
                                            <img src="images/logo.png" alt="" class="img-responsive">
                                        </a>
                                    </div>
                                </div>
                                <!-- END col-md-2 for Logo -->

                                <!-- START col-md-10 for Menu and User Icon -->
                                <div class="col-md-10">
                                    <div class="uni-main-menu d-flex justify-content-between">
                                        <!-- Main Navigation Menu -->
                                        <nav class="main-navigation uni-menu-text">
                                            <div class="cssmenu">
                                                <ul>
                                                    <li><a href="home.php">Home</a></li>
                                                    <li class="has-sub">
                                                        <a href="#">Information</a>
                                                        <ul>
                                                            <li><a href="about.php">About</a></li>
                                                            <li><a href="contact.php">Contact</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><a href="doctors.php">Doctors</a></li>
                                                    <li><a href="departments.php">Departments</a></li>
                                                    <li><a href="blog.php">Blog</a></li>
                                                    <li><a href="shop.php">Shop</a></li>
                                                    <?php if (isset($_SESSION['user_email']) && $_SESSION['user_role'] === 'admin'): ?>
                                                        <li><a href="admin_panel/index.php">Admin</a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </nav>

                                        <!-- User Icon and Dropdown -->

                                    </div>

                                    <!-- Search and Appointment Button -->
                                    <div class="uni-search-appointment">

                                        <div class="user-dropdown">
                                            <?php if (isset($_SESSION['user_email'])): ?>
                                                <!-- Show user name and dropdown when logged in -->
                                                <a class="user-icon">
                                                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                    <span class="user-name">
                                                        <?php
                                                        // Display first name if available, otherwise display email
                                                        if (isset($_SESSION['FirstName'])) {
                                                            echo "<strong>" . htmlspecialchars($_SESSION['FirstName']) . "</strong>";
                                                        } else {
                                                            // Extract first part of email before @
                                                            $emailParts = explode('@', $_SESSION['FirstName']);
                                                            echo "<strong>" . htmlspecialchars($emailParts[0]) . "</strong>";
                                                        }
                                                        ?>
                                                    </span>
                                                </a>
                                                <div class="user-dropdown-menu">
                                                    <a href="appointment.php">My Appointments</a>
                                                    <a href="logout.php">Logout</a>
                                                </div>
                                            <?php else: ?>
                                                <!-- Show sign in button when not logged in -->
                                                <a href="login.php" class="user-icon">
                                                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                </a>
                                                <div class="user-dropdown-menu">
                                                    <a href="login.php">Sign In</a>
                                                    <a href="registers.php">Register</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                                <!-- END col-md-10 for Menu and User Icon -->
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        </div>
    </div>



    <div id="main-content" class="site-main-content">
        <section class="site-content-area">

            <!--BANNER-->
            <div class="uni-banner">
                <div class="uni-owl-one-item owl-carousel owl-theme">
                    <div class="item">
                        <div class="uni-banner-img uni-background-5"></div>
                        <div class="content animated" data-animation="flipInX" data-delay="0.9s">
                            <div class="container">
                                <div class="caption">
                                    <h1>Let us protect your health</h1>
                                    <p>
                                        Your health matters with committed care, we help you stay safe and thrive every day.
                                        <br>
                                        Together, we ensure your health stays in safe hands.
                                    </p>
                                    <a href="services.php">our services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="uni-banner-img uni-background-6"></div>
                        <div class="content animated" data-animation="flipInX" data-delay="0.9s">
                            <div class="container">
                                <div class="caption">
                                    <h1>Let us protect your health</h1>
                                    <p>
                                        From prevention to protection, we stand by you to ensure your health stays in trusted hands.
                                        <br>
                                        Together, we ensure your health stays in safe hands.
                                    </p>
                                    <a href="services.php">our services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="uni-banner-img uni-background-7"></div>
                        <div class="content animated" data-animation="flipInX" data-delay="0.9s">
                            <div class="container">
                                <div class="caption">
                                    <h1>Let us protect your health</h1>
                                    <p>
                                        We protect what matters most — your health, with care and dedication.
                                        <br>
                                        Together, we ensure your health stays in safe hands.
                                    </p>
                                    <a href="services.php">our services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--OPENING HOURS AND BOOK APPOINTMENT-->
            <div class="uni-home-opening-book">
                <div class="container">
                    <div class="uni-home-opening-book-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="uni-services-opinging-hours">
                                    <div class="uni-services-opinging-hours-title">
                                        <div class="icon">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        </div>
                                        <h4>opening hours</h4>
                                    </div>
                                    <div class="uni-services-opinging-hours-content">
                                        <table class="table">
                                            <tr>
                                                <td>monday</td>
                                                <td>8:00 - 17:00</td>
                                            </tr>
                                            <tr>
                                                <td>tuesday</td>
                                                <td>8:00 - 17:00</td>
                                            </tr>
                                            <tr>
                                                <td>wednesday</td>
                                                <td>8:00 - 17:00</td>
                                            </tr>
                                            <tr>
                                                <td>thursday</td>
                                                <td>8:00 - 17:00</td>
                                            </tr>
                                            <tr>
                                                <td>friday</td>
                                                <td>8:00 - 17:00</td>
                                            </tr>
                                            <tr>
                                                <td>sunday</td>
                                                <td>8:00 - 17:00</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8" id="book-appointment" style="padding-top: 100px; margin-top: -80px;">
                                <div class="uni-single-department-appointment-form">
                                    <!-- view appointment work -->
                                    <?php
                                    include('connection.php');

                                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                                        // Get the form data
                                        $name = $_POST[`Name`];
                                        $email = $_POST[`Email`];
                                        $phone = $_POST[`Phone`];
                                        $city = $_POST[`city`];
                                        $doctor = $_POST['Doctor'];
                                        $Date1 = $_POST['Date1']; // The date of the appointment
                                        $note = $_POST['Note'];

                                        // Insert the data into the database
                                        $stmt = $connection->prepare("INSERT INTO appointments (Name, Email, Phone, city, Doctor, Date1, Note) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                        $stmt->bind_param("sssssss", $name, $email, $phone, $city, $doctor, $Date1, $note);

                                        if ($stmt->execute()) {
                                            // Redirect to appointments page after successful booking
                                            header("Location: appointments.php");
                                            exit();
                                        } else {
                                            echo "Error: " . $stmt->error;
                                        }
                                    }
                                    ?>


                                    <form id="booking" action="" method="POST" novalidate>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="input-group form-group">
                                                    <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="your name" required>
                                                    <div class="error-message" id="nameError"></div>
                                                </div>

                                                <div class="input-group form-group">
                                                    <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="phone number" required>
                                                    <div class="error-message" id="phoneError"></div>
                                                </div>

                                                <div class="input-group form-group">
                                                    <span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                                                    <div class="error-message" id="emailError"></div>
                                                </div>

                                                <div class="input-group form-group">
                                                    <span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                                                    <input type="text" class="form-control" id="city" name="city" placeholder="city" required>
                                                    <div class="error-message" id="cityError"></div>
                                                </div>

                                                <div class="input-group form-group">
                                                    <span class="input-group-addon"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                                                    <input type="text" class="form-control" id="doctor" name="doctor" placeholder="doctor" value="<?php echo htmlspecialchars($doctorName); ?>" required>
                                                    <div class="error-message" id="DoctorError"></div>
                                                </div>

                                                <div class="input-group form-group">
                                                    <div class="input-group date date-check-in" data-date="12-02-2017" data-date-format="mm-dd-yyyy">
                                                        <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                        <input name="Date1" id="Date1" class="form-control" type="date" placeholder="YYYY-MM-DD" required>
                                                    </div>
                                                    <span class="error-message1" id="dateError"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-7">
                                                <div class="uni-home-title">
                                                    <h3>Book appointment</h3>
                                                    <div class="uni-underline" style="margin-right:155px;"></div>
                                                </div>

                                                <div class="input-group form-group">
                                                    <textarea id="message" name="note" class="form-control" placeholder="note"></textarea>
                                                </div>

                                                <button class="vk-btn vk-btn-send" name="submit" type="submit">send</button>
                                            </div>
                                        </div>
                                        <?php if (isset($_GET['error_form'])): ?>
                                            <div class="alert alert-danger">
                                                <?php echo htmlspecialchars($_GET['error_form']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (isset($_GET['success'])) { ?>
                                            <div class="alert alert-success" role="alert">
                                                <strong>🎉 Booking Successful!</strong>
                                            </div>
                                        <?php } ?>

                                        <?php if (isset($_GET['error'])) { ?>
                                            <div class="alert alert-danger" role="alert">
                                                <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                                            </div>
                                        <?php } ?>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!--DEPARTMENT-->
            <div class="uni-hơm-1-department">
                <div class="container">
                    <div class="uni-home-title">
                        <h3>Department</h3>
                        <div class="uni-underline"></div>
                    </div>
                    <div class="uni-shortcode-icon-box-1">
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icon-box-1-default">
                                    <div class="item-icons">
                                        <img src="images/icons_box/icon_1/icon-5.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <h4>cardiology</h4>
                                        <p>Provides diagnosis and treatment for heart-related conditions like heart attacks, arrhythmias, and hypertension.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icon-box-1-default">
                                    <div class="item-icons">
                                        <img src="images/icons_box/icon_1/icon-4.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <h4>Neurology</h4>
                                        <p>Focused on disorders of the nervous system, such as epilepsy, migraines, stroke, and Parkinson’s disease.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icon-box-1-default">
                                    <div class="item-icons">
                                        <img src="images/icons_box/icon_1/icon-3.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <h4>Orthopedics</h4>
                                        <p>Deals with conditions of the bones, joints, ligaments, and muscles including fractures, arthritis, and spine disorders.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icon-box-1-default">
                                    <div class="item-icons">
                                        <img src="images/icons_box/icon_1/icon-2.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <h4>cancer department</h4>
                                        <p>Provides comprehensive cancer care including diagnosis, chemotherapy, radiation therapy, and surgical oncology.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icon-box-1-default">
                                    <div class="item-icons">
                                        <img src="images/icons_box/icon_1/icon-1.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <h4>Ophthalmology</h4>
                                        <p>Dedicated to eye care, offering treatments for vision problems, cataracts, glaucoma, and other eye diseases.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icon-box-1-default">
                                    <div class="item-icons">
                                        <img src="images/icons_box/icon_1/icon.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <h4>Respiratory</h4>
                                        <p>Specializes in lung and breathing issues, including asthma, COPD, pneumonia, and chronic respiratory disorders.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--OUR DOCTOR-->
            <div class="uni-home-1-our-doctor">
                <div class="uni-shortcode-team-2 uni-background-2">
                    <div class="container">

                        <div class="uni-home-title">
                            <h3>Our Doctor</h3>
                            <div class="uni-underline"></div>
                        </div>

                        <div class="uni-owl-four-item owl-carousel owl-theme">
                            <div class="item">
                                <div class="uni-team-default">
                                    <div class="item-img">
                                        <img src="images/team/img.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <div class="col-md-3 col-sm-3 col-xs-3 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-5.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>adam jonson</h4>
                                                <span>Cardiologist</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="uni-team-default">
                                    <div class="item-img">
                                        <img src="images/team/img1.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <div class="col-md-3 col-sm-3 col-xs-3 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-4.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Henrik larssom</h4>
                                                <span>neurologist</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="uni-team-default">
                                    <div class="item-img">
                                        <img src="images/team/img2.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <div class="col-md-3 col-sm-3 col-xs-3 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-3.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>amanda smith</h4>
                                                <span>Ophthalmology doctor</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="uni-team-default">
                                    <div class="item-img">
                                        <img src="images/team/img3.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <div class="col-md-3 col-sm-3 col-xs-3 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-2.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>david martin</h4>
                                                <span>Cancer doctor</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="uni-team-default">
                                    <div class="item-img">
                                        <img src="images/team/img.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <div class="col-md-3 col-sm-3 col-xs-3 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-5.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>adam jonson</h4>
                                                <span>Cardiologist</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="uni-team-default">
                                    <div class="item-img">
                                        <img src="images/team/img1.png" alt="" class="img-responsive">
                                    </div>
                                    <div class="item-caption">
                                        <div class="col-md-3 col-sm-3 col-xs-3 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-4.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-sm-9 col-xs-9 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Henrik larssom</h4>
                                                <span>neurologist</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!--OUR SERVICES-->
            <div class="uni-home-our-services">
                <div class="uni-shortcode-icons-box-5">
                    <div class="container">

                        <div class="uni-home-title">
                            <h3>Our Services</h3>
                            <div class="uni-underline"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icons-box-5-default">
                                    <div class="item-icons-title">
                                        <div class="col-md-4 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-5.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Corneal transplant surgery</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            A corneal transplant replaces damaged or diseased corneal tissue with healthy tissue from a donor, restoring vision and relieving pain.
                                        </p>
                                        <a href="https://en.wikipedia.org/wiki/Corneal_transplantation" class="readmore">Read more</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icons-box-5-default">
                                    <div class="item-icons-title">
                                        <div class="col-md-4 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-4.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Cardiothoracic Surgery</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            This surgical specialty focuses on operations involving the heart, lungs, and chest, often used to treat conditions like heart disease or lung cancer.
                                        </p>
                                        <a href="https://en.wikipedia.org/wiki/Cardiothoracic_surgery" class="readmore">Read more</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icons-box-5-default">
                                    <div class="item-icons-title">
                                        <div class="col-md-4 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-3.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>General health check</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            A routine physical exam helps detect early signs of illness, ensuring ongoing health and preventing future complications through timely intervention.
                                        </p>
                                        <a href="https://en.wikipedia.org/wiki/Health_Check" class="readmore">Read more</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icons-box-5-default">
                                    <div class="item-icons-title">
                                        <div class="col-md-4 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-2.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Diagnosis &amp; <br> treatment of cancer</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            Cancer diagnosis involves imaging and biopsies, followed by treatments like chemotherapy, radiation, or surgery depending on type and stage.
                                        </p>
                                        <a href="https://en.wikipedia.org/wiki/Cancer_treatment" class="readmore">Read more</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icons-box-5-default">
                                    <div class="item-icons-title">
                                        <div class="col-md-4 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon-1.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Treatment of <br> pneumonia</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            Pneumonia is treated with antibiotics, antivirals, or antifungals depending on the cause, along with rest and fluids to support recovery.
                                        </p>
                                        <a href="https://en.wikipedia.org/wiki/Pneumonia" class="readmore">Read more</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="uni-shortcode-icons-box-5-default">
                                    <div class="item-icons-title">
                                        <div class="col-md-4 uni-clear-padding">
                                            <div class="item-icons">
                                                <img src="images/icons_box/icon_4/icon.png" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 uni-clear-padding">
                                            <div class="item-title">
                                                <h4>Treatment of <br> dermatitis</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            Dermatitis is managed with moisturizers, corticosteroid creams, and avoiding allergens or irritants that trigger skin inflammation and itching.
                                        </p>
                                        <a href="https://en.wikipedia.org/wiki/Dermatitis" class="readmore">Read more</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-all-services">
                            <a href="services.php">All services +</a>
                        </div>

                    </div>
                </div>
            </div>

            <!--CUSTOMERS SAY-->
            <div class="uni-home-customers-says">
                <div class="uni-shortcode-testimonials-2 uni-background-3">
                    <div class="container">

                        <div class="uni-home-title">
                            <h3>Customers say</h3>
                            <div class="uni-underline"></div>
                        </div>

                        <div class="uni-owl-two-item owl-carousel owl-theme">
                            <div class="item">
                                <div class="uni-shortcode-testimonials-default">
                                    <div class="item-info">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4">
                                                <div class="item-info-img">
                                                    <img src="images/testimonial/img.png" alt="" class="img-responsive">
                                                </div>
                                            </div>
                                            <div class="col-md-9 col-sm-8">
                                                <div class="item-info-title">
                                                    <h4>Sarah Williams</h4>
                                                    <p class="testimonial_subtitle">Diabetes Patient</p>
                                                </div>
                                                <div class="uni-divider"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            The diabetic management program here changed my life. The staff is professional and caring, and their tech-based tracking made everything easier to manage.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="uni-shortcode-testimonials-default">
                                    <div class="item-info">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4">
                                                <div class="item-info-img">
                                                    <img src="images/testimonial/img1.png" alt="" class="img-responsive">
                                                </div>
                                            </div>
                                            <div class="col-md-9 col-sm-8">
                                                <div class="item-info-title">
                                                    <h4>James Miller</h4>
                                                    <p class="testimonial_subtitle">Orthopedic Recovery</p>
                                                </div>
                                                <div class="uni-divider"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            After a leg fracture, I received world-class orthopedic treatment. The recovery plan was effective and well-supported by modern rehabilitation tools.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="uni-shortcode-testimonials-default">
                                    <div class="item-info">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4">
                                                <div class="item-info-img">
                                                    <img src="images/testimonial/img.png" alt="" class="img-responsive">
                                                </div>
                                            </div>
                                            <div class="col-md-9 col-sm-8">
                                                <div class="item-info-title">
                                                    <h4>Olivia Khan</h4>
                                                    <p class="testimonial_subtitle">Eye Care Patient</p>
                                                </div>
                                                <div class="uni-divider"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            I came for a myopia check-up and was impressed with the accuracy of the eye scans and treatment suggestions. Their smart diagnostic tools are a game-changer.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="item">
                                <div class="uni-shortcode-testimonials-default">
                                    <div class="item-info">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-4">
                                                <div class="item-info-img">
                                                    <img src="images/testimonial/img1.png" alt="" class="img-responsive">
                                                </div>
                                            </div>
                                            <div class="col-md-9 col-sm-8">
                                                <div class="item-info-title">
                                                    <h4>Daniel Smith</h4>
                                                    <p class="testimonial_subtitle">Heart Patient</p>
                                                </div>
                                                <div class="uni-divider"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-caption">
                                        <p>
                                            The cardiology department gave me confidence during a difficult time. Their use of AI-assisted diagnostics helped catch my condition early.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <!--FAQ'S  and LASTEST POST-->
            <div class="uni-home-faq-latest-post">
                <div class="container">
                    <div class="row">
                        <!-- FAQ Section -->
                        <div class="col-md-6">
                            <div class="uni-home-faq">
                                <div class="uni-home-title">
                                    <h3>FAQ's</h3>
                                    <div class="uni-line"></div>
                                </div>
                                <div class="accordion-default">
                                    <div class="accordion-min-plus">
                                        <div class="accordion">
                                            <div class="accordion-item">
                                                <h4 class="accordion-toggle">What are the most common symptoms of diabetes?</h4>
                                                <div class="accordion-content">
                                                    <p>
                                                        Common symptoms include frequent urination, excessive thirst, unexplained weight loss, fatigue, and blurred vision. It's important to get tested if you notice these signs.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h4 class="accordion-toggle">How can I prevent high blood pressure?</h4>
                                                <div class="accordion-content">
                                                    <p>
                                                        Maintain a healthy diet low in sodium, exercise regularly, avoid smoking, limit alcohol, and manage stress. Routine blood pressure checks are also essential.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h4 class="accordion-toggle">What are the early signs of cancer?</h4>
                                                <div class="accordion-content">
                                                    <p>
                                                        Symptoms vary by type, but early signs may include unusual lumps, prolonged cough, changes in bowel habits, unexplained weight loss, and persistent fatigue.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h4 class="accordion-toggle">Can asthma be cured completely?</h4>
                                                <div class="accordion-content">
                                                    <p>
                                                        Asthma cannot be completely cured, but it can be effectively managed with medication, avoiding triggers, and regular medical follow-ups.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h4 class="accordion-toggle">What causes skin conditions like dermatitis?</h4>
                                                <div class="accordion-content">
                                                    <p>
                                                        Dermatitis is usually caused by allergens, irritants, stress, or genetics. Common types include atopic, contact, and seborrheic dermatitis.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h4 class="accordion-toggle">Are viral infections like COVID-19 still a concern?</h4>
                                                <div class="accordion-content">
                                                    <p>
                                                        Yes, though less severe now due to vaccination, COVID-19 and other viral infections still pose a risk, especially to immunocompromised individuals. Stay updated with CDC guidelines.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Latest Posts Section -->
                        <div class="col-md-6">
                            <div class="uni-home-latest-post">
                                <div class="uni-home-title">
                                    <h3>Latest Medical News</h3>
                                    <div class="uni-line"></div>
                                </div>
                                <div class="uni-home-latest-post-body">
                                    <div class="item">
                                        <div class="item-img">
                                            <a href="https://www.bbc.com/news/health-68777812" target="_blank">
                                                <img src="images/home1/lastestpost/img2.png" alt="AI Diagnosing Cancer" class="img-responsive">
                                            </a>
                                        </div>
                                        <div class="item-caption">
                                            <h4><a href="https://www.bbc.com/news/health-68777812" target="_blank">AI used to detect early-stage cancer with 92% accuracy</a></h4>
                                            <span class="time">April 21, 2025</span>
                                        </div>
                                    </div>
                                    <div class="uni-divider"></div>
                                    <div class="item">
                                        <div class="item-img">
                                            <a href="https://www.medicalnewstoday.com/articles/medication-that-repairs-heart-tissue" target="_blank">
                                                <img src="images/home1/lastestpost/img3.png" alt="Heart Tissue Repair Drug" class="img-responsive">
                                            </a>
                                        </div>
                                        <div class="item-caption">
                                            <h4><a href="https://www.medicalnewstoday.com/articles/medication-that-repairs-heart-tissue" target="_blank">New drug shows promise in repairing damaged heart tissue</a></h4>
                                            <span class="time">April 18, 2025</span>
                                        </div>
                                    </div>
                                    <div class="uni-divider"></div>
                                    <div class="item">
                                        <div class="item-img">
                                            <a href="https://www.who.int/news-room/feature-stories/detail/world-malaria-day-2025" target="_blank">
                                                <img src="images/home1/lastestpost/img4.png" alt="Malaria Vaccine Update" class="img-responsive">
                                            </a>
                                        </div>
                                        <div class="item-caption">
                                            <h4><a href="https://www.who.int/news-room/feature-stories/detail/world-malaria-day-2025" target="_blank">WHO approves second-generation malaria vaccine</a></h4>
                                            <span class="time">April 25, 2025</span>
                                        </div>
                                    </div>
                                    <div class="uni-divider"></div>
                                    <div class="item">
                                        <div class="item-img">
                                            <a href="https://www.nature.com/articles/d41586-025-01234" target="_blank">
                                                <img src="images/home1/lastestpost/img5.png" alt="Gene Therapy Breakthrough" class="img-responsive">
                                            </a>
                                        </div>
                                        <div class="item-caption">
                                            <h4><a href="https://www.nature.com/articles/d41586-025-01234" target="_blank">Gene therapy breakthrough offers new hope for rare diseases</a></h4>
                                            <span class="time">April 10, 2025</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Right Column -->
                    </div>
                </div>
            </div>


            <!--MAP-->
            <div class="uni-home-map">
                <div class="uni-about-map">
                    <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2520.355677596112!2d-0.13052618407551403!3d50.82457546821709!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4875859878db2cc7%3A0xff129250121f260d!2s45+Queen's+Park+Rd%2C+Brighton+BN2+0GJ%2C+V%C6%B0%C6%A1ng+Qu%E1%BB%91c+Anh!5e0!3m2!1svi!2s!4v1514436176997" height="700" style="border:0"></iframe>
                </div>
            </div>


        </section>
    </div>

    <footer class="site-footer footer-default">
        <div class="footer-main-content">
            <div class="container">
                <div class="row">
                    <div class="footer-main-content-elements">
                        <div class="footer-main-content-element col-md-3 col-sm-6">
                            <aside class="widget">
                                <div class="widget-title uni-uppercase"><a href="#"><img src="images/logowhite.png" alt="" class="img-responsive"></a></div>
                                <div class="widget-content">
                                    <p>
                                        MediCare Group is a digital healthcare service that helps people book doctor appointments online quickly and easily. We connect patients with verified medical professionals through a secure and user-friendly platform.
                                    </p>

                                    <div class="uni-info-contact">
                                        <ul>
                                            <li> <i class="fa fa-map-marker" aria-hidden="true"></i> 45 Queen's Park Rd, Brighton, UK</li>
                                            <li><i class="fa fa-phone" aria-hidden="true"></i> (094) 123 4567 - (094) 123 4568</li>
                                            <li><i class="fa fa-envelope-o" aria-hidden="true"></i> medicareplus@domain.com</li>
                                        </ul>
                                    </div>
                                </div>
                            </aside>
                        </div>
                        <div class="footer-main-content-element col-md-3 col-sm-6">
                            <aside class="widget">
                                <h3 class="widget-title uni-uppercase">quick links</h3>
                                <div class="widget-content">
                                    <div class="uni-quick-link">
                                        <ul>
                                            <li><a href="home.php"><span>+</span> Home</a></li>
                                            <li><a href="about.php"><span>+</span> about</a></li>
                                            <li><a href="services.php"><span>+</span> services</a></li>
                                            <li><a href="home.php#book-appointment"><span>+</span> timetable</a></li>
                                            <li><a href="blog.php"><span>+</span> blog</a></li>
                                            <li><a href="contact.php"><span>+</span> contact</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </aside>
                        </div>
                        <div class="footer-main-content-element col-md-3 col-sm-6">
                            <aside class="widget">
                                <h3 class="widget-title uni-uppercase">latest posts</h3>
                                <div class="widget-content">
                                    <div class="uni-footer-latest-post">
                                        <ul>
                                            <li>
                                                <h4><a href="https://www.bbc.com/news/health-68777812" target="_blank">AI used to detect early-stage cancer with 92% accuracy</a></h4>
                                                <span class="time">April 21, 2025</span>
                                            </li>
                                            <li>
                                                <h4><a href="https://www.medicalnewstoday.com/articles/medication-that-repairs-heart-tissue" target="_blank">New drug shows promise in repairing damaged heart tissue</a></h4>
                                                <span class="time">April 18, 2025</span>
                                            </li>
                                            <li>
                                                <h4><a href="https://www.who.int/news-room/feature-stories/detail/world-malaria-day-2025" target="_blank">WHO approves second-generation malaria vaccine</a></h4>
                                                <span class="time">April 25, 2025</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </aside>
                        </div>
                        <div class="footer-main-content-element col-md-3 col-sm-6">
                            <aside class="widget">
                                <h3 class="widget-title uni-uppercase">News<span>letter</span></h3>
                                <div class="widget-content">
                                    <div class="uni-footer-newletter">
                                        <div class="input-group">
                                            <input type="email" class="form-control" placeholder="Enter your email">
                                            <button class="btn btn-sub" type="submit">subscribe</button>
                                        </div>
                                        <div class="uni-social">
                                            <h4>Follow us</h4>
                                            <ul>
                                                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                                                <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <div class="container">
                <div class="copyright-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="copyright-text"> <a href="">Care Project</a></p>
                        </div>
                        <div class="col-sm-6">
                            <ul class="copyright-menu">
                                <li><a href="#">Term Of Use</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    </div>
    </div>
    <script src="plugin/jquery/jquery-2.0.2.min.js"></script>
    <script src="plugin/jquery-ui/jquery-ui.min.js"></script>
    <script src="plugin/bootstrap/js/bootstrap.js"></script>
    <script src="plugin/process-bar/tox-progress.js"></script>
    <script src="plugin/waypoint/jquery.waypoints.min.js"></script>
    <script src="plugin/counterup/jquery.counterup.min.js"></script>
    <script src="plugin/owl-carouse/owl.carousel.min.js"></script>
    <script src="plugin/jquery-ui/jquery-ui.min.js"></script>
    <script src="plugin/mediaelement/mediaelement-and-player.js"></script>
    <script src="plugin/masonry/masonry.pkgd.min.js"></script>
    <script src="plugin/datetimepicker/moment.min.js"></script>
    <script src="plugin/datetimepicker/bootstrap-datepicker.min.js"></script>
    <script src="plugin/datetimepicker/bootstrap-datepicker.tr.min.js"></script>
    <script src="plugin/datetimepicker/bootstrap-datetimepicker.js"></script>
    <script src="plugin/datetimepicker/bootstrap-datetimepicker.fr.js"></script>

    <script src="plugin/lightgallery/picturefill.min.js"></script>
    <script src="plugin/lightgallery/lightgallery.js"></script>
    <script src="plugin/lightgallery/lg-pager.js"></script>
    <script src="plugin/lightgallery/lg-autoplay.js"></script>
    <script src="plugin/lightgallery/lg-fullscreen.js"></script>
    <script src="plugin/lightgallery/lg-zoom.js"></script>
    <script src="plugin/lightgallery/lg-hash.js"></script>
    <script src="plugin/lightgallery/lg-share.js"></script>
    <script src="plugin/sticky/jquery.sticky.js"></script>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('booking');
            const nameInput = document.getElementById('name');
            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const dateInput = document.getElementById('Date1');
            const cityInput = document.getElementById('city');
            const doctorInput = document.getElementById('doctor');

            const todayDate = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', todayDate);

            // Real-time validation
            nameInput.addEventListener('input', validateName);
            phoneInput.addEventListener('input', validatePhone);
            emailInput.addEventListener('input', validateEmail);
            dateInput.addEventListener('input', validateDate);
            cityInput.addEventListener('input', validateCity);
            doctorInput.addEventListener('input', validateDoctor);

            form.addEventListener('submit', function(event) {
                const valid =
                    validateName() &
                    validatePhone() &
                    validateEmail() &
                    validateDate() &
                    validateCity() &
                    validateDoctor();

                if (!valid) {
                    event.preventDefault();
                }
            });

            function validateName() {
                const name = nameInput.value.trim();
                const error = document.getElementById('nameError');
                if (name === '') {
                    error.textContent = 'Name is required';
                    error.style.display = 'block';
                    return false;
                } else if (!/^[a-zA-Z ]+$/.test(name)) {
                    error.textContent = 'Only letters and spaces allowed';
                    error.style.display = 'block';
                    return false;
                } else {
                    error.style.display = 'none';
                    return true;
                }
            }

            function validatePhone() {
                const phone = phoneInput.value.trim();
                const error = document.getElementById('phoneError');
                if (phone === '') {
                    error.textContent = 'Phone number is required';
                    error.style.display = 'block';
                    return false;
                } else if (!/^[0-9]{10,15}$/.test(phone)) {
                    error.textContent = 'Invalid phone number (10–15 digits)';
                    error.style.display = 'block';
                    return false;
                } else {
                    error.style.display = 'none';
                    return true;
                }
            }

            function validateEmail() {
                const email = emailInput.value.trim();
                const error = document.getElementById('emailError');
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email === '') {
                    error.textContent = 'Email is required';
                    error.style.display = 'block';
                    return false;
                } else if (!regex.test(email)) {
                    error.textContent = 'Invalid email format';
                    error.style.display = 'block';
                    return false;
                } else {
                    error.style.display = 'none';
                    return true;
                }
            }

            function validateDate() {
                const date = dateInput.value.trim();
                const error = document.getElementById('dateError');
                if (date === '') {
                    error.textContent = 'Date is required';
                    error.style.display = 'block';
                    return false;
                } else if (date < todayDate) {
                    error.textContent = 'Date cannot be in the past';
                    error.style.display = 'block';
                    return false;
                } else {
                    error.style.display = 'none';
                    return true;
                }
            }

            function validateCity() {
                const city = cityInput.value.trim();
                const error = document.getElementById('cityError');
                if (city === '') {
                    error.textContent = 'City is required';
                    error.style.display = 'block';
                    return false;
                } else if (!/^[a-zA-Z ]+$/.test(city)) {
                    error.textContent = 'Only letters and spaces allowed';
                    error.style.display = 'block';
                    return false;
                } else {
                    error.style.display = 'none';
                    return true;
                }
            }

            function validateDoctor() {
                const doctor = doctorInput.value.trim();
                const error = document.getElementById('DoctorError');
                if (doctor === '') {
                    error.textContent = 'Doctor is required';
                    error.style.display = 'block';
                    return false;
                } else if (!/^[a-zA-Z. ]+$/.test(doctor)) {
                    error.textContent = 'Only letters, spaces, dots allowed';
                    error.style.display = 'block';
                    return false;
                } else {
                    error.style.display = 'none';
                    return true;
                }
            }

            // Optional: Animate success/error alert (e.g. after redirect or DB insert)
            setTimeout(() => {
                const alerts = document.querySelectorAll('.success-alert, .error-alert');
                alerts.forEach(alert => alert.style.top = '20px');
                setTimeout(() => {
                    alerts.forEach(alert => alert.style.top = '-100px');
                }, 4000);
            }, 500);
        });
    </script>

    <!-- Session check to block form submission if not logged in -->
    <?php if (!isset($_SESSION['user_email'])): ?>
        <script>
            document.getElementById('booking').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('❗ Please login first to book an appointment');
                window.location.href = 'login.php';
            });
        </script>
    <?php endif; ?>

</body>

</html>