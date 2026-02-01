# Swift Ride - Enhanced Car Rental Management System

Swift Ride is a premium, modernized vehicle rental platform designed to provide a seamless experience for customers, vehicle providers, and administrators. This enhanced version features a state-of-the-art UI with glassmorphism, cinematic animations, and robust management capabilities.

## Overview

Developed as a comprehensive solution for automotive rentals, Swift Ride combines technical performance with high-end aesthetics. The system supports multiple vehicle categories including Cars, Bikes, Vans, Bicycles, and Buses, offering a versatile fleet management experience.

## Key Features

### User Interface and Experience
- Professional Landing Page: Features a cinematic hero section with Ken Burns effects and dynamic overlays.
- Interactive Category Cards: Implement 3D tilt effects responding to mouse movements for an engaging browsing experience.
- Featured Fleet: A randomized recommendation engine displaying available high-end vehicles on the homepage.
- Modernized Listings: Clean, glassmorphic vehicle listing pages with advanced filtering by brand, model, and location.
- Responsive Design: Fully optimized for mobile, tablet, and desktop environments.

### Administrative and Provider Management
- Advanced Dashboards: Premium management interfaces for both administrators and vehicle providers.
- Soft Delete System: A robust archival mechanism that allows vehicle removal while preserving critical booking history for financial records.
- Real-time Statistics: Visual data tracking for fleet size, active bookings, and system growth.
- Booking Oversight: Streamlined reservation tracking with status management (Pending, Confirmed, Cancelled).

### Smart Assistance and Engagement
- Swift Guide Chatbot: An integrated travel guide assistant to help users navigate the booking process.
- Multi-tier Feedback: Integrated reviews and feedback system for vehicles and services.
- Enquiry Management: Centralized handling of user queries and contact requests.

## Database Structure

The system uses a relational database named `carrentalp` with the following key tables:

- **admins**: System administration and management accounts.
- **bookings**: Handles vehicle reservations and status tracking.
- **enquiries**: Manages communication via the contact system.
- **feedbacks**: General system and service feedback.
- **locations**: Defines the various service branches and cities.
- **payments**: Tracks financial transactions related to bookings.
- **providers**: Stores information for vehicle owners/providers.
- **reviews**: Detailed vehicle reviews provided by users.
- **users**: Manages customer account information.
- **vehicles**: Comprehensive inventory of all available vehicles.

## Technology Stack

- Frontend: HTML5, Vanilla CSS3 (Custom Design System), JavaScript (ES6+), Bootstrap 5.1.3
- Backend: PHP 7.4+
- Database: MySQL 5.7+
- Icons: Font Awesome 5.15.4
- Typography: Playfair Display, Poppins, Inter (Google Fonts)

## Installation and Setup

### Prerequisites
- PHP 7.4 or later
- MySQL 5.7 or later
- Apache Web Server (recommended via XAMPP or WAMP)

### Setup Steps
1. Clone or download the project files to your web server directory.
2. Create a new MySQL database named `carrentalp`.
3. Import the database schema from the `new_carrentalp.sql` file located in the root directory.
4. Open `includes/config.php` and update the database connection credentials.
5. Access the application via your browser at `http://localhost/car-rental-system-enhanced`.

## License

This project is licensed under the MIT License.
