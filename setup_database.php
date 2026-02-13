<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS book_class";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select database
$conn->select_db("book_class");

// Drop existing tables to ensure clean slate with new schema
$conn->query("DROP TABLE IF EXISTS bookings"); // Drop bookings first due to potential FK (though none defined explicitly in schema.sql yet)
$conn->query("DROP TABLE IF EXISTS users");

// Create users table
$sql_users = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    year ENUM('E1', 'E2', 'E3', 'E4') NULL,
    section ENUM('A', 'B', 'C', 'D', 'E') NULL,
    subject VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Table users created successfully\n";
} else {
    echo "Error creating table users: " . $conn->error . "\n";
}

// Create bookings table
$sql_bookings = "CREATE TABLE bookings (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    name VARCHAR(255) NOT NULL,
    year ENUM('E1', 'E2', 'E3', 'E4') NOT NULL,
    section ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    classroom ENUM('GF-1', 'GF-2', 'GF-3', 'GF-4', 'GF-5', 'GF-6', 'GF-7', 'GF-8', 'GF-9', 'GF-10', 'Small Seminar Hall', 'Big Seminar Hall') NOT NULL,
    subject VARCHAR(100) NOT NULL,
    faculty VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    time_from TIME NOT NULL,
    time_to TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_bookings) === TRUE) {
    echo "Table bookings created successfully\n";
} else {
    echo "Error creating table bookings: " . $conn->error . "\n";
}

$conn->close();
?>
