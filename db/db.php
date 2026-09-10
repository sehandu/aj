<?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "event-hub";
$port = 8889;

// Try default MAMP port 8889 first, fallback to 3306 or default socket
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($servername, $username, $password, "", $port);

if ($conn->connect_error) {
    $conn = @new mysqli($servername, $username, $password, "", 3306);
}

if ($conn->connect_error) {
    $conn = @new mysqli("localhost", $username, $password, "");
}

// Check connection to MySQL
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// Table: users
$conn->query("CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    encripted_password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    profile_pic VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Table: categories
$conn->query("CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Table: events
$conn->query("CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    event_time VARCHAR(50) NOT NULL,
    event_venue VARCHAR(200) NOT NULL,
    max_participants INT NOT NULL DEFAULT 100,
    organizer VARCHAR(100) NOT NULL,
    image VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Table: event_registrations
$conn->query("CREATE TABLE IF NOT EXISTS event_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reg (event_id, user_id)
)");

// Table: announcements
$conn->query("CREATE TABLE IF NOT EXISTS announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Seed default Admin user if none exists
$checkAdmin = $conn->query("SELECT * FROM users WHERE role = 'admin'");
if ($checkAdmin && $checkAdmin->num_rows == 0) {
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (full_name, email, encripted_password, role) VALUES ('System Administrator', 'admin@nsbm.ac.lk', '$adminPass', 'admin')");
}

// Seed default Categories if empty
$checkCats = $conn->query("SELECT * FROM categories");
if ($checkCats && $checkCats->num_rows == 0) {
    $defaultCats = [
        ['Academic', 'Academic seminars, lectures, and educational events'],
        ['Workshop', 'Practical workshops, hands-on training, and tech bootcamps'],
        ['Sports', 'Sports tournaments, matches, and fitness activities'],
        ['Social', 'Social gatherings, cultural festivals, and entertainment'],
        ['Club Event', 'Student club activities, meetups, and exhibitions']
    ];
    foreach ($defaultCats as $cat) {
        $name = $cat[0];
        $desc = $cat[1];
        $conn->query("INSERT INTO categories (category_name, description) VALUES ('$name', '$desc')");
    }
}
?>