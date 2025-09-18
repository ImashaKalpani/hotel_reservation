-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 04:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_reservation`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `status` enum('booked','cancelled') NOT NULL DEFAULT 'booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `hotel_id`, `room_id`, `checkin_date`, `checkout_date`, `status`, `created_at`) VALUES
(11, 4, 12, 14, '2025-09-18', '2025-09-26', 'booked', '2025-09-17 14:06:14');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `location`, `description`, `image`, `created_at`) VALUES
(12, 'Ocean Breeze – Coral Wing', 'Colombo', 'A modern hotel offering comfortable rooms, excellent service, and convenient access to top attractions for both leisure and business travelers.', 'pexels-asman-chema-91897-594077.jpg', '2025-09-17 14:02:41'),
(13, 'Ocean Breeze – Sunset Villas', 'Mirissa', 'Experience a relaxing stay with stylish rooms, warm hospitality, and all the amenities you need for a memorable getaway.', 'pexels-pixabay-261102.jpg', '2025-09-17 14:07:18'),
(14, 'Ocean Breeze – Lagoon View', 'Badulla', 'A welcoming hotel designed for comfort and convenience, perfect for travelers seeking relaxation and great service.', 'pexels-boonkong-boonpeng-442952-1134176.jpg', '2025-09-17 14:12:48'),
(15, 'Ocean Breeze - Blue Horizon', 'Nuwaraeliya', 'Ocean Breeze Hotel offers a perfect blend of comfort, elegance, and seaside charm, making every stay truly unforgettable.', 'pexels-thorsten-technoman-109353-338504.jpg', '2025-09-17 14:30:47'),
(16, 'Ocean Breeze - Palm Haven', 'Galle', 'A relaxing escape by the sea, where comfort meets coastal beauty. Your perfect getaway spot for leisure, luxury, and unforgettable memories.', 'pexels-quark-studio-1159039-2506988.jpg', '2025-09-17 14:35:05'),
(17, 'Ocean Breeze - Skyline Shore', 'Matara', 'Enjoy warm hospitality, modern amenities, and stunning ocean views. Stay, relax, and enjoy the soothing rhythm of the waves.', 'pexels-pixabay-261395.jpg', '2025-09-17 14:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `availability` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `type`, `price`, `availability`, `created_at`) VALUES
(14, 12, 'Single Room', 10000.00, 8, '2025-09-17 14:05:06'),
(17, 12, 'Double Room', 20000.00, 10, '2025-09-17 14:38:25'),
(18, 12, 'Family Room', 50000.00, 10, '2025-09-17 14:38:51'),
(19, 13, 'Single Room', 10000.00, 8, '2025-09-17 14:40:45'),
(20, 13, 'Double Room', 20000.00, 10, '2025-09-17 14:42:12'),
(21, 13, 'Family Room', 50000.00, 10, '2025-09-17 14:42:29'),
(22, 14, 'Single Room', 10000.00, 8, '2025-09-17 14:42:48'),
(23, 14, 'Double Room', 20000.00, 10, '2025-09-17 14:43:04'),
(24, 14, 'Family Room', 50000.00, 10, '2025-09-17 14:43:17'),
(25, 15, 'Single Room', 10000.00, 8, '2025-09-17 14:43:31'),
(26, 15, 'Double Room', 20000.00, 10, '2025-09-17 14:43:45'),
(27, 15, 'Family Room', 50000.00, 10, '2025-09-17 14:44:00'),
(28, 16, 'Single Room', 20000.00, 10, '2025-09-17 14:44:15'),
(29, 16, 'Double Room', 40000.00, 10, '2025-09-17 14:44:31'),
(30, 16, 'Family Room', 60000.00, 15, '2025-09-17 14:44:47'),
(31, 17, 'Single Room', 30000.00, 10, '2025-09-17 14:45:04'),
(32, 17, 'Double Room', 70000.00, 15, '2025-09-17 14:45:17'),
(33, 17, 'Family Room', 90000.00, 20, '2025-09-17 14:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(4, 'imasha', 'imasha@gmail.com', '$2y$12$PzmsvtrHihrCZ5glUYIvQOIxxflacdguAMZOmHMtUQB0Y/yCpzhUO', '2025-09-17 14:05:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
