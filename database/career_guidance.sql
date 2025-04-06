CREATE DATABASE IF NOT EXISTS nsbm_career_guidance;
USE nsbm_career_guidance;

CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    student_email VARCHAR(100) NOT NULL,
    service_type ENUM('counseling', 'resume', 'interview') NOT NULL,
    appointment_date DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cv_uploads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    cv_file_path VARCHAR(255) NOT NULL,
    job_category VARCHAR(50) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE career_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_name VARCHAR(200) NOT NULL,
    location VARCHAR(100) NOT NULL,
    session_date DATE NOT NULL,
    session_time TIME,
    session_type ENUM('ongoing', 'upcoming') NOT NULL,
    details TEXT,
    status ENUM('active', 'cancelled', 'completed') DEFAULT 'active'
);
