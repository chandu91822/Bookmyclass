CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    year ENUM('E1', 'E2', 'E3', 'E4') DEFAULT NULL,
    section ENUM('A', 'B', 'C', 'D', 'E') DEFAULT NULL,
    subject VARCHAR(120) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classrooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(64) NOT NULL UNIQUE,
    capacity INT NOT NULL DEFAULT 60,
    type VARCHAR(60) NOT NULL DEFAULT 'Classroom',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    year ENUM('E1', 'E2', 'E3', 'E4') DEFAULT NULL,
    section ENUM('A', 'B', 'C', 'D', 'E') DEFAULT NULL,
    room_number VARCHAR(64) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    faculty VARCHAR(150) NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    year VARCHAR(20) DEFAULT 'All',
    section VARCHAR(20) DEFAULT 'All',
    type VARCHAR(40) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    year VARCHAR(20) DEFAULT 'All',
    section VARCHAR(20) DEFAULT 'All',
    deadline DATETIME DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_assignments_faculty FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(80) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(150) NOT NULL,
    course_code VARCHAR(80) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO classrooms (room_number, capacity, type, status) VALUES
('GF-1', 60, 'lecture_hall', 'active'),
('GF-2', 60, 'lecture_hall', 'active'),
('GF-3', 60, 'lecture_hall', 'active'),
('GF-4', 60, 'lecture_hall', 'active'),
('GF-5', 60, 'lecture_hall', 'active'),
('GF-6', 60, 'lecture_hall', 'active'),
('GF-7', 60, 'lecture_hall', 'active'),
('GF-8', 60, 'lecture_hall', 'active'),
('GF-9', 60, 'lecture_hall', 'active'),
('GF-10', 60, 'lecture_hall', 'active'),
('Small Seminar Hall', 100, 'seminar_hall', 'active'),
('Big Seminar Hall', 200, 'seminar_hall', 'active');
