-- ===============================================
-- Database Schema for Restaurant Reservation System
-- ===============================================

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hashed password
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    secret_key VARCHAR(255), -- Secret key for password recovery
    message TEXT,  -- Optional personal message from the user
    role ENUM('customer', 'admin', 'owner') DEFAULT 'customer'
);

-- Table: restaurants
CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    promotion_message TEXT -- Global promotion message for the restaurant
);

-- Table: tables
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    capacity INT NOT NULL, -- Number of seats
    status ENUM('available', 'reserved', 'unavailable') DEFAULT 'available',
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE  -- Foreign key constraint linking to restaurants table
);

-- Table: reservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    table_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    confirmation_number VARCHAR(10) UNIQUE,
    status ENUM('active', 'canceled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, -- Foreign key linking to users table
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE, -- Foreign key linking to restaurants table
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE -- Foreign key linking to tables table
);

-- Table: time_slots
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT NOT NULL,
    slot_datetime DATETIME NOT NULL,
    status ENUM('available', 'reserved') DEFAULT 'available',
    FOREIGN KEY (table_id) REFERENCES tables(id)  -- Foreign key linking to tables table
);