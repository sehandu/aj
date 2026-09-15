<?php
$servername = '127.0.0.1';
$username = 'root';
$password = 'root';
$dbname = 'event-hub';
$port = 8889;

$conn = @new mysqli($servername, $username, $password, '', $port);

if ($conn->connect_error) {
    $conn = @new mysqli($servername, $username, $password, '', 3306);
}

if ($conn->connect_error) {
    $conn = @new mysqli('localhost', $username, $password, '');
}
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

$conn->query("CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    encripted_password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    profile_pic VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query('CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)');

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

$conn->query('CREATE TABLE IF NOT EXISTS event_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reg (event_id, user_id)
)');

$conn->query('CREATE TABLE IF NOT EXISTS announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)');

?>