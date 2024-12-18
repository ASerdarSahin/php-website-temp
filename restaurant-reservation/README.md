# Restaurant Reservation System

## Overview
The Restaurant Reservation System is a web-based application designed to streamline the process of managing restaurant reservations. It caters to both customers and administrators, providing a seamless experience for booking tables, managing user profiles, and overseeing reservation logistics.

## Features

### User Authentication
- **Registration**: Users can create accounts by providing a username, email, phone number, password, and a secret key for password recovery.
- **Login**: Registered users can log in using their credentials.
- **Password Recovery**: Users can reset their passwords by verifying their secret key.
- **Session Management**: Secure session handling to maintain user authentication status.

### User Profiles
- **Profile Viewing**: Users can view their profile information, including username, email, and phone number.
- **Profile Editing**: Users can update their profile details and change their passwords.
- **Reservation History**: Users can view their past and upcoming reservations with details such as date, time, table ID, and status.

### Reservations
- **Table Selection**: Users can select available tables based on capacity.
- **Time Slot Selection**: Available time slots are fetched dynamically based on the selected table.
- **Booking Confirmation**: Upon successful reservation, users receive a confirmation number.
- **Reservation Management**: Users can cancel active reservations directly from their profile.

### Administrative Functions
- **Admin Panel**: Accessible only to users with the 'admin' role.
  - **User Management**: Admins can view, edit, and manage user roles and messages.
  - **Reservation Management**: View and manage all reservations, including cancellation of reservations.
  - **Statistics**: Access reservation statistics, displaying the number of reservations per date.
  - **Promotion Messages**: Create and manage promotional messages displayed to users.

- **Owner Panel**: Accessible only to users with the 'owner' role.
  - **Reservation Management**: Similar to the admin reservation management features.
  - **Promotion Messages**: Manage promotional content specific to owners.

### Time Slot Management
- **Time Slot Generation**: Administrators can generate time slots for tables within specified dates and operating hours.
- **Dynamic Availability**: The system updates table statuses based on available time slots.

### Contact and Support
- **Contact Us**: Users can access restaurant contact information.
- **Message Handling**: Admins and owners can manage messages associated with user profiles.

### Security
- **Input Validation and Sanitization**: All user inputs are validated and sanitized to prevent SQL injection and other security threats.
- **Password Hashing**: Secure password storage using hashing algorithms.
- **Access Control**: Role-based access control ensuring that only authorized users can access certain functionalities.

### User Interface
- **Responsive Design**: The application is designed to be responsive, ensuring usability.
- **Intuitive Navigation**: Easy-to-navigate interface with clear calls-to-action for booking reservations and managing profiles.

## Technologies Used
- **Backend**: PHP for server-side scripting and MySQL for database management.
- **Frontend**: HTML, CSS, and JavaScript for the user interface and interactivity.
- **Styling**: Custom CSS with responsive design principles.
- **JavaScript**: Used for dynamic fetching of tables and time slots, as well as handling reservation submissions via AJAX.

## Project Structure
- `php/`: Contains all PHP scripts handling backend operations such as user authentication, reservation management, and administrative functions.
- `css/style.css`: Stylesheet for the application's frontend.
- `js/script.js`: JavaScript file managing frontend interactions and AJAX requests.
- `images/`: Directory for storing image assets used in the application.
- `index.php`: The home page of the application.
- `README.md`: Documentation of the project.

## Getting Started
To set up the Restaurant Reservation System locally:
You can use XAMPP (Apache, MySQL) with db.sql file to create the database.
Use generate_time_slots.php to create time slots.

1. **Clone the Repository**
   ```bash
   git clone https://github.com/ASerdarSahin/php-website-temp