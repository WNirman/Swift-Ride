Swift Ride – Vehicle Rental System

A modern and user-friendly vehicle rental management system built with PHP and MySQL. Designed and developed by Team Swift-Ride, this system simplifies car rental management for both customers and administrators.

About

Swift Ride is a comprehensive web application providing an intuitive platform to manage car rentals efficiently. Customers can browse, book, and manage rentals, while administrators can handle inventory, bookings, and user management through a secure dashboard.

Features

User Features:

User registration and login

Browse available cars

Book cars

View booking history

Update profile

Contact form

Admin Features:

Secure admin login

Dashboard with statistics

Manage cars (add, edit, delete)

Manage bookings

User management

Enquiries management

Screenshots

User Interface

Home page with featured vehicles

Car listings with details and pricing

Easy-to-use booking system

User profile management

Admin Interface

Dashboard overview: bookings, users, revenue

Car management interface

Booking tracking and management

User account management

Requirements

PHP 7.4 or higher

MySQL 5.7 or higher

Apache Web Server

XAMPP / WAMP / LAMP stack

Installation

Clone the repository:

git clone https://github.com/yourusername/carrentalphp.git


Create a MySQL database named carrentalp.

Import the database from:
DATABASE FILE/new_carrentalp.sql

Update database credentials in includes/config.php.

Access the system:

User: http://localhost/carrentalphp

Admin: http://localhost/carrentalphp/admin

Default Admin Credentials:

Username: 

Password: 

Tech Stack

Frontend: HTML5, CSS3, JavaScript, Bootstrap

Backend: PHP 7.4+

Database: MySQL 5.7+

Server: Apache

Libraries: Font Awesome, jQuery, Bootstrap

Security

Password hashing with password_hash()

SQL injection prevention

XSS & CSRF protection

Input validation and sanitization

Secure session management

Directory Structure
carrentalphp/
├── admin/              # Admin panel files
├── assets/             # CSS, JS, and image files
├── DATABASE FILE/      # Database setup file
├── includes/           # PHP includes (config, functions)
└── uploads/            # Vehicle image uploads

Contributing

We welcome contributions! See Contributing Guidelines
 for details.

Author

Wanith Nirman – Project Creator & Lead Developer
GitHub: 

License

MIT License – Open source.
