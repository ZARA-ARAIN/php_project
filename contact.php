<?php
include('connection.php'); // make sure this connects to your DB

// Start session for secure message handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store messages in session to prevent URL parameter tampering
if (isset($_GET['success'])) {
    $_SESSION['success'] = $_GET['success'];
    header("Location: contact.php");
    exit();
}

if (isset($_GET['error'])) {
    $_SESSION['error'] = $_GET['error'];
    header("Location: contact.php");
    exit();
}

if (isset($_POST['sendMessage'])) {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $message = mysqli_real_escape_string($connection, $_POST['message']);

    // Insert into messages table
    $query = "INSERT INTO messages (name, phone, email, message) 
              VALUES ('$name', '$phone', '$email', '$message')";

    if (mysqli_query($connection, $query)) {
        header("Location: contact.php?success=Thanks for contacting us! We'll get back to you soon.");
        exit();
    } else {
        header("Location: contact.php?error=Message could not be sent. Please try again.");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact</title>
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
        /* Modern Success Message Notification */
        .success-message-card {
            background: white;
            border-radius: 12px;
            padding: 12px 18px;
            max-width: 300px;
            margin: 0px auto 25px 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: scale(0.9);
            opacity: 0;
            animation: cardEntrance 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            border-top: 4px solid #4CAF50;

        }

        .success-icon {
            font-size: 40px;
            color: #4CAF50;
            margin-bottom: 10px;
            animation: iconBounce 0.8s ease;
        }

        .success-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .success-content {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .success-button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .success-button:hover {
            background: #3e8e41;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        @keyframes cardEntrance {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes iconBounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        .error-message-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 350px;
            margin: 20px auto;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: scale(0.9);
            opacity: 0;
            animation: cardEntrance 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            border-top: 4px solid #e74c3c;
        }

        .error-icon {
            font-size: 60px;
            color: #e74c3c;
            margin-bottom: 20px;
            animation: iconBounce 0.8s ease;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .error-content {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .error-button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .error-button:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
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

                <li><a href="departments.php">Departments</a>

                </li>
                <li><a href='blog.php'>Blog</a>

                </li>
                <li><a href='shop.php'>shop</a>
                </li>
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
                                <div class="col-md-2">
                                    <!--LOGO-->
                                    <div class="wrapper-logo">
                                        <a class="logo-default" href="#"><img src="images/logo.png" alt="" class="img-responsive"></a>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <!--MENU TEXT-->
                                    <div class="uni-main-menu">
                                        <nav class="main-navigation uni-menu-text">
                                            <div class="cssmenu">
                                                <ul>
                                                    <li><a href="home.php">Home</a>

                                                    </li>
                                                    <li class="has-sub"><a href='#'>Information</a>
                                                        <ul>

                                                            <li><a href="about.php">About</a></li>
                                                            <li><a href="contact.php">Contact</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><a href="doctors.php">Doctors</a></li>
                                                    <li><a href="departments.php">Departments</a>
                                                    </li>
                                                    <li><a href='blog.php'>Blog</a>
                                                    </li>
                                                    <li><a href='shop.php'>shop</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </nav>
                                    </div>

                                    <!--SEARCH AND APPOINTMENT-->
                                    <div class="uni-search-appointment">
                                        <ul>
                                            <li class="uni-btn-appointment">
                                                <a href="home.php#book-appointment">Appointment</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>


                            <!--FORM SEARCH-->
                            <div class="uni-form-search-header">
                                <div class="box-search-header collapse in" id="box-search-header">
                                    <div class="uni-input-group">
                                        <input type="text" name="key" placeholder="Search" class="form-control">
                                        <button class="uni-btn btn-search">
                                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div id="main-content" class="site-main-content">
                <section class="site-content-area">

                    <div class="uni-banner-default uni-background-1">
                        <div class="container">
                            <!-- Page title -->
                            <div class="page-title">
                                <div class="page-title-inner">
                                    <h1>Contact us</h1>
                                </div>
                            </div>
                            <!-- End page title -->

                            <!-- Breadcrumbs -->
                            <ul class="breadcrumbs">
                                <li><a href="home.php">home</a></li>
                                <li><a href="contact.php">Contact us</a></li>
                            </ul>
                            <!-- End breadcrumbs -->
                        </div>
                    </div>

                    <div class="uni-contact-us-body">
                        <!--MAP-->
                        <div class="uni-about-map">
                            <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2520.355677596112!2d-0.13052618407551403!3d50.82457546821709!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4875859878db2cc7%3A0xff129250121f260d!2s45+Queen's+Park+Rd%2C+Brighton+BN2+0GJ%2C+V%C6%B0%C6%A1ng+Qu%E1%BB%91c+Anh!5e0!3m2!1svi!2s!4v1514436176997" height="700" style="border:0"></iframe>
                        </div>



                        <div class="uni-contact-us-body-content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="uni-send-a-message">
                                            <div class="uni-contact-title">
                                                <h3>Send a message</h3>
                                                <div class="uni-line"></div>
                                            </div>
                                            <div class="uni-send-a-message-body">
                                                <?php if (isset($_SESSION['success'])): ?>
                                                    <div class="success-message-card">
                                                        <div class="success-icon">
                                                            <i class="fa fa-check-circle"></i>
                                                        </div>
                                                        <h2 class="success-title">Thank You!</h2>
                                                        <p class="success-content"><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                                                        <button class="success-button" onclick="this.parentElement.style.display='none'">OK</button>
                                                    </div>
                                                    <?php unset($_SESSION['success']); ?>
                                                <?php elseif (isset($_SESSION['error'])): ?>
                                                    <div class="error-message-card">
                                                        <div class="error-icon">
                                                            <i class="fa fa-exclamation-circle"></i>
                                                        </div>
                                                        <h2 class="error-title">Error!</h2>
                                                        <p class="error-content"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
                                                        <button class="error-button" onclick="this.parentElement.style.display='none'">OK</button>
                                                    </div>
                                                    <?php unset($_SESSION['error']); ?>
                                                <?php endif; ?>




                                                <form action="contact.php" method="POST">
                                                    <div class="input-group form-group">
                                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                        <input type="text" class="form-control" name="name" placeholder="your name" required>
                                                    </div>
                                                    <div class="input-group form-group">
                                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                        <input type="tel" class="form-control" name="phone" placeholder="phone number" required>
                                                    </div>
                                                    <div class="input-group form-group">
                                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                        <input type="email" class="form-control" name="email" placeholder="email" required>
                                                    </div>
                                                    <div class="input-group form-group">
                                                        <textarea name="message" class="form-control" placeholder="note" required></textarea>
                                                    </div>
                                                    <button type="submit" class="vk-btn vk-btn-send" name="sendMessage">Send</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="uni-contact-info">
                                            <div class="uni-contact-title">
                                                <h3>Contact us</h3>
                                                <div class="uni-line"></div>
                                            </div>
                                            <div class="uni-contact-info-body">
                                                <div class="item">
                                                    <div class="icon-holder">
                                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="text-holder">
                                                        <p>45 Queen's Park Rd, Brighton</p>
                                                        <span>United Kingdom</span>
                                                    </div>
                                                </div>

                                                <!--Receive records-->
                                                <div class="uni-receive-records">
                                                    <div class="uni-contact-info-title">
                                                        <h4>Receive records</h4>
                                                        <div class="uni-divider"></div>
                                                    </div>

                                                    <div class="item">
                                                        <div class="icon-holder">
                                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="text-holder">
                                                            <p>Call Us</p>
                                                            <span>(094) 123 4567</span>
                                                        </div>
                                                    </div>
                                                    <div class="item">
                                                        <div class="icon-holder">
                                                            <i class="fa fa-envelope" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="text-holder">
                                                            <p>Send A Message</p>
                                                            <span>medicareplus@domain.com</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--customer care-->
                                                <div class="uni-customer-care">
                                                    <div class="uni-contact-info-title">
                                                        <h4>customer care</h4>
                                                        <div class="uni-divider"></div>
                                                    </div>

                                                    <div class="item">
                                                        <div class="icon-holder">
                                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="text-holder">
                                                            <p>Call Us</p>
                                                            <span>(094) 123 4567</span>
                                                        </div>
                                                    </div>
                                                    <div class="item">
                                                        <div class="icon-holder">
                                                            <i class="fa fa-envelope" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="text-holder">
                                                            <p>Send A Message</p>
                                                            <span>medicareplus@domain.com</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="uni-contact-us-hours">
                                            <div class="uni-contact-us-title">
                                                <div class="icon">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                </div>
                                                <h4>opening hours</h4>
                                            </div>
                                            <div class="uni-contact-us-hours-content">
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
                                                <a href="home.php#book-appointment" class="book-appointment">Book appointments</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        // Auto-hide alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.custom-alert').fadeOut('slow');
            }, 5000);

            // Close button functionality
            $('.custom-alert .close-alert').click(function() {
                $(this).parent().fadeOut('slow');
            });
        });
    </script>

</body>

</html>