CREATE DATABASE class_book;
USE booking;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('student', 'faculty', 'admin')
);

CREATE TABLE bookings (
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
);

