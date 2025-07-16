<?php
include 'connection.php';
$id = $_GET['id'];
$query = "SELECT * FROM doctors WHERE id = $id";
$result = mysqli_query($connection, $query);
$doctor = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($doctor['name']); ?> Profile | Medical Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #7f8c8d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            background-color: var(--light-bg);
            line-height: 1.6;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
        }
        
        .doctor-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 5px solid var(--secondary-color);
        }
        
        .specialization-badge {
            background-color: var(--secondary-color);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .availability-badge {
            font-size: 0.9rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
        }
        
        .available {
            background-color: #2ecc71;
            color: white;
        }
        
        .not-available {
            background-color: var(--accent-color);
            color: white;
        }
        
        .detail-item {
            margin-bottom: 1.2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }
        
        .btn-book {
            background-color: var(--accent-color);
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-book:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .social-links {
            margin-top: 1.5rem;
        }
        
        .social-links a {
            color: var(--light-text);
            margin-right: 1rem;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .doctor-img {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <header class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4">Dr. <?php echo htmlspecialchars($doctor['name']); ?></h1>
                    <div class="specialization-badge">
                        <i class="fas fa-stethoscope me-2"></i><?php echo htmlspecialchars($doctor['specialization']); ?>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="availability-badge <?php echo $doctor['is_available'] ? 'available' : 'not-available'; ?>">
                        <i class="fas fa-<?php echo $doctor['is_available'] ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                        <?php echo $doctor['is_available'] ? 'Available' : 'Not Available'; ?>
                    </span>
                </div>
            </div>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="profile-card h-100">
                    <img src="images/doctor/<?php echo htmlspecialchars($doctor['image']); ?>" alt="Dr. <?php echo htmlspecialchars($doctor['name']); ?>" class="doctor-img">
                    <div class="p-4">
                        <div class="text-center mb-4">
                        <a href="home.php?doctor_id=<?php echo $doctor['id']; ?>#book-appointment" class="btn btn-book btn-lg w-100">
                        <i class="fas fa-calendar-check me-2"></i>Book Appointment
                            </a>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-map-marker-alt me-2"></i>Location</div>
                            <div><?php echo htmlspecialchars($doctor['city']); ?></div>
                        </div>
                        
                        <?php if ($doctor['is_available']): ?>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-clock me-2"></i>Available Times</div>
                            <div><?php echo htmlspecialchars($doctor['available_times']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="social-links">
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="profile-card h-100 p-4">
                    <h3 class="mb-4">About Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-graduation-cap me-2"></i>Specialization Details</div>
                        <p><?php echo htmlspecialchars($doctor['specialization']); ?> specialist with extensive experience in the field.</p>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-info-circle me-2"></i>Professional Bio</div>
                        <p><?php echo nl2br(htmlspecialchars($doctor['description'])); ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-award me-2"></i>Education</div>
                                <p>MD in <?php echo htmlspecialchars($doctor['specialization']); ?> from Prestigious University</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-briefcase me-2"></i>Experience</div>
                                <p>15+ years of clinical experience</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-star me-2"></i>Patient Reviews</div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning me-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span>4.7 (128 reviews)</span>
                        </div>
                        <a href="#" class="text-primary">Read patient testimonials</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>