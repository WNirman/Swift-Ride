-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 04:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carrentalp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('superadmin','manager') DEFAULT 'manager',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `name`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'sysadmin', '$2y$10$aII/iG.z5x5IHUmnWKRBheO4oLvwj7kJY2QsykCU0ulh5pzYv3FRu', 'System Administrator', 'admin@carrental.com', '0700000000', 'superadmin', '2025-09-10 23:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `vehicle_id`, `pickup_date`, `return_date`, `pickup_location`, `dropoff_location`, `total_amount`, `booking_date`, `created_at`, `status`) VALUES
(1, 1, 1, '2025-09-05', '2025-09-10', 'Colombo Main Branch', 'Kandy Central Branch', 250.00, '2025-09-11 04:46:52', '2025-09-10 23:16:52', 'confirmed'),
(2, 2, 2, '2025-09-08', '2025-09-12', 'Kandy Central Branch', 'Colombo Main Branch', 300.00, '2025-09-11 04:46:52', '2025-09-10 23:16:52', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `branch` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `city`, `branch`) VALUES
(1, 'Colombo', 'Main Branch'),
(2, 'Jaffna', 'Sub Branch'),
(3, 'Kandy', 'Central Branch'),
(4, 'Jaffna', 'Jaffna Branch'),
(5, 'Colombo', 'Colombo Branch'),
(6, 'Gampaha', 'Gampaha Branch'),
(7, 'Kandy', 'Kandy Branch'),
(8, 'Trincomalee', 'Trinco Branch'),
(9, 'Anuradhapura', 'Anuradhapura Branch');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(100) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `method`, `status`) VALUES
(1, 1, 250.00, 'Credit Card', 'confirmed'),
(2, 2, 300.00, 'Cash', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `provider_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`provider_id`, `username`, `password`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'toyota_lanka', '$2y$12$7Rti4pGpH9H7b6Z8wZcE2e2h6F/Jv8xFyUQvR4kw6rW4jZzvJFxO', 'Toyota Lanka Pvt Ltd', 'toyota@provider.com', '0112345678', '2025-09-10 23:16:52'),
(2, 'provider1', '$2y$10$Ii.Bq2NOKCtIUVzkrmY5ku21ph0iGSsnuKdtbo61z6nYGMM5I0FO.', 'Test Provider', 'provider1@example.com', '0712345678', '2025-09-10 23:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `vehicle_id`, `user_id`, `rating`, `comment`) VALUES
(1, 1, 1, 5, 'Excellent car, very clean and smooth ride!'),
(2, 2, 2, 4, 'Good SUV, but fuel consumption was a bit high.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'john_doe', '$2y$12$wL5y0O5M1e2kPtFtHwvWGuJ9P0D9oi4AGYjP8L7V8uB6x4Gz1RmQe', 'John Doe', 'john@example.com', '0712345678', '2025-09-10 23:16:52'),
(2, 'jane_smith', '$2y$12$E7YQ7dS5U4/jlXkQe1TbNeDf9YosA2aOasf9qFf2l0O3kHjS1X2vG', 'Jane Smith', 'jane@example.com', '0723456789', '2025-09-10 23:16:52'),
(3, 'VNW', '$2y$10$ZW/VxD5rbZSr1asCxFz5LejfZv/TbxlXTn3iEIKZKHOYHLrSS18Bq', 'Wanith Nirman', 'wanithnirmanbc@gmail.com', '0762069840', '2025-09-11 22:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_brand` varchar(100) NOT NULL,
  `vehicle_model` varchar(200) NOT NULL,
  `vehicle_year` int(11) NOT NULL,
  `seats` int(11) NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `transmission` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `vehicle_availability` enum('yes','no') NOT NULL DEFAULT 'yes',
  `vehicle_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `provider_id`, `location_id`, `vehicle_type`, `vehicle_brand`, `vehicle_model`, `vehicle_year`, `seats`, `fuel_type`, `transmission`, `price`, `vehicle_availability`, `vehicle_image`) VALUES
(1, 1, 1, 'Car', 'Toyota', 'Corolla', 2022, 5, 'Petrol', 'Automatic', 50.00, 'yes', NULL),
(2, 1, 3, 'SUV', 'Nissan', 'X-Trail', 2021, 7, 'Petrol', 'Automatic', 60.00, 'yes', NULL),
(7, 2, 4, 'Car', '', 'Toyota', 0, 0, '', '', 15000.00, 'yes', NULL),
(8, 2, 3, 'Car', '', 'BMW', 0, 0, '', '', 10000000.00, 'yes', NULL),
(9, 2, 1, 'Bike', '', 'BMW', 0, 0, '', '', 1250.00, 'yes', NULL),
(10, 2, 1, 'Car', '', 'BMW', 0, 0, '', '', 125025.00, 'yes', '68c28c595e2a8.jpg'),
(11, 2, 9, 'Car', '', '12', 0, 0, '', '', 10.00, 'yes', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `location_id` (`location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`provider_id`),
  ADD CONSTRAINT `vehicles_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
ALTER TABLE 'providers' 
  ADD COLUMN total_earnings DECIMAL(10,2) DEFAULT 0;
