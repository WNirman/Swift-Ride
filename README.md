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

The system uses a relational database architecture with the following table structures:

### Core Tables

#### Table: users
Manages customer account information.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| user_id | int(11) | NO | auto_increment |
| username | varchar(50) | NO | |
| password | varchar(255) | NO | |
| name | varchar(100) | NO | |
| email | varchar(100) | NO | |
| phone | varchar(20) | NO | |
| created_at | timestamp | NO | |

#### Table: providers
Stores information for vehicle owners/providers.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| provider_id | int(11) | NO | auto_increment |
| username | varchar(50) | NO | |
| password | varchar(255) | NO | |
| name | varchar(100) | NO | |
| email | varchar(100) | YES | |
| phone | varchar(20) | YES | |
| created_at | timestamp | NO | |
| total_earnings | decimal(10,2) | NO | |

#### Table: vehicles
Comprehensive inventory of all available vehicles.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| vehicle_id | int(11) | NO | auto_increment |
| provider_id | int(11) | NO | |
| location_id | int(11) | NO | |
| vehicle_type | varchar(50) | NO | |
| vehicle_brand | varchar(100) | NO | |
| vehicle_model | varchar(200) | NO | |
| vehicle_year | int(11) | NO | |
| seats | int(11) | NO | |
| fuel_type | varchar(50) | NO | |
| transmission | varchar(50) | NO | |
| price | decimal(10,2) | NO | |
| vehicle_availability | enum('yes','no') | NO | |
| vehicle_image | varchar(255) | YES | |
| avg_rating | decimal(3,2) | NO | |
| is_deleted | tinyint(1) | YES | |

### Operations and Logistics

#### Table: bookings
Handles vehicle reservations and status tracking.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| booking_id | int(11) | NO | auto_increment |
| user_id | int(11) | NO | |
| vehicle_id | int(11) | NO | |
| pickup_date | date | NO | |
| return_date | date | NO | |
| pickup_location | varchar(255) | NO | |
| dropoff_location | varchar(255) | NO | |
| total_amount | decimal(10,2) | NO | |
| booking_date | datetime | NO | |
| created_at | timestamp | NO | |
| status | enum('pending','confirmed','cancelled','completed') | NO | |
| feedback_given | enum('yes','no') | NO | |

#### Table: payments
Tracks financial transactions related to bookings.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| payment_id | int(11) | NO | auto_increment |
| booking_id | int(11) | NO | |
| amount | decimal(10,2) | NO | |
| method | varchar(100) | NO | |
| status | enum('pending','confirmed','cancelled') | NO | |

#### Table: locations
Defines the various service branches and cities.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| location_id | int(11) | NO | auto_increment |
| city | varchar(100) | NO | |
| branch | varchar(200) | NO | |

### Communication and Feedback

#### Table: reviews
Detailed vehicle reviews provided by users.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| review_id | int(11) | NO | auto_increment |
| booking_id | int(11) | YES | |
| vehicle_id | int(11) | NO | |
| user_id | int(11) | NO | |
| rating | int(11) | YES | |
| comment | varchar(500) | YES | |

#### Table: feedbacks
General system and service feedback.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| feedback_id | int(11) | NO | auto_increment |
| booking_id | int(11) | NO | |
| user_id | int(11) | NO | |
| rating | int(11) | NO | |
| comment | text | NO | |
| created_at | timestamp | NO | |

#### Table: enquiries
Manages communication via the contact system.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| id | int(11) | NO | auto_increment |
| name | varchar(100) | NO | |
| email | varchar(100) | NO | |
| subject | varchar(200) | NO | |
| message | text | NO | |
| created_at | timestamp | NO | |

#### Table: admins
System administration and management accounts.
| Column | Type | Null | Extra |
| --- | --- | --- | --- |
| admin_id | int(11) | NO | auto_increment |
| username | varchar(50) | NO | |
| password | varchar(255) | NO | |
| name | varchar(100) | NO | |
| email | varchar(100) | NO | |
| phone | varchar(20) | NO | |
| role | enum('superadmin','manager') | YES | |
| created_at | timestamp | NO | |

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
