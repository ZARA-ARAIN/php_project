<?php
include 'connection.php'; // Your DB connection file
$query = "SELECT * FROM doctors";
$result = mysqli_query($connection, $query);

// Get all unique cities and specializations for filter
$cities = [];
$specializations = [];
$doctors_by_specialization = [];
while ($row = mysqli_fetch_assoc($result)) {
    $specialization = strtolower(trim($row['specialization']));
    $doctors_by_specialization[$specialization][] = $row;

    if (!in_array($row['city'], $cities)) {
        $cities[] = $row['city'];
    }
    if (!in_array($row['specialization'], $specializations)) {
        $specializations[] = $row['specialization'];
    }
}
sort($cities); // Sort cities alphabetically
sort($specializations); // Sort specializations alphabetically
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Doctors</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            width: 250px !important;
            padding-left: 12px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
            background-color: #fff !important;
            line-height: 40px !important;
            box-shadow: none !important;
        }


        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            color: #495057;

        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            top: 0px !important;
            right: 10px;
        }

        @media (max-width: 768px) {
            #citySearch {
                width: 100%;
            }
        }

        .search-form-box {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-inline {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .doctor-card.hidden {
            display: none;
        }

        .doctor-card {
            display: block;
        }

        @media (max-width: 768px) {
            .form-group {
                margin-bottom: 10px;
                width: 100%;
            }

            .form-inline button {
                width: 48%;
                margin-bottom: 10px;
            }
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
                                <div class="box-search-header collapse" id="box-search-header">
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
                    <!-- Hero Banner with Search Form -->
                    <div class="uni-banner-default uni-background-1">
                        <div class="container">
                            <div class="page-title">
                                <div class="page-title-inner">
                                    <h1>our doctor</h1>
                                </div>
                            </div>
                            <ul class="breadcrumbs">
                                <li><a href="home.php">home</a></li>
                                <li><a href="#">Info</a></li>
                                <li><a href="doctors.php">our doctor</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Modern Search Form -->
                    <!-- Compact Search Form -->
                    <div class="container" style="margin-bottom: 30px;">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="search-form-box" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <form id="doctorSearchForm" class="form-inline">
                                        <div class="form-group" style="margin-right: 10px; flex-grow: 1;">
                                            <div class="input-group" style="width: 100%;">
                                                <span class="input-group-addon" style="background: #3498db; color: white; border: none;">
                                                    <i class="fas fa-stethoscope"></i>
                                                </span>
                                                <input type="text" id="specialtySearch" class="form-control" placeholder="Specialty" style="height: 40px;">
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-right: 10px; flex-grow: 1;">
                                            <div class="input-group" style="width: 100%;">
                                                <span class="input-group-addon" style="background: #3498db; color: white; border: none;">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </span>
                                                <select id="citySearch" class="form-control">
                                                    <option value="">All Cities</option>
                                                    <?php foreach ($cities as $city): ?>
                                                        <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary" style="height: 40px; margin-right: 5px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button type="button" id="resetSearch" class="btn btn-default" style="height: 40px;">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctors Tab System -->
                    <div class="uni-our-doctor-body">
                        <div class="container">
                            <div class="uni-shortcode-tabs-default">
                                <div class="uni-shortcode-tab-2">
                                    <div class="tabbable-panel">
                                        <div class="tabbable-line">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#tab_default_1" data-toggle="tab">Cardiology</a></li>
                                                <li><a href="#tab_default_2" data-toggle="tab">Neurology</a></li>
                                                <li><a href="#tab_default_3" data-toggle="tab">Orthopedics</a></li>
                                                <li><a href="#tab_default_4" data-toggle="tab">Cancer Department</a></li>
                                                <li><a href="#tab_default_5" data-toggle="tab">Ophthalmology</a></li>
                                                <li><a href="#tab_default_6" data-toggle="tab">Respiratory</a></li>
                                            </ul>

                                            <div class="tab-content">
                                                <!-- Cardiology Tab -->
                                                <div class="tab-pane active" id="tab_default_1">
                                                    <div class="row" id="cardiology-doctors">
                                                        <?php foreach ($doctors_by_specialization['cardiology'] ?? [] as $row) { ?>
                                                            <div class="col-md-3 col-sm-6 doctor-card"
                                                                data-specialization="<?php echo strtolower($row['specialization']); ?>"
                                                                data-name="<?php echo strtolower($row['name']); ?>"
                                                                data-city="<?php echo strtolower($row['city']); ?>">
                                                                <div class="uni-our-doctor-item-default">
                                                                    <div class="item-img">
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>">
                                                                            <img src="images/doctor/<?php echo $row['image']; ?>" style="width:270px; height:270px; object-fit:cover; object-position: center 20%;" alt="">
                                                                        </a>
                                                                    </div>
                                                                    <div class="item-caption" style="font-family: 'Segoe UI', Arial, sans-serif; padding: 12px;">
                                                                        <h1 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin: 0 0 4px 0;"><?php echo $row['name']; ?></h1>
                                                                        <span style="font-size: 16px; font-weight: 600; color: #555; display: block; margin-bottom: 3px;"><?php echo $row['specialization']; ?></span>
                                                                        <span style="font-size: 14px; color: #777; display: block; margin-bottom: 10px;"><?php echo $row['city']; ?></span>
                                                                        <p style="margin: 0 0 12px 0; font-size: 14px;">
                                                                            <strong style="font-weight: 600;">Availability: </strong>
                                                                            <span style="color: <?php echo $row['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                                                                <?php echo $row['is_available'] ? $row['available_times'] : 'Not Available'; ?>
                                                                            </span>
                                                                        </p>
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="display: inline-block; padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                                                            View Profile
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Neurology Tab -->
                                                <div class="tab-pane" id="tab_default_2">
                                                    <div class="row" id="neurology-doctors">
                                                        <?php foreach ($doctors_by_specialization['neurology'] ?? [] as $row) { ?>
                                                            <div class="col-md-3 col-sm-6 doctor-card"
                                                                data-specialization="<?php echo strtolower($row['specialization']); ?>"
                                                                data-name="<?php echo strtolower($row['name']); ?>"
                                                                data-city="<?php echo strtolower($row['city']); ?>">
                                                                <div class="uni-our-doctor-item-default">
                                                                    <div class="item-img">
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>">
                                                                            <img src="images/doctor/<?php echo $row['image']; ?>" style="width:270px; height:270px; object-fit:cover; object-position: center 20%;" alt="">
                                                                        </a>
                                                                    </div>
                                                                    <div class="item-caption" style="font-family: 'Segoe UI', Arial, sans-serif; padding: 12px;">
                                                                        <h1 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin: 0 0 4px 0;"><?php echo $row['name']; ?></h1>
                                                                        <span style="font-size: 16px; font-weight: 600; color: #555; display: block; margin-bottom: 3px;"><?php echo $row['specialization']; ?></span>
                                                                        <span style="font-size: 14px; color: #777; display: block; margin-bottom: 10px;"><?php echo $row['city']; ?></span>
                                                                        <p style="margin: 0 0 12px 0; font-size: 14px;">
                                                                            <strong style="font-weight: 600;">Availability: </strong>
                                                                            <span style="color: <?php echo $row['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                                                                <?php echo $row['is_available'] ? $row['available_times'] : 'Not Available'; ?>
                                                                            </span>
                                                                        </p>
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="display: inline-block; padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                                                            View Profile
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Orthopedics Tab -->
                                                <div class="tab-pane" id="tab_default_3">
                                                    <div class="row" id="orthopedics-doctors">
                                                        <?php foreach ($doctors_by_specialization['orthopedics'] ?? [] as $row) { ?>
                                                            <div class="col-md-3 col-sm-6 doctor-card"
                                                                data-specialization="<?php echo strtolower($row['specialization']); ?>"
                                                                data-name="<?php echo strtolower($row['name']); ?>"
                                                                data-city="<?php echo strtolower($row['city']); ?>">
                                                                <div class="uni-our-doctor-item-default">
                                                                    <div class="item-img">
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>">
                                                                            <img src="images/doctor/<?php echo $row['image']; ?>" style="width:270px; height:270px; object-fit:cover; object-position: center 20%;" alt="">
                                                                        </a>
                                                                    </div>
                                                                    <div class="item-caption" style="font-family: 'Segoe UI', Arial, sans-serif; padding: 12px;">
                                                                        <h1 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin: 0 0 4px 0;"><?php echo $row['name']; ?></h1>
                                                                        <span style="font-size: 16px; font-weight: 600; color: #555; display: block; margin-bottom: 3px;"><?php echo $row['specialization']; ?></span>
                                                                        <span style="font-size: 14px; color: #777; display: block; margin-bottom: 10px;"><?php echo $row['city']; ?></span>
                                                                        <p style="margin: 0 0 12px 0; font-size: 14px;">
                                                                            <strong style="font-weight: 600;">Availability: </strong>
                                                                            <span style="color: <?php echo $row['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                                                                <?php echo $row['is_available'] ? $row['available_times'] : 'Not Available'; ?>
                                                                            </span>
                                                                        </p>
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="display: inline-block; padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                                                            View Profile
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Cancer Department Tab -->
                                                <div class="tab-pane" id="tab_default_4">
                                                    <div class="row" id="oncology-doctors">
                                                        <?php foreach ($doctors_by_specialization['cancer department'] ?? [] as $row) { ?>
                                                            <div class="col-md-3 col-sm-6 doctor-card"
                                                                data-specialization="<?php echo strtolower($row['specialization']); ?>"
                                                                data-name="<?php echo strtolower($row['name']); ?>"
                                                                data-city="<?php echo strtolower($row['city']); ?>">
                                                                <div class="uni-our-doctor-item-default">
                                                                    <div class="item-img">
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>">
                                                                            <img src="images/doctor/<?php echo $row['image']; ?>" style="width:270px; height:270px; object-fit:cover; object-position: center 20%;" alt="">
                                                                        </a>
                                                                    </div>
                                                                    <div class="item-caption" style="font-family: 'Segoe UI', Arial, sans-serif; padding: 12px;">
                                                                        <h1 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin: 0 0 4px 0;"><?php echo $row['name']; ?></h1>
                                                                        <span style="font-size: 16px; font-weight: 600; color: #555; display: block; margin-bottom: 3px;"><?php echo $row['specialization']; ?></span>
                                                                        <span style="font-size: 14px; color: #777; display: block; margin-bottom: 10px;"><?php echo $row['city']; ?></span>
                                                                        <p style="margin: 0 0 12px 0; font-size: 14px;">
                                                                            <strong style="font-weight: 600;">Availability: </strong>
                                                                            <span style="color: <?php echo $row['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                                                                <?php echo $row['is_available'] ? $row['available_times'] : 'Not Available'; ?>
                                                                            </span>
                                                                        </p>
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="display: inline-block; padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                                                            View Profile
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Ophthalmology Tab -->
                                                <div class="tab-pane" id="tab_default_5">
                                                    <div class="row" id="ophthalmology-doctors">
                                                        <?php foreach ($doctors_by_specialization['ophthalmology'] ?? [] as $row) { ?>
                                                            <div class="col-md-3 col-sm-6 doctor-card"
                                                                data-specialization="<?php echo strtolower($row['specialization']); ?>"
                                                                data-name="<?php echo strtolower($row['name']); ?>"
                                                                data-city="<?php echo strtolower($row['city']); ?>">
                                                                <div class="uni-our-doctor-item-default">
                                                                    <div class="item-img">
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>">
                                                                            <img src="images/doctor/<?php echo $row['image']; ?>" style="width:270px; height:270px; object-fit:cover; object-position: center 20%;" alt="">
                                                                        </a>
                                                                    </div>
                                                                    <div class="item-caption" style="font-family: 'Segoe UI', Arial, sans-serif; padding: 12px;">
                                                                        <h1 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin: 0 0 4px 0;"><?php echo $row['name']; ?></h1>
                                                                        <span style="font-size: 16px; font-weight: 600; color: #555; display: block; margin-bottom: 3px;"><?php echo $row['specialization']; ?></span>
                                                                        <span style="font-size: 14px; color: #777; display: block; margin-bottom: 10px;"><?php echo $row['city']; ?></span>
                                                                        <p style="margin: 0 0 12px 0; font-size: 14px;">
                                                                            <strong style="font-weight: 600;">Availability: </strong>
                                                                            <span style="color: <?php echo $row['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                                                                <?php echo $row['is_available'] ? $row['available_times'] : 'Not Available'; ?>
                                                                            </span>
                                                                        </p>
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="display: inline-block; padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                                                            View Profile
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Respiratory Tab -->
                                                <div class="tab-pane" id="tab_default_6">
                                                    <div class="row" id="pulmonology-doctors">
                                                        <?php foreach ($doctors_by_specialization['respiratory'] ?? [] as $row) { ?>
                                                            <div class="col-md-3 col-sm-6 doctor-card"
                                                                data-specialization="<?php echo strtolower($row['specialization']); ?>"
                                                                data-name="<?php echo strtolower($row['name']); ?>"
                                                                data-city="<?php echo strtolower($row['city']); ?>">
                                                                <div class="uni-our-doctor-item-default">
                                                                    <div class="item-img">
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>">
                                                                            <img src="images/doctor/<?php echo $row['image']; ?>" style="width:270px; height:270px; object-fit:cover; object-position: center 20%;" alt="">
                                                                        </a>
                                                                    </div>
                                                                    <div class="item-caption" style="font-family: 'Segoe UI', Arial, sans-serif; padding: 12px;">
                                                                        <h1 style="font-size: 22px; font-weight: 700; color: #2c3e50; margin: 0 0 4px 0;"><?php echo $row['name']; ?></h1>
                                                                        <span style="font-size: 16px; font-weight: 600; color: #555; display: block; margin-bottom: 3px;"><?php echo $row['specialization']; ?></span>
                                                                        <span style="font-size: 14px; color: #777; display: block; margin-bottom: 10px;"><?php echo $row['city']; ?></span>
                                                                        <p style="margin: 0 0 12px 0; font-size: 14px;">
                                                                            <strong style="font-weight: 600;">Availability: </strong>
                                                                            <span style="color: <?php echo $row['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                                                                <?php echo $row['is_available'] ? $row['available_times'] : 'Not Available'; ?>
                                                                            </span>
                                                                        </p>
                                                                        <a href="docpreview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="display: inline-block; padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                                                            View Profile
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            <script src="preview.js"></script>
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
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <script>
                $(document).ready(function() {
                    $('#citySearch').select2({
                        dropdownAutoWidth: true,
                        width: '250px'
                    });
                });
            </script>

            <script src="js/main.js"></script>
            <script>
                $(document).ready(function() {
                    // Specialty search suggestions
                    $('#specialtySearch').on('input focus', function() {
                        const query = $(this).val().toLowerCase();
                        const $suggestions = $('#specialtySuggestions');

                        if (query.length > 0) {
                            $suggestions.empty();

                            // Filter specializations
                            const matches = <?php echo json_encode($specializations); ?>.filter(spec =>
                                spec.toLowerCase().includes(query)
                            );

                            if (matches.length > 0) {
                                matches.forEach(spec => {
                                    $suggestions.append(
                                        `<a class="dropdown-item" href="#" data-value="${spec.toLowerCase()}">${spec}</a>`
                                    );
                                });
                                $suggestions.show();
                            } else {
                                $suggestions.hide();
                            }
                        } else {
                            $suggestions.hide();
                        }
                    });

                    // Handle suggestion selection
                    $(document).on('click', '#specialtySuggestions .dropdown-item', function(e) {
                        e.preventDefault();
                        $('#specialtySearch').val($(this).text());
                        $('#specialtySuggestions').hide();
                    });

                    // Hide suggestions when clicking elsewhere
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.search-specialty-group').length) {
                            $('#specialtySuggestions').hide();
                        }
                    });

                    // Rest of your existing search functionality...
                    const allCities = <?php echo json_encode($cities); ?>;
                    const $citySelect = $('#citySearch');
                    $citySelect.empty().append('<option value="">All Cities</option>');
                    allCities.forEach(city => {
                        $citySelect.append(`<option value="${city.toLowerCase()}">${city}</option>`);
                    });

                    const specializationMap = {
                        'cardiology': 1,
                        'cardiologist': 1,
                        'heart': 1,
                        'neurology': 2,
                        'neurologist': 2,
                        'brain': 2,
                        'orthopedics': 3,
                        'orthopedic': 3,
                        'bone': 3,
                        'oncology': 4,
                        'cancer': 4,
                        'ophthalmology': 5,
                        'eye': 5,
                        'pulmonology': 6,
                        'respiratory': 6,
                        'lung': 6
                    };

                    $('#doctorSearchForm').on('submit', function(e) {
                        e.preventDefault();
                        const specialty = $('#specialtySearch').val().toLowerCase();
                        const city = $('#citySearch').val().toLowerCase();
                        let foundTab = false;
                        let hasResults = false;

                        $('.doctor-card').removeClass('hidden');

                        $('.doctor-card').each(function() {
                            const doctorName = $(this).data('name');
                            const doctorSpecialization = $(this).data('specialization');
                            const doctorCity = $(this).data('city');

                            const matchesSpecialty = !specialty ||
                                doctorName.includes(specialty) ||
                                doctorSpecialization.includes(specialty);
                            const matchesCity = !city || doctorCity.includes(city);

                            if (!(matchesSpecialty && matchesCity)) {
                                $(this).addClass('hidden');
                            } else {
                                hasResults = true;
                                const tabId = $(this).closest('.tab-pane').attr('id');
                                $('.nav-tabs a[href="#' + tabId + '"]').parent().show();
                            }
                        });

                        for (const [term, tabIndex] of Object.entries(specializationMap)) {
                            if (specialty.includes(term)) {
                                $('.nav-tabs a[href="#tab_default_' + tabIndex + '"]').tab('show');
                                foundTab = true;
                                break;
                            }
                        }

                        if (!foundTab && hasResults) {
                            $('.tab-pane').each(function() {
                                if ($(this).find('.doctor-card:not(.hidden)').length > 0) {
                                    const tabId = $(this).attr('id');
                                    $('.nav-tabs a[href="#' + tabId + '"]').tab('show');
                                    return false;
                                }
                            });
                        }

                        $('.tab-pane').each(function() {
                            const tabId = $(this).attr('id');
                            if ($(this).find('.doctor-card:not(.hidden)').length === 0) {
                                $('.nav-tabs a[href="#' + tabId + '"]').parent().hide();
                            } else {
                                $('.nav-tabs a[href="#' + tabId + '"]').parent().show();
                            }
                        });
                    });

                    $('#resetSearch').on('click', function() {
                        $('#specialtySearch').val('');
                        $('#citySearch').val('').selectpicker('refresh');
                        $('.doctor-card').removeClass('hidden');
                        $('.nav-tabs li').show();
                        $('.nav-tabs a[href="#tab_default_1"]').tab('show');
                    });
                });
            </script>

</body>

</html>