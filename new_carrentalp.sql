-- ============================================
-- FINAL CARRRANTALP DATABASE SCRIPT
-- ============================================

-- 1️⃣ Disable foreign key checks to allow table drops
SET FOREIGN_KEY_CHECKS = 0;

-- 2️⃣ Drop all tables if they exist
DROP TABLE IF EXISTS Reviews;
DROP TABLE IF EXISTS Payments;
DROP TABLE IF EXISTS Bookings;
DROP TABLE IF EXISTS Vehicles;
DROP TABLE IF EXISTS Locations;
DROP TABLE IF EXISTS providers;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Admins;

-- 3️⃣ Re-enable foreign key checks after drops
SET FOREIGN_KEY_CHECKS = 1;

-- 4️⃣ Create database
DROP DATABASE IF EXISTS carrentalp;
CREATE DATABASE carrentalp CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE carrentalp;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- TABLES
-- ============================================

-- Users table
CREATE TABLE IF NOT EXISTS Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE IF NOT EXISTS Admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('superadmin','manager') DEFAULT 'manager',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Providers table (lowercase)
CREATE TABLE providers (
    provider_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Locations table
CREATE TABLE IF NOT EXISTS Locations (
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    city VARCHAR(100) NOT NULL,
    branch VARCHAR(200) NOT NULL
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS Vehicles (
    vehicle_id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL,
    location_id INT NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    vehicle_brand VARCHAR(100) NOT NULL,
    vehicle_model VARCHAR(200) NOT NULL,
    vehicle_year INT NOT NULL,
    seats INT NOT NULL,
    fuel_type VARCHAR(50) NOT NULL,
    transmission VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    vehicle_availability ENUM('yes', 'no') NOT NULL DEFAULT 'yes',
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id),
    FOREIGN KEY (location_id) REFERENCES Locations(location_id)
);

ALTER TABLE Vehicles
ADD COLUMN vehicle_image VARCHAR(255) DEFAULT NULL AFTER vehicle_availability;


-- Bookings table
CREATE TABLE IF NOT EXISTS Bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    pickup_date DATE NOT NULL,
    return_date DATE NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (vehicle_id) REFERENCES Vehicles(vehicle_id)
);

-- Payments table
CREATE TABLE IF NOT EXISTS Payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(100) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (booking_id) REFERENCES Bookings(booking_id)
);

-- Reviews table
CREATE TABLE IF NOT EXISTS Reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment VARCHAR(500),
    FOREIGN KEY (vehicle_id) REFERENCES Vehicles(vehicle_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SAMPLE DATA
-- ============================================

INSERT INTO Admins (username, password, name, email, phone, role)
VALUES ('sysadmin', '$2y$10$e0V1g9bCwFpF.ZhYlvfZ6e1qQ7JYbxYbbFmY0nZxOwK6pGnNQ9jvG', 'System Administrator', 'admin@carrental.com', '0700000000', 'superadmin');


-- Providers
INSERT INTO providers (username, password, name, email, phone)
VALUES 
('toyota_lanka', '$2y$12$7Rti4pGpH9H7b6Z8wZcE2e2h6F/Jv8xFyUQvR4kw6rW4jZzvJFxO', 'Toyota Lanka Pvt Ltd', 'toyota@provider.com', '0112345678'),
('provider1', '$2y$12$e0NRWj0tOjRn2E6Hw7qYduA7jCkA2vK7pLzH2S6b7V8xYcTQ6u2QK', 'Test Provider', 'provider1@example.com', '0712345678');

-- Locations
INSERT INTO Locations (city, branch)
VALUES ('Colombo', 'Main Branch'),
       ('Jaffna', 'Sub Branch'),
       ('Kandy', 'Central Branch');

-- Vehicles
INSERT INTO Vehicles (provider_id, location_id, vehicle_type, vehicle_brand, vehicle_model,
                      vehicle_year, seats, fuel_type, transmission, price, vehicle_availability)
VALUES 
(1, 1, 'Car', 'Toyota', 'Corolla', 2022, 5, 'Petrol', 'Automatic', 50.00, 'yes'),
(1, 3, 'SUV', 'Nissan', 'X-Trail', 2021, 7, 'Petrol', 'Automatic', 60.00, 'yes');

-- Users
INSERT INTO Users (username, password, name, email, phone)
VALUES 
('john_doe', '$2y$12$wL5y0O5M1e2kPtFtHwvWGuJ9P0D9oi4AGYjP8L7V8uB6x4Gz1RmQe', 'John Doe', 'john@example.com', '0712345678'),
('jane_smith', '$2y$12$E7YQ7dS5U4/jlXkQe1TbNeDf9YosA2aOasf9qFf2l0O3kHjS1X2vG', 'Jane Smith', 'jane@example.com', '0723456789');

-- Bookings
INSERT INTO Bookings (user_id, vehicle_id, pickup_date, return_date, pickup_location, dropoff_location, total_amount, status)
VALUES 
(1, 1, '2025-09-05', '2025-09-10', 'Colombo Main Branch', 'Kandy Central Branch', 250.00, 'confirmed'),
(2, 2, '2025-09-08', '2025-09-12', 'Kandy Central Branch', 'Colombo Main Branch', 300.00, 'pending');

-- Payments
INSERT INTO Payments (booking_id, amount, method, status)
VALUES 
(1, 250.00, 'Credit Card', 'confirmed'),
(2, 300.00, 'Cash', 'pending');

-- Reviews
INSERT INTO Reviews (vehicle_id, user_id, rating, comment)
VALUES 
(1, 1, 5, 'Excellent car, very clean and smooth ride!'),
(2, 2, 4, 'Good SUV, but fuel consumption was a bit high.');

UPDATE providers
SET password = '$2y$10$pmDtYOHW7fAgBmnXNq2nAu8SRMmye6oJlvY5zuuBis2zPhPs4LqLi'
WHERE username = 'provider1';
