# Car Rental System

A modern and user-friendly car rental management system built with PHP and MySQL. Designed and developed by Ayush Ghimire.

## About

This Car Rental System is a comprehensive web application that provides an easy-to-use platform for both customers and administrators to manage car rentals efficiently. With a clean and intuitive interface, it simplifies the process of car booking and rental management.

## Features

- **User Features:**
  - User Registration and Login
  - Browse Available Cars
  - Book Cars
  - View Booking History
  - Update Profile
  - Contact Form

- **Admin Features:**
  - Secure Admin Login
  - Dashboard with Statistics
  - Manage Cars (Add, Edit, Delete)
  - Manage Bookings
  - User Management
  - Enquiries Management

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- XAMPP/WAMP/LAMP stack

## Installation

1. Clone the repository to your web server directory:
   ```bash
   git clone https://github.com/yourusername/carrentalphp.git
   ```

2. Create a new MySQL database named 'carrentalp'

3. Import the database:
   - Open phpMyAdmin
   - Select the 'carrentalp' database
   - Go to Import tab
   - Select the file 'DATABASE FILE/new_carrentalp.sql'
   - Click 'Go' to import the database structure and initial data

4. Configure database connection:
   - Open `includes/config.php`
   - Update the database credentials if needed:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "carrentalp";
     ```

5. Access the system:
   - User Interface: `http://localhost/carrentalphp`
   - Admin Interface: `http://localhost/carrentalphp/admin`

## Default Admin Credentials

- Username: admin
- Password: admin123

## Directory Structure

```
carrentalphp/
├── admin/              # Admin panel files
├── assets/            # CSS, JS, and image files
├── DATABASE FILE/     # Database setup file
├── includes/          # PHP includes (config, functions)
└── uploads/           # Car image uploads
```

## Security Features

- Password Hashing
- SQL Injection Prevention
- XSS Protection
- Session Management
- Input Validation

## Contributing

If you'd like to contribute to this project, please feel free to:
1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## Author

**Ayush Ghimire**
- Project Creator and Lead Developer
- GitHub: [@ayushghi4](https://github.com/ayushghi4)

## License

This project is open source and available under the [MIT License](LICENSE).

## Acknowledgments

Special thanks to everyone who has contributed to making this project better!
