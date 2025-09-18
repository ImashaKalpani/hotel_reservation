<?php
require 'config.php';
if (!is_logged_in()) { 
    header('Location: login.php'); 
    exit; 
}

$id = (int)($_GET['id'] ?? 0);

// Verify booking belongs to user
$stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo 'Booking not found or access denied.';
    exit;
}

// Prevent cancellation on or after check-in date
if ($booking['checkin_date'] <= date('Y-m-d')) {
    echo 'Cannot cancel booking on or after check-in date.';
    exit;
}

// Mark booking as cancelled
$u = $pdo->prepare('UPDATE bookings SET status = "cancelled" WHERE id = ?');
$u->execute([$id]);

header('Location: bookings.php');
exit;
