<?php
require 'config.php';
if (!is_logged_in()) { 
    header('Location: login.php'); 
    exit; 
}

$user = current_user($pdo);

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header('Location: index.php'); 
    exit; 
}

// Collect form data
$hotel_id  = (int)($_POST['hotel_id'] ?? 0);
$room_id   = (int)($_POST['room_id'] ?? 0);
$checkin   = $_POST['checkin'] ?? '';
$checkout  = $_POST['checkout'] ?? '';

// Basic date validation
if (!$checkin || !$checkout || $checkin >= $checkout) { 
    echo 'Invalid dates'; 
    exit; 
}

// Ensure room exists and belongs to the hotel
$stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ? AND hotel_id = ?');
$stmt->execute([$room_id, $hotel_id]);
$room = $stmt->fetch();
if (!$room) { 
    echo 'Selected room not found'; 
    exit; 
}

// Insert booking into DB
$stmt = $pdo->prepare('INSERT INTO bookings (user_id, hotel_id, room_id, checkin_date, checkout_date) VALUES (?,?,?,?,?)');
$stmt->execute([$_SESSION['user_id'], $hotel_id, $room_id, $checkin, $checkout]);
$booking_id = $pdo->lastInsertId();

// Redirect to booking confirmation
header('Location: bookings.php?show=' . $booking_id);
exit;
