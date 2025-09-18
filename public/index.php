<?php
require 'config.php';
$user = current_user($pdo);

// Handle search query
$q = '';
if (!empty($_GET['q'])) {
    $q = trim($_GET['q']);
    $stmt = $pdo->prepare('SELECT * FROM hotels WHERE name LIKE ? OR location LIKE ?');
    $stmt->execute(["%$q%", "%$q%"]);
    $hotels = $stmt->fetchAll();
} else {
    $hotels = $pdo->query('SELECT * FROM hotels')->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LuxeStaysLK - Premium Hotel Reservation System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/css/lightgallery-bundle.min.css">
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
* { margin: 0; padding: 0; box-sizing: border-box; }
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
    color: var(blue);
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



@keyframes pulseBackground {
    0% { background: linear-gradient(135deg, #ff6b6b, #ff9e6b); }
    50% { background: linear-gradient(135deg, #ff9e6b, #ff6b6b); }
    100% { background: linear-gradient(135deg, #ff6b6b, #ff9e6b); }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

/* Hero Section with Video Background */
.hero {
    position: relative;
    width: 100%;
    max-width: 1300px;
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 3rem;
    margin: 0 auto;
    text-align: left;
    color: white;
    overflow: hidden;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
}

.video-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}

.video-background video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 0;
}

.hero-content { 
    max-width: 700px; 
    z-index: 2;
    position: relative;
}
.hero h1 { 
    font-size: 3.5rem; 
    font-weight: 800; 
    margin-bottom: 1.5rem;
    line-height: 1.2;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
}
.hero p { 
    font-size: 1.4rem; 
    margin-bottom: 2.5rem;
    opacity: 0.9;
    text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
}
.hero-btn {
    background: var(--accent-color);
    color: white;
    padding: 1.2rem 2.8rem;
    font-weight: 600;
    font-size: 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 5px 20px rgba(76, 201, 240, 0.4);
    animation: pulse 2s infinite;
    position: relative;
    overflow: hidden;
}
.hero-btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: var(--transition);
}
.hero-btn:hover:before {
    left: 100%;
}
.hero-btn:hover { 
    background: var(--primary-color); 
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(76, 201, 240, 0.5);
    animation: none;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(76, 201, 240, 0.7); }
    70% { box-shadow: 0 0 0 15px rgba(76, 201, 240, 0); }
    100% { box-shadow: 0 0 0 0 rgba(76, 201, 240, 0); }
}

.hero-btn i {
    font-size: 1.3rem;
}

/* Search Section */
.search-section {
    width: 100%;
    max-width: 1200px;
    margin: -80px auto 5rem;
    padding: 0 2rem;
    position: relative;
    z-index: 10;
}
.search-container {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.8rem;
    box-shadow: var(--box-shadow);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.search-container:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--gradient);
}

.search-title {
    text-align: center;
    margin-bottom: 1.8rem;
    font-size: 2rem;
    color: var(--text-color);
    font-weight: 700;
}
.search-form {
    display: flex;
    gap: 0;
    max-width: 800px;
    margin: 0 auto;
    position: relative;
}
.search-form input[type="text"] { 
    flex-grow: 1; 
    padding: 1.3rem 1.8rem; 
    border-radius: 50px 0 0 50px; 
    border: 2px solid #e2e8f0;
    outline: none;
    font-size: 1.1rem;
    transition: var(--transition);
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
    font-family: 'Poppins', sans-serif;
}
.search-form input[type="text"]:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
}
.search-form button.btn { 
    border-radius: 0 50px 50px 0; 
    padding: 0 2.5rem; 
    background: var(--primary-color); 
    color: white; 
    border: none; 
    cursor: pointer; 
    transition: var(--transition);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.1rem;
    font-family: 'Poppins', sans-serif;
}
.search-form button.btn:hover { 
    background: var(--secondary-color); 
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
}



/* Hotels Grid */
.hotels-section {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto 4rem;
    padding: 0 2rem;
}
.section-title {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 3rem;
    color: var(--text-color);
    font-weight: 800;
    position: relative;
    padding-bottom: 20px;
}
.section-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 5px;
    background: var(--gradient);
    border-radius: 10px;
}
.hotels-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
    gap: 1.5rem; 
    margin-bottom: 3rem;
}
.hotel {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    position: relative;
    opacity: 0;
    transform: translateY(20px);
    border: 1px solid rgba(67, 97, 238, 0.1);
}
.hotel:hover { 
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}
.hotel-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--accent-color);
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    z-index: 2;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.hotel-badge.premium {
    background: var(--gold);
}
.hotel-badge.featured {
    background: var(--primary-color);
}
.hotel-badge.new {
    background: var(--success-color);
}
.hotel-image-container {
    width: 100%;
    height: 250px;
    overflow: hidden;
    position: relative;
}
.hotel-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s ease;
}
.hotel:hover .hotel-image {
    transform: scale(1.1);
}
.hotel-content {
    padding: 2rem;
}
.hotel h3 { 
    color: var(--secondary-color); 
    margin-bottom: 0.8rem;
    font-size: 1.5rem;
    font-weight: 700;
}
.hotel-location {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-light);
    margin-bottom: 1rem;
    font-size: 1rem;
}
.hotel-location i {
    color: var(--primary-color);
    font-size: 1.1rem;
}
.hotel p { 
    font-size: 1rem; 
    line-height: 1.6; 
    margin-bottom: 1.8rem;
    color: var(--text-color);
}
.hotel-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.hotel-price {
    font-weight: 800;
    font-size: 1.4rem;
    color: var(--primary-color);
    display: flex;
    flex-direction: column;
}
.hotel-price .original {
    font-size: 1rem;
    text-decoration: line-through;
    color: var(--text-light);
    font-weight: 500;
}
.hotel-price .discount {
    font-size: 0.9rem;
    color: var(--success-color);
    font-weight: 600;
}
.hotel-price span {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-light);
}
.hotel a.btn { 
    padding: 0.8rem 1.8rem; 
    background: var(--primary-color); 
    color: white; 
    border-radius: 10px; 
    text-decoration: none; 
    font-weight: 600; 
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.hotel a.btn:hover { 
    background: var(--secondary-color); 
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
}

/* Staggered animation for hotel cards */
.hotel.animated {
    animation: fadeInUp 0.6s forwards;
}
.hotel:nth-child(1) { animation-delay: 0.1s; }
.hotel:nth-child(2) { animation-delay: 0.2s; }
.hotel:nth-child(3) { animation-delay: 0.3s; }
.hotel:nth-child(4) { animation-delay: 0.4s; }
.hotel:nth-child(5) { animation-delay: 0.5s; }

/* No hotels message */
.no-hotels {
    text-align: center; 
    padding: 4rem 3rem;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin: 2rem 0;
    border: 1px solid rgba(67, 97, 238, 0.1);
}
.no-hotels i {
    font-size: 4rem;
    color: var(--text-light);
    margin-bottom: 1.5rem;
}
.no-hotels h3 {
    color: var(--text-color);
    margin-bottom: 1rem;
    font-size: 1.8rem;
}
.no-hotels p {
    color: var(--text-light);
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Features Section */
.features-section {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto 6rem;
    padding: 0 2rem;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 4rem;
}
.feature {
    background: white;
    padding: 2.8rem 2.2rem;
    border-radius: var(--border-radius);
    text-align: center;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(67, 97, 238, 0.1);
}
.feature:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--gradient);
}
.feature:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}
.feature i {
    font-size: 3.2rem;
    color: var(--primary-color);
    margin-bottom: 1.8rem;
    background: var(--primary-light);
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 1.8rem;
    transition: var(--transition);
}
.feature:hover i {
    transform: scale(1.1) rotate(5deg);
    background: var(--gradient);
    color: white;
}
.feature h3 {
    font-size: 1.5rem;
    margin-bottom: 1.2rem;
    color: var(--text-color);
    font-weight: 700;
}
.feature p {
    color: var(--text-light);
    line-height: 1.7;
    font-size: 1.05rem;
}

/* Testimonials Section */
.testimonials-section {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto 6rem;
    padding: 0 2rem;
}
.testimonials-container {
    position: relative;
}
.testimonial {
    background: white;
    padding: 3rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin: 0 1rem;
    position: relative;
    border: 1px solid rgba(67, 97, 238, 0.1);
}
.testimonial:before {
    content: '\201C';
    position: absolute;
    top: 20px;
    left: 25px;
    font-size: 5rem;
    color: var(--primary-light);
    font-family: Georgia, serif;
    line-height: 1;
}
.testimonial-content {
    position: relative;
    z-index: 2;
}
.testimonial-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-color);
    margin-bottom: 2rem;
}
.testimonial-author {
    display: flex;
    align-items: center;
    gap: 15px;
}
.testimonial-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-light);
    transition: var(--transition);
}
.testimonial:hover .testimonial-avatar {
    border-color: var(--primary-color);
    transform: scale(1.05);
}
.testimonial-info h4 {
    font-size: 1.2rem;
    margin-bottom: 0.3rem;
    color: var(--text-color);
}
.testimonial-info p {
    color: var(--text-light);
    font-size: 0.9rem;
}
.testimonial-rating {
    color: #FFC107;
    margin-top: 0.5rem;
    font-size: 1.1rem;
}

/* Banner Section */
.banner-about { 
    display: flex; 
    max-width: 1200px; 
    margin: 0 auto 6rem;
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden; 
    box-shadow: var(--box-shadow);
    border: 1px solid rgba(67, 97, 238, 0.1);
}
.banner-image { 
    flex: 1; 
    background-image: url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80'); 
    background-size: cover; 
    background-position: center; 
    min-height: 450px;
    position: relative;
}
.banner-image:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--gradient);
    opacity: 0.8;
    mix-blend-mode: multiply;
}
.banner-text { 
    flex: 1; 
    padding: 3.5rem; 
    display: flex; 
    flex-direction: column; 
    justify-content: center;
    position: relative;
}
.banner-text h2 { 
    font-size: 2.5rem; 
    margin-bottom: 1.5rem; 
    color: var(--primary-color); 
    font-weight: 800;
}
.banner-text p { 
    font-size: 1.1rem; 
    line-height: 1.8; 
    margin-bottom: 1.5rem; 
    color: var(--text-color);
}
.banner-text .btn {
    align-self: flex-start;
    margin-top: 1rem;
}

/* Newsletter Section */
.newsletter-section {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto 6rem;
    padding: 0 2rem;
}
.newsletter-container {
    background: var(--gradient);
    border-radius: var(--border-radius);
    padding: 4.5rem 3.5rem;
    text-align: center;
    color: white;
    box-shadow: var(--box-shadow);
    position: relative;
    overflow: hidden;
}
.newsletter-container:before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.05)"/></svg>');
    background-size: 100% 100%;
    transform: rotate(30deg);
    z-index: 1;
}
.newsletter-content {
    position: relative;
    z-index: 2;
}
.newsletter-container h2 {
    font-size: 2.4rem;
    margin-bottom: 1.2rem;
    font-weight: 700;
}
.newsletter-container p {
    font-size: 1.1rem;
    margin-bottom: 2.5rem;
    opacity: 0.9;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}
.newsletter-form {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
    gap: 0;
}
.newsletter-form input {
    flex-grow: 1;
    padding: 1.2rem 1.5rem;
    border-radius: 50px 0 0 50px;
    border: none;
    outline: none;
    font-size: 1rem;
    font-family: 'Poppins', sans-serif;
}
.newsletter-form button {
    padding: 0 2.2rem;
    background: var(--dark-color);
    color: white;
    border: none;
    border-radius: 0 50px 50px 0;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-family: 'Poppins', sans-serif;
}
.newsletter-form button:hover {
    background: black;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

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

/* Responsive Design */
@media (max-width: 1200px) {
    .hero h1 { font-size: 3rem; }
}

@media (max-width: 992px) {
    .hero h1 { font-size: 2.5rem; }
    .banner-about { flex-direction: column; }
    .banner-image { min-height: 350px; }
    .hotels-grid {
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    }
}

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
    .hero {
        min-height: 80vh;
        border-radius: 0;
        margin: 0;
        padding: 2rem;
    }
    .hero-content {
        text-align: center;
        max-width: 100%;
    }
    .hero h1 { font-size: 2.2rem; }
    .hero p { font-size: 1.1rem; }
    .search-container {
        margin: 0 1rem -40px;
        padding: 2rem 1.5rem;
    }
    .search-form {
        flex-direction: column;
        gap: 1rem;
    }
    .search-form input[type="text"],
    .search-form button.btn {
        border-radius: 50px;
        width: 100%;
    }
    .search-form button.btn {
        padding: 1.2rem;
    }
    .hotels-grid {
        grid-template-columns: 1fr;
    }
    .section-title {
        font-size: 2rem;
    }
    .newsletter-form {
        flex-direction: column;
    }
    .newsletter-form input,
    .newsletter-form button {
        border-radius: 50px;
        width: 100%;
    }
    .newsletter-form button {
        padding: 1.2rem;
        margin-top: 1rem;
    }
    .discount-banner p {
        font-size: 0.9rem;
        padding: 0 1rem;
    }
}

@media (max-width: 576px) {
    :root {
        --navbar-height: 70px;
    }
    .hero {
        min-height: 70vh;
        padding: 1.5rem;
    }
    .hero h1 { font-size: 2rem; }
    .hero p { font-size: 1rem; }
    .hero-btn {
        padding: 1rem 2rem;
        font-size: 1rem;
    }
    .section-title {
        font-size: 1.8rem;
    }
    .banner-text {
        padding: 2.5rem 1.5rem;
    }
    .banner-text h2 {
        font-size: 2rem;
    }
    .footer-container {
        grid-template-columns: 1fr;
        text-align: center;
    }
    .footer-section h3:after {
        left: 50%;
        transform: translateX(-50%);
    }
    .filter-options {
        justify-content: center;
    }
    .discount-banner .highlight {
        display: block;
        margin: 0.5rem 0;
    }
}

/* Animations */
@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}
@keyframes fadeInUp { 
    from { opacity: 0; transform: translateY(30px); } 
    to { opacity: 1; transform: translateY(0); } 
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-100%); }
    to { opacity: 1; transform: translateY(0); }
}

/* Utility Classes */
.text-center { text-align: center; }
.mb-3 { margin-bottom: 1.5rem; }
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 2.2rem;
    background: var(--primary-color);
    color: white;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    gap: 8px;
    font-size: 1.05rem;
}
.btn:hover {
    background: var(--secondary-color);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
}
.btn-outline {
    background: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
}
.btn-outline:hover {
    background: var(--primary-color);
    color: white;
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
            <a href="index.php" class="active">Home</a>
            <a href="#hotels">Hotels</a>
            <a href="bookings.php">Bookings</a>
            <div class="nav-user">
                <span><i class="fas fa-user-circle"></i> Hello, <?=htmlspecialchars($user['name'])?></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        <?php else: ?>
            <a href="index.php" class="active">Home</a>
            <a href="#hotels">Hotels</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>


<section class="hero">
        <div class="video-background">
    <video autoplay muted loop playsinline>
        <source src="IMAGES/video1.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
        <div class="video-overlay"></div>
    </div>
    <div class="hero-content" data-aos="fade-right" data-aos-duration="1000">
        <h1>Discover Your Perfect Stay in Sri Lanka</h1>
        <p>Luxury accommodations at unbeatable prices. Book now and experience the best hospitality.</p>
        <a href="#hotels" class="hero-btn">
            <i class="fas fa-calendar-check"></i> Book Now
        </a>
    </div>
</section>

<div class="search-section">
    <div data-aos="fade-up" data-aos-duration="800">
        <form method="get" action="" class="search-form">
            <input type="text" name="q" placeholder="Search..." value="<?=htmlspecialchars($q)?>" />
            <button class="btn" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
</div>



<section class="hotels-section" id="hotels">
    <h2 class="section-title" data-aos="fade-up">Hotels</h2>
    
    <?php if (empty($hotels)): ?>
        <div class="no-hotels" data-aos="fade-up">
            <i class="fas fa-search"></i>
            <h3>No Rooms found</h3>
            <p>Try adjusting your search criteria or browse all our rooms.</p>
            <a href="index.php" class="btn">View All Hotels</a>
        </div>
    <?php else: ?>
        <div class="hotels-grid">
            <?php foreach($hotels as $h): ?>
                <?php 
                $img = !empty($h['image']) ? $h['image'] : 'default.jpg'; 
                $badges = [
                    ['type' => 'premium', 'text' => 'Premium'],
                    ['type' => 'featured', 'text' => 'Featured'],
                    ['type' => 'new', 'text' => 'New']
                ];
                $badge = $badges[array_rand($badges)];
                $original_price = rand(150, 300);
                $discount = rand(10, 30);
                $discounted_price = $original_price - ($original_price * $discount / 100);
                ?>
                <div class="hotel" data-aos="fade-up" data-category="<?= strtolower(str_replace(' ', '-', $badge['text'])) ?>">
                    <div class="hotel-image-container">
                        <img src="uploads/<?=htmlspecialchars($img)?>" alt="<?=htmlspecialchars($h['name'])?>" class="hotel-image">
                        <div class="hotel-badge <?= $badge['type'] ?>"><?= $badge['text'] ?></div>
                    </div>
                    <div class="hotel-content">
                        <h3><?=htmlspecialchars($h['name'])?></h3>
                        <div class="hotel-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?=htmlspecialchars($h['location'])?>
                        </div>
                        <p><?=nl2br(htmlspecialchars($h['description']))?></p>
                        <div class="hotel-footer">
                            
                            <a class="btn" href="hotel.php?id=<?=(int)$h['id']?>">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="features-section">
    <h2 class="section-title" data-aos="fade-up">Why Choose Us</h2>
    <div class="features-grid">
        <div class="feature" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-tag"></i>
            <h3>Best Price Guarantee</h3>
            <p>Find a lower rate elsewhere? We'll match it and give you an additional discount.</p>
        </div>
        <div class="feature" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-star"></i>
            <h3>Verified Reviews</h3>
            <p>Read genuine reviews from guests who have stayed at our partner hotels.</p>
        </div>
        <div class="feature" data-aos="fade-up" data-aos-delay="300">
            <i class="fas fa-lock"></i>
            <h3>Secure Booking</h3>
            <p>Your personal and payment information is protected with advanced encryption.</p>
        </div>
    </div>
</section>

<section class="testimonials-section">
    <h2 class="section-title" data-aos="fade-up">What Our Guests Say</h2>
    <div class="testimonials-container" data-aos="fade-up">
        <div class="testimonial">
            <div class="testimonial-content">
                <div class="testimonial-text">
                    "LuxeStaysLK made my Sri Lanka trip unforgettable. The hotels were exactly as described, and the booking process was seamless. Will definitely use this service again!"
                </div>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Sarah Johnson" class="testimonial-avatar">
                    <div class="testimonial-info">
                        <h4>Sarah Johnson</h4>
                        <p>Travel Blogger</p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="banner-about">
    <div class="banner-image" data-aos="fade-right"></div>
    <div class="banner-text" data-aos="fade-left">
        <h2>About LuxeStaysLK</h2>
        <p>We are Sri Lanka's premier hotel booking platform, connecting travelers with the finest accommodations across the island.</p>
        <p>With years of experience in the hospitality industry, we've curated a selection of hotels that meet the highest standards of comfort, service, and value.</p>
        <a href="#hotels" class="btn">
            <i class="fas fa-hotel"></i> Explore Hotels
        </a>
    </div>
</section>

<section class="newsletter-section">
    <div class="newsletter-container" data-aos="fade-up">
        <div class="newsletter-content">
            <h2>Subscribe to Our Newsletter</h2>
            <p>Get exclusive deals, travel tips, and updates on the best hotels in Sri Lanka delivered straight to your inbox.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
</section>

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
                <li><a href="#hotels"><i class="fas fa-bed"></i> Hotels</a></li>
                <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p><i class="fas fa-envelope"></i> <a href="mailto:support@lankashen.com">support@luxestayslk.com</a></p>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/lightgallery.min.js"></script>
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

// Filter functionality
document.querySelectorAll('.filter-option').forEach(option => {
    option.addEventListener('click', function() {
        // Remove active class from all options
        document.querySelectorAll('.filter-option').forEach(opt => {
            opt.classList.remove('active');
        });
        
        // Add active class to clicked option
        this.classList.add('active');
        
        const filter = this.getAttribute('data-filter');
        
        // Filter hotels
        document.querySelectorAll('.hotel').forEach(hotel => {
            if (filter === 'all') {
                hotel.style.display = 'block';
            } else {
                if (hotel.getAttribute('data-category') === filter) {
                    hotel.style.display = 'block';
                } else {
                    hotel.style.display = 'none';
                }
            }
        });
    });
});

// Animate hotel cards on scroll
function animateHotels() {
    const hotels = document.querySelectorAll('.hotel');
    hotels.forEach(hotel => {
        const hotelTop = hotel.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (hotelTop < windowHeight - 100) {
            hotel.classList.add('animated');
        }
    });
}

// Initial animation check
animateHotels();

// Animation check on scroll
window.addEventListener('scroll', animateHotels);

// Initialize lightGallery for hotel images
document.addEventListener('DOMContentLoaded', function() {
    const hotelImages = document.querySelectorAll('.hotel-image');
    hotelImages.forEach(image => {
        image.addEventListener('click', function() {
            // Create a simple lightbox effect
            const lightbox = document.createElement('div');
            lightbox.style.position = 'fixed';
            lightbox.style.top = '0';
            lightbox.style.left = '0';
            lightbox.style.width = '100%';
            lightbox.style.height = '100%';
            lightbox.style.backgroundColor = 'rgba(0,0,0,0.9)';
            lightbox.style.display = 'flex';
            lightbox.style.alignItems = 'center';
            lightbox.style.justifyContent = 'center';
            lightbox.style.zIndex = '10000';
            lightbox.style.cursor = 'zoom-out';
            
            const img = document.createElement('img');
            img.src = this.src;
            img.style.maxWidth = '90%';
            img.style.maxHeight = '90%';
            img.style.objectFit = 'contain';
            img.style.borderRadius = '10px';
            
            lightbox.appendChild(img);
            document.body.appendChild(lightbox);
            
            lightbox.addEventListener('click', function() {
                document.body.removeChild(lightbox);
            });
        });
    });
});

// Newsletter form validation
document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    
    if (!email || !email.includes('@')) {
        alert('Please enter a valid email address.');
        return;
    }
    
    alert('Thank you for subscribing to our newsletter!');
    this.reset();
});

// Countdown timer for discount banner (example)
function updateCountdown() {
    const countdownElement = document.querySelector('.discount-banner .highlight:last-child');
    if (countdownElement) {
        // This is just a placeholder - you would implement a real countdown here
        countdownElement.innerHTML = "WELCOME25";
    }
}

setInterval(updateCountdown, 1000);
</script>

</body>
</html>