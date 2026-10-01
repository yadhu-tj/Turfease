# TurfEase

TurfEase is a PHP-based online turf booking system designed for booking sports courts and managing turf reservations. This project was created as a BCA-level web application to simulate a real-world sports venue booking platform.

## Overview

The application allows users to:
- create an account and log in securely,
- browse available sports and court types,
- select a date and time slot,
- make a booking request,
- view their booking details,
- manage their bookings and payments,
- log out securely.

The system also includes an admin section for managing users and bookings.

## Features

### User Features
- User registration and login
- Password hashing for secure authentication
- Sports booking form
- Court and time-slot selection
- Booking amount calculation based on selected court and slot
- Payment flow and confirmation
- Booking history and detail view
- Cancellation handling

### Admin Features
- Admin login (default demo admin email is configured in the app)
- User management
- Booking overview
- Admin dashboard for operations

## Tech Stack

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- Apache / XAMPP / WAMP server

## Project Structure

```text
Turfease/
├── assets/
│   ├── css/
│   ├── img/
│   └── js/
├── includes/
│   └── dbconnection.php
├── about.php
├── admin_home.php
├── allbookings.php
├── appointment.php
├── book.php
├── bookingdtl.php
├── cancel_booking.php
├── confirm_booking.php
├── confirm_booking.html
├── contact.php
├── delete_user.php
├── demoadmin.php
├── get_booked_slots.php
├── home.php
├── index.php
├── logout.php
├── payment.php
├── paymentdtls.php
├── receipt.php
├── signup.php
├── slotReservation.php
├── success.php
├── try.html
├── userManagement.php
├── .gitignore
└── README.md
```

## Database Setup

This project uses a MySQL database named `turfdb` and connects through the file:

- `includes/dbconnection.php`

The connection is configured as:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "turfdb";
```

### Required Database Tables

Before running the app, create a MySQL database named `turfdb` and make sure the tables used by the application exist. The project logic references a `signup` table for user accounts and booking-related data for reservations.

Example structure for the user table:

```sql
CREATE DATABASE turfdb;

USE turfdb;

CREATE TABLE signup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
);
```

If your version of the project includes additional booking or admin tables, create them as needed to match the application logic.

## Run Locally

### Prerequisites
- XAMPP / WAMP / MAMP installed
- Apache and MySQL running
- PHP enabled

### Steps
1. Clone the repository:

```bash
git clone https://github.com/yadhu-tj/Turfease.git
```

2. Move the project into your local web server directory:
   - For XAMPP: `C:/xampp/htdocs/Turfease`
   - For WAMP: `C:/wamp64/www/Turfease`

3. Start Apache and MySQL.

4. Create the database `turfdb` in phpMyAdmin.

5. Open the app in your browser:

```text
http://localhost/Turfease/index.php
```

## Login Details

The app checks for an admin email in the login flow:

```php
if ($email == 'admin@example.com') {
    $role = 'admin';
}
```

So the default admin demo login can use:

- Email: `admin@example.com`
- Password: the admin password you set during signup / your configured flow

## How It Works

1. A user lands on the landing page and signs up or logs in.
2. After login, the user is redirected to the home screen.
3. The user selects a sport, court, date, and available time slots.
4. The app validates the booking request and calculates the cost.
5. The user proceeds to payment and confirmation.
6. Admins can manage the overall booking process and users.

## Screens and Pages

- `index.php` – login/signup landing page
- `home.php` – user dashboard
- `book.php` – booking form
- `payment.php` – payment summary
- `bookingdtl.php` – booking details
- `admin_home.php` – admin dashboard
- `userManagement.php` – user management panel

## Notes

This project is a student-level web application intended for learning and demonstration. It is a good example of a PHP + MySQL based booking platform with front-end HTML/CSS/JavaScript and backend database interaction.

## Future Improvements

- Add secure admin authentication
- Improve validation and error handling
- Add database migration scripts
- Add real payment gateway integration
- Add email notifications
- Add responsive design improvements
- Add QR code or confirmation ticket generation

## License

This project is provided for educational purposes and is not currently published under a formal open-source license.

## Author

- yadhu-tj

## Repository

https://github.com/yadhu-tj/Turfease
