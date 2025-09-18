<?php
require 'config.php';
if (!is_logged_in()) { 
    header('Location: login.php'); 
    exit; 
}

$user = current_user($pdo);

// Fetch all bookings for this user with hotel and room info
$stmt = $pdo->prepare(
    'SELECT b.*, h.name AS hotel_name, h.image AS hotel_image, r.type AS room_type 
     FROM bookings b 
     JOIN hotels h ON b.hotel_id = h.id 
     JOIN rooms r ON b.room_id = r.id 
     WHERE b.user_id = ? 
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - LuxeStaysLK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #eef2ff;
        --secondary-color: #3f37c9;
        --accent-color: #50d9e1ff;
        --error-color: #f72585;
        --success-color: #4ad66d;
        --text-color: #2b2d42;
        --text-light: #6c757d;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --navbar-height: 80px;
        --border-radius: 16px;
        --box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        --transition: all 0.3s ease;
        --gradient: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        --gold: #FFD700;
        --silver: #C0C0C0;
        --bronze: #CD7F32;
    }
    * {margin:0; padding:0; box-sizing:border-box;}
    body {
        font-family: 'Poppins', sans-serif;
        background: #f9fafc;
        padding-top: var(--navbar-height);
        color: var(--text-color);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Preloader */
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    }

    .loader {
        width: 60px;
        height: 60px;
        border: 5px solid var(--primary-light);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Navbar - Advanced */
    nav.navbar {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--navbar-height);
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(15px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 2.5rem;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        z-index: 1000;
        opacity: 0;
        animation: slideDown 0.8s forwards;
        transition: var(--transition);
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    nav.navbar.scrolled {
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        height: 70px;
    }

    nav.navbar .logo { 
        font-weight: 800; 
        font-size: 1.8rem; 
        color: var(--primary-color); 
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
    }
    nav.navbar .logo i {
        font-size: 2.2rem;
        color: var(--primary-color);
        transition: var(--transition);
    }
    nav.navbar .logo:hover i {
        transform: rotate(-10deg);
    }
    nav.navbar .logo:after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 3px;
        background: var(--gradient);
        transition: var(--transition);
        border-radius: 10px;
    }
    nav.navbar .logo:hover:after {
        width: 100%;
    }
    nav.navbar .nav-links { 
        display: flex; 
        align-items: center;
        gap: 2rem;
    }
    nav.navbar .nav-links a { 
        color: var(--text-color); 
        text-decoration: none; 
        font-weight: 600; 
        transition: var(--transition);
        position: relative;
        padding: 0.5rem 0;
        font-size: 1.05rem;
    }
    nav.navbar .nav-links a:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background: var(--gradient);
        transition: var(--transition);
        border-radius: 10px;
    }
    nav.navbar .nav-links a:hover:after,
    nav.navbar .nav-links a.active:after {
        width: 100%;
    }
    nav.navbar .nav-links a:hover,
    nav.navbar .nav-links a.active {
        color: var(--primary-color);
    }
    nav.navbar .nav-user { 
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }
    nav.navbar .nav-user span { 
        color: var(--text-light);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    nav.navbar .nav-user a { 
        color: var(--primary-color); 
        text-decoration: none; 
        font-weight: 600;
        transition: var(--transition);
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    nav.navbar .nav-user a:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
    }
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-color);
        cursor: pointer;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--primary-light);
        transition: var(--transition);
    }
    .mobile-menu-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Page Container */
    .container {
        max-width: 1200px;
        margin: 100px auto 40px;
        padding: 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.8rem;
    }

    /* Booking Card */
    .booking-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s forwards;
        transition: var(--transition);
        border: 1px solid rgba(67, 97, 238, 0.1);
    }
    .booking-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .booking-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .booking-card:hover img {
        transform: scale(1.1);
    }

    .booking-card .content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .booking-card h3 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }
    .booking-card p {font-size: 0.95rem; margin-bottom: 0.5rem; color: var(--text-color);}
    .booking-card .dates {font-weight: 600;}
    .booking-card .status {
        font-weight: 600;
        margin: 0.5rem 0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        width: fit-content;
    }
    .status-booked {
        color: var(--success-color);
        background: rgba(74, 214, 109, 0.1);
    }
    .status-cancelled {
        color: var(--error-color);
        background: rgba(247, 37, 133, 0.1);
    }
    .booking-card a.btn {
        margin-top: auto;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0.8rem;
        background: var(--error-color);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: var(--transition);
    }
    .booking-card a.btn:hover {
        background: #d10b60;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3);
    }

    /* No bookings message */
    .no-bookings {
        text-align: center; 
        padding: 4rem 3rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin: 2rem 0;
        border: 1px solid rgba(67, 97, 238, 0.1);
        grid-column: 1 / -1;
    }
    .no-bookings i {
        font-size: 4rem;
        color: var(--text-light);
        margin-bottom: 1.5rem;
    }
    .no-bookings h3 {
        color: var(--text-color);
        margin-bottom: 1rem;
        font-size: 1.8rem;
    }
    .no-bookings p {
        color: var(--text-light);
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }
    .no-bookings a.btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Fade-in animation */
    @keyframes fadeInUp {
        from {opacity: 0; transform: translateY(20px);}
        to {opacity: 1; transform: translateY(0);}
    }

    /* Staggered animation delay */
    .booking-card:nth-child(1) {animation-delay: 0.1s;}
    .booking-card:nth-child(2) {animation-delay: 0.2s;}
    .booking-card:nth-child(3) {animation-delay: 0.3s;}
    .booking-card:nth-child(4) {animation-delay: 0.4s;}
    .booking-card:nth-child(5) {animation-delay: 0.5s;}

    /* Footer - Advanced */
    footer.footer { 
        margin-top: auto; 
        width: 100%; 
        background: var(--dark-color); 
        color: white; 
        padding: 6rem 1rem 2.5rem; 
        position: relative;
        overflow: hidden;
    }
    footer.footer:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient);
    }
    .footer-container { 
        max-width: 1200px; 
        margin: 0 auto; 
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3.5rem;
        position: relative;
        z-index: 2;
    }
    .footer-section h3 { 
        margin-bottom: 1.8rem; 
        font-size: 1.6rem; 
        padding-bottom: 0.8rem;
        position: relative;
        display: inline-block;
        font-weight: 700;
    }
    .footer-section h3:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--accent-color);
        border-radius: 10px;
        transition: var(--transition);
    }
    .footer-section:hover h3:after {
        width: 100%;
    }
    .footer-section ul { 
        list-style: none; 
    }
    .footer-section ul li { 
        margin-bottom: 1rem; 
        transition: var(--transition);
    }
    .footer-section ul li:hover {
        transform: translateX(5px);
    }
    .footer-section ul li a { 
        color: #ddd; 
        text-decoration: none;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }
    .footer-section ul li a:hover { 
        color: var(--accent-color); 
    }
    .footer-section p { 
        color: #ddd; 
        line-height: 1.6;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    .footer-social {
        display: flex;
        gap: 1.2rem;
        margin-top: 2rem;
    }
    .footer-social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        color: white;
        transition: var(--transition);
        font-size: 1.2rem;
        position: relative;
        overflow: hidden;
    }
    .footer-social a:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--gradient);
        opacity: 0;
        transition: var(--transition);
        z-index: 1;
    }
    .footer-social a i {
        position: relative;
        z-index: 2;
    }
    .footer-social a:hover:before {
        opacity: 1;
    }
    .footer-social a:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 5px 15px rgba(76, 201, 240, 0.3);
    }
    .footer-bottom { 
        text-align: center; 
        margin-top: 5rem; 
        padding-top: 2.5rem;
        border-top: 1px solid rgba(255,255,255,0.1);
        color: #aaa;
        font-size: 0.9rem;
        position: relative;
        z-index: 2;
        font-weight: 500;
    }

    /* Back to top button */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 5px 20px rgba(67,97,238,0.3);
        transition: var(--transition);
        opacity: 0;
        visibility: hidden;
        z-index: 999;
        font-size: 1.2rem;
    }
    .back-to-top.active {
        opacity: 1;
        visibility: visible;
    }
    .back-to-top:hover {
        background: var(--secondary-color);
        transform: translateY(-5px) scale(1.1);
    }

    /* Animations */
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-100%); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        nav.navbar { padding: 0 1.5rem; }
        .nav-links {
            position: fixed;
            top: var(--navbar-height);
            left: -100%;
            width: 80%;
            height: calc(100vh - var(--navbar-height));
            background: white;
            flex-direction: column;
            padding: 2.5rem;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            transition: var(--transition);
            z-index: 999;
            gap: 0;
        }
        .nav-links.active {
            left: 0;
        }
        .nav-links a {
            margin-left: 0;
            padding: 1.2rem 0;
            width: 100%;
            border-bottom: 1px solid #eee;
            font-size: 1.1rem;
        }
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            margin: 80px 1rem 2rem;
            padding: 1.5rem;
            grid-template-columns: 1fr;
        }
        .footer-container {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .footer-section h3:after {
            left: 50%;
            transform: translateX(-50%);
        }
    }

    @media (max-width: 576px) {
        :root {
            --navbar-height: 70px;
        }
        .container {
            padding: 1.2rem;
        }
    }
    </style>
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="loader"></div>
</div>

<nav class="navbar">
    <div class="logo">
        <i class="fas fa-hotel"></i>
        <span>LuxeStaysLK</span>
    </div>
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="nav-links" id="navLinks">
        <?php if ($user): ?>
            <a href="index.php">Home</a>
            <a href="index.php#hotels">Hotels</a>
            <a href="bookings.php" class="active">Bookings</a>
            <div class="nav-user">
                <span><i class="fas fa-user-circle"></i> Hello, <?=htmlspecialchars($user['name'])?></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="index.php#hotels">Hotels</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <?php if(empty($bookings)): ?>
        <p style="grid-column:1/-1; text-align:center; font-size:1.2rem; color:var(--text-dark);">No bookings yet.</p>
    <?php else: ?>
        <?php foreach($bookings as $b): 
            $img = !empty($b['hotel_image']) ? $b['hotel_image'] : 'default.jpg';
        ?>
            <div class="booking-card">
                <img src="uploads/<?=htmlspecialchars($img)?>" alt="<?=htmlspecialchars($b['hotel_name'])?>">
                <div class="content">
                    <h3><?=htmlspecialchars($b['hotel_name'])?></h3>
                    <p>Room: <?=htmlspecialchars($b['room_type'])?></p>
                    <p class="dates">Dates: <?=htmlspecialchars($b['checkin_date'])?> → <?=htmlspecialchars($b['checkout_date'])?></p>
                    <p class="status status-<?=htmlspecialchars(strtolower($b['status']))?>"><?=htmlspecialchars($b['status'])?></p>
                    <?php if($b['status'] === 'booked' && $b['checkin_date'] > date('Y-m-d')): ?>
                        <a href="cancel.php?id=<?=$b['id']?>" class="btn" onclick="return confirm('Cancel this booking?')">Cancel Booking</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>LuxeStaysLK</h3>
            <p>Sri Lanka's leading hotel reservation system, providing exceptional accommodation experiences since 2023.</p>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="index.php#hotels"><i class="fas fa-bed"></i> Hotels</a></li>
                <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p><i class="fas fa-envelope"></i> <a href="mailto:support@oceanbreezehotel.com">support@luxestayslk.com</a></p>
            <p><i class="fas fa-phone"></i> <a href="tel:+94123456789">+94 123 456 789</a></p>
            <p><i class="fas fa-map-marker-alt"></i> 123 Main Street, Colombo, Sri Lanka</p>
        </div>
        <div class="footer-section">
            <h3>Payment Methods</h3>
            <p>We accept all major credit cards and payment methods:</p>
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <i class="fab fa-cc-visa" style="font-size: 2rem;"></i>
                <i class="fab fa-cc-mastercard" style="font-size: 2rem;"></i>
                <i class="fab fa-cc-amex" style="font-size: 2rem;"></i>
                <i class="fab fa-cc-paypal" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?=date('Y')?> LuxeStaysLK Reservation System. All rights reserved.
    </div>
</footer>

<a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</a>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Preloader
window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    setTimeout(function() {
        preloader.style.opacity = '0';
        setTimeout(function() {
            preloader.style.display = 'none';
        }, 500);
    }, 1000);
});

// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Mobile menu toggle
document.getElementById('mobileMenuBtn').addEventListener('click', function() {
    document.getElementById('navLinks').classList.toggle('active');
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    const backToTop = document.getElementById('backToTop');
    
    if (window.pageYOffset > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    if (window.pageYOffset > 300) {
        backToTop.classList.add('active');
    } else {
        backToTop.classList.remove('active');
    }
});

// Back to top button
document.getElementById('backToTop').addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            document.getElementById('navLinks').classList.remove('active');
        }
    });
});
</script>
</body>
</html>