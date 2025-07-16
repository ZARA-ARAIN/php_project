<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shop | MediCare</title>
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
        /* Add some styling for the sorting dropdown */
        .woocommerce-ordering {
            float: right;
        }

        .orderby {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f8f8f8;
            cursor: pointer;
        }

        .product-item {
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .product-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
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
                <div class="site-content-area">

                    <div class="uni-banner-default uni-background-1">
                        <div class="container">
                            <!-- Page title -->
                            <div class="page-title">
                                <div class="page-title-inner">
                                    <h1>shop</h1>
                                </div>
                            </div>
                            <!-- End page title -->

                            <!-- Breadcrumbs -->
                            <ul class="breadcrumbs">
                                <li><a href="#">home</a></li>
                                <li><a href="#">shop</a></li>
                            </ul>
                            <!-- End breadcrumbs -->
                        </div>
                    </div>

                    <main id="main" class="site-main">
                        <div class="uni-shop-body">
                            <div class="container">
                                <div id="content">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="uni-single-product-left">
                                                <div class="uni-siderbar-left">
                                                    <form id="product-search-form" onsubmit="return false;">
                                                        <div class="vk-newlist-banner-test-search">
                                                            <input type="text" id="product-search-input" placeholder="Search...">
                                                            <button type="button" onclick="filterProducts()"><i class="fa fa-search" aria-hidden="true"></i></button>
                                                        </div>
                                                    </form>


                                                    <div class="vk-category">
                                                        <h3>Medicine Categories</h3>
                                                        <div class="uni-divider"></div>
                                                        <ul class="list-career">
                                                            <li><a href="#" class="category-link" data-category="Pain Relief"><i class="fa fa-angle-right"></i> Pain Relief</a></li>
                                                            <li><a href="#" class="category-link" data-category="Digestive Health"><i class="fa fa-angle-right"></i> Digestive Health</a></li>
                                                            <li><a href="#" class="category-link" data-category="Skin Care"><i class="fa fa-angle-right"></i> Skin Care</a></li>
                                                            <li><a href="#" class="category-link" data-category="Allergy & Cold"><i class="fa fa-angle-right"></i> Allergy & Cold</a></li>
                                                            <li><a href="#" class="category-link" data-category="Heart & Blood Pressure"><i class="fa fa-angle-right"></i> Heart & Blood Pressure</a></li>
                                                            <li><a href="#" class="category-link" data-category="Vitamins & Supplements"><i class="fa fa-angle-right"></i> Vitamins & Supplements</a></li>
                                                        </ul>

                                                    </div>


                                                    <div class="uni-filter-price">
                                                        <h3>Filter By Price</h3>
                                                        <div class="uni-divider"></div>
                                                        <div id="slider-range"></div>
                                                        <div class="label-filter-price"><input type="text" id="amount" readonly></div>
                                                        <button class="btn-filter-prince">SEARCH</button>

                                                        <div class="clearfix"></div>
                                                    </div>


                                                    <div class="uni-best-seller">
                                                        <h3>Best Sellers</h3>
                                                        <div class="uni-divider"></div>
                                                        <div class="vk-newlist-details">
                                                            <div class="vk-newlist-details-newlist1 vk-book-details">
                                                                <div class="vk-best-seller-img">
                                                                    <a href=""><img src="images/shop/img-4.png" alt="review" class="img-responsive"></a>
                                                                </div>
                                                                <div class="vk-best-seller-info">
                                                                    <h4><a href="">Redufluxes</a></h4>
                                                                    <ul>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                                                    </ul>
                                                                    <p>$26.00</p>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>

                                                            <div class="vk-newlist-details-newlist1  vk-book-details">
                                                                <div class="vk-best-seller-img">
                                                                    <a href=""><img src="images/shop/img-3.png" alt="review" class="img-responsive"></a>
                                                                </div>
                                                                <div class="vk-best-seller-info">
                                                                    <h4><a href="">Sperm Plus</a></h4>
                                                                    <ul>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                                                    </ul>
                                                                    <p>$26.00</p>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>

                                                            <div class="vk-newlist-details-newlist1  vk-book-details">
                                                                <div class="vk-best-seller-img">
                                                                    <a href=""><img src="images/shop/img-2.png" alt="review" class="img-responsive"></a>
                                                                </div>
                                                                <div class="vk-best-seller-info">
                                                                    <h4><a href="">paracetamol</a></h4>
                                                                    <ul>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                                                    </ul>
                                                                    <p>$26.00</p>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>

                                                            <div class="vk-newlist-details-newlist1  vk-book-details">
                                                                <div class="vk-best-seller-img">
                                                                    <a href=""><img src="images/shop/img-1.png" alt="review" class="img-responsive"></a>
                                                                </div>
                                                                <div class="vk-best-seller-info">
                                                                    <h4><a href="">terley infusion</a></h4>
                                                                    <ul>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                                        <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                                                    </ul>
                                                                    <p>$26.00</p>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- [Previous HTML content remains the same until the product filter section] -->

                                        <div class="col-md-9">
                                            <div class="uni-shop-siderbar-right">
                                                <div class="product-filter">
                                                    <p class="woocommerce-result-count">Showing <span>1–9</span> of <span>9</span> results</p>
                                                    <form class="woocommerce-ordering" method="get">
                                                        <select name="orderby" class="orderby" id="product-sort">
                                                            <option value="default" selected="selected">Default Sorting</option>
                                                            <option value="popularity">Sort by popularity</option>
                                                            <option value="rating">Sort by average rating</option>
                                                            <option value="date">Sort by newness</option>
                                                            <option value="price-low">Sort by price: low to high</option>
                                                            <option value="price-high">Sort by price: high to low</option>
                                                        </select>
                                                    </form>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="category-product uni-product-wapper">
                                                    <div class="row" id="product-container">
                                                        <?php
                                                        require_once 'connection.php';

                                                        // Check connection
                                                        if (!$connection) {
                                                            die("Connection failed: " . mysqli_connect_error());
                                                        }

                                                        // Query to get all products
                                                        $query = "SELECT * FROM products ORDER BY date_added DESC";
                                                        $result = mysqli_query($connection, $query);

                                                        // Check if query was successful
                                                        if (!$result) {
                                                            echo "<div class='col-12'>Error loading products: " . mysqli_error($conn) . "</div>";
                                                        } else {
                                                            // Check if there are any products
                                                            if (mysqli_num_rows($result) > 0) {
                                                                // Loop through each product and display it
                                                                while ($product = mysqli_fetch_assoc($result)) {
                                                                    // Calculate star rating display
                                                                    $fullStars = floor($product['rating']);
                                                                    $halfStar = ($product['rating'] - $fullStars) >= 0.5 ? 1 : 0;
                                                                    $emptyStars = 5 - $fullStars - $halfStar;

                                                                    // Generate star HTML
                                                                    $starsHTML = '';
                                                                    for ($i = 0; $i < $fullStars; $i++) {
                                                                        $starsHTML .= '<li><i class="fa fa-star" aria-hidden="true"></i></li>';
                                                                    }
                                                                    if ($halfStar) {
                                                                        $starsHTML .= '<li><i class="fa fa-star-half-o" aria-hidden="true"></i></li>';
                                                                    }
                                                                    for ($i = 0; $i < $emptyStars; $i++) {
                                                                        $starsHTML .= '<li><i class="fa fa-star-o" aria-hidden="true"></i></li>';
                                                                    }
                                                        ?>
                                                                    <!-- Product Item -->
                                                                    <div class="col-md-4 col-sm-6 product-item"
                                                                        data-category="<?php echo htmlspecialchars($product['category']); ?>"
                                                                        data-popularity="<?php echo htmlspecialchars($product['popularity']); ?>"
                                                                        data-rating="<?php echo htmlspecialchars($product['rating']); ?>"
                                                                        data-date="<?php echo htmlspecialchars($product['date_added']); ?>"
                                                                        data-price="<?php echo htmlspecialchars($product['price']); ?>">
                                                                        <ul class="category-product">
                                                                            <li>
                                                                                <div class="wrapper">
                                                                                    <div class="feature-image">
                                                                                        <a href=""><img class="wp-post-image img-responsive" src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></a>
                                                                                        <?php if ($product['on_sale']): ?>
                                                                                            <div class="uni-sale"><span>SALE</span></div>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                    <div class="stats">
                                                                                        <div class="box-title">
                                                                                            <h2 class="title-product">
                                                                                                <a href="" class="product_name"><?php echo htmlspecialchars($product['name']); ?></a>
                                                                                            </h2>
                                                                                        </div>
                                                                                        <div class="price-add-cart">
                                                                                            <div class="vote-star">
                                                                                                <ul><?php echo $starsHTML; ?></ul>
                                                                                            </div>
                                                                                            <div class="box-price">
                                                                                                <span class="price">
                                                                                                    <span class="woocommerce-Price-amount amount">
                                                                                                        <span class="woocommerce-Price-currencySymbol">$</span>
                                                                                                        <?php echo number_format($product['price'], 2); ?>
                                                                                                    </span>
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="box-add">
                                                                                                <a href="cart.php?action=add&name=<?php echo urlencode($product['name']); ?>&price=<?php echo urlencode($product['price']); ?>&image=<?php echo urlencode($product['image_path']); ?>">Add to cart</a>
                                                                                            </div>
                                                                                            <div class="clearfix"></div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                        <?php
                                                                }
                                                            } else {
                                                                echo "<div class='col-12'>No products found in the database.</div>";
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <!--PAGINATION-->
                                                <nav class="woocommerce-pagination">
                                                    <ul class="loop-pagination">
                                                        <li><a class="prev page-numbers" href="#"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
                                                        <li><a class="page-numbers current">1</a></li>
                                                    </ul><!-- .pagination -->
                                                </nav>

                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>

                </div>
            </div>

            <!-- [Rest of the HTML content remains the same] -->
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


    <!-- JavaScript for sorting functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('product-sort');
            const productContainer = document.getElementById('product-container');
            const products = Array.from(document.querySelectorAll('.product-item'));

            // Store original order for default sorting
            const originalOrder = products.map(product => product.outerHTML);

            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;

                // Create an array of products with their data for sorting
                const productsWithData = products.map(product => {
                    return {
                        element: product,
                        popularity: parseFloat(product.getAttribute('data-popularity')),
                        rating: parseFloat(product.getAttribute('data-rating')),
                        date: new Date(product.getAttribute('data-date')),
                        price: parseFloat(product.getAttribute('data-price'))
                    };
                });

                // Sort based on selected option
                switch (sortValue) {
                    case 'popularity':
                        productsWithData.sort((a, b) => b.popularity - a.popularity);
                        break;
                    case 'rating':
                        productsWithData.sort((a, b) => b.rating - a.rating);
                        break;
                    case 'date':
                        productsWithData.sort((a, b) => b.date - a.date);
                        break;
                    case 'price-low':
                        productsWithData.sort((a, b) => a.price - b.price);
                        break;
                    case 'price-high':
                        productsWithData.sort((a, b) => b.price - a.price);
                        break;
                    default:
                        // Default sorting - restore original order
                        productContainer.innerHTML = originalOrder.join('');
                        return;
                }

                // Clear the container
                productContainer.innerHTML = '';

                // Append sorted products
                productsWithData.forEach(item => {
                    productContainer.appendChild(item.element);
                });
            });
        });
    </script>
    <script>
        function filterProducts() {
            const input = document.getElementById('product-search-input').value.toLowerCase();
            const items = document.querySelectorAll('.product-item');
            let found = false;

            items.forEach(item => {
                const productName = item.querySelector('.product_name').textContent.toLowerCase();
                if (productName.includes(input)) {
                    item.style.display = 'block';
                    found = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Check if message already exists
            let noResultMsg = document.getElementById('no-results-message');
            const productContainer = document.querySelector('.row'); // Adjust if needed

            if (!found) {
                if (!noResultMsg) {
                    noResultMsg = document.createElement('div');
                    noResultMsg.id = 'no-results-message';
                    noResultMsg.className = 'alert alert-secondary text-center w-100 mt-4';
                    noResultMsg.textContent = 'No products found. Please try a different search.';
                    productContainer.appendChild(noResultMsg);
                }
            } else {
                if (noResultMsg) {
                    noResultMsg.remove();
                }
            }
        }
    </script>
    <script>
        document.querySelectorAll('.category-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const selectedCategory = this.getAttribute('data-category');
                const products = document.querySelectorAll('.product-item');

                let anyVisible = false;
                products.forEach(product => {
                    if (product.getAttribute('data-category') === selectedCategory || selectedCategory === 'All') {
                        product.style.display = 'block';
                        anyVisible = true;
                    } else {
                        product.style.display = 'none';
                    }
                });

                // Show "no product found" if nothing visible
                const message = document.getElementById('no-product-message');
                if (message) {
                    message.style.display = anyVisible ? 'none' : 'block';
                }
            });
        });
    </script>





    <!-- [Rest of the scripts remain the same] -->
    </div>
    </div>
</body>

</html>