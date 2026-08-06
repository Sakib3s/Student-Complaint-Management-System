-- Student Complaint & Feedback Management System
-- Database: student_complaint_management
-- University: RTM Al-KABIR TECHNICAL UNIVERSITY
-- Department: Computer Science and Engineering (CSE)
-- Student: Sakib Hasan
-- Student ID: 0992320005101814

-- IMPORTANT: Create the database manually in phpMyAdmin first:
-- 1. Open phpMyAdmin
-- 2. Click "New" to create a new database
-- 3. Name: student_complaint_management
-- 4. Collation: utf8mb4_unicode_ci
-- 5. Click "Create"
-- 6. Select the database and import this file

-- Table: admins
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admin_email (email),
    INDEX idx_admin_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    department VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_student_email (email),
    INDEX idx_student_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_name (name),
    INDEX idx_category_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: complaints
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_number VARCHAR(20) NOT NULL UNIQUE,
    student_id INT NOT NULL,
    category_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    attachment VARCHAR(255),
    status ENUM('Pending', 'Under Review', 'In Progress', 'Resolved', 'Rejected') DEFAULT 'Pending',
    admin_response TEXT,
    responded_by INT,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (responded_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_complaint_number (complaint_number),
    INDEX idx_complaint_student (student_id),
    INDEX idx_complaint_category (category_id),
    INDEX idx_complaint_status (status),
    INDEX idx_complaint_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: feedback
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    type ENUM('General Feedback', 'Suggestion', 'Service Feedback') NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_feedback_student (student_id),
    INDEX idx_feedback_type (type),
    INDEX idx_feedback_rating (rating),
    INDEX idx_feedback_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin
-- Email: admin@rtm.edu
-- Password: admin123
INSERT INTO admins (name, email, password, status) VALUES
('Super Admin', 'admin@rtm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active');

-- Insert Default Categories
INSERT INTO categories (name, status) VALUES
('Academic', 'active'),
('Examination', 'active'),
('Library', 'active'),
('Laboratory', 'active'),
('Internet / Network', 'active'),
('Hostel', 'active'),
('Transportation', 'active'),
('Administration', 'active'),
('Other', 'active');

-- Insert Demo Students
-- Password: student123 for all demo students
INSERT INTO students (name, student_id, email, phone, department, password, status) VALUES
('Sakib Hasan', '0992320005101814', 'sakib@rtm.edu', '01712345678', 'Computer Science and Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active'),
('Rahim Ahmed', '0992320005101815', 'rahim@rtm.edu', '01712345679', 'Computer Science and Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active'),
('Karim Uddin', '0992320005101816', 'karim@rtm.edu', '01712345680', 'Electrical Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active'),
('Fatima Begum', '0992320005101817', 'fatima@rtm.edu', '01712345681', 'Computer Science and Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active'),
('Jamal Hossain', '0992320005101818', 'jamal@rtm.edu', '01712345682', 'Mechanical Engineering', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active');

-- Insert Demo Complaints
INSERT INTO complaints (complaint_number, student_id, category_id, subject, description, status, admin_response, responded_by, responded_at) VALUES
('CMP-2026-00001', 1, 1, 'Lab Equipment Issue', 'The computers in Lab-3 are not working properly. Many systems are outdated and slow.', 'Resolved', 'We have upgraded the lab equipment. Please check again.', 1, NOW()),
('CMP-2026-00002', 2, 2, 'Exam Schedule Conflict', 'My CSE-301 and CSE-302 exams are scheduled at the same time.', 'Under Review', NULL, NULL, NULL),
('CMP-2026-00003', 1, 4, 'Projector Not Working', 'The projector in Room-201 is not working for the last 3 days.', 'In Progress', 'Technician has been notified. Will be fixed by tomorrow.', 1, NOW()),
('CMP-2026-00004', 3, 5, 'Slow WiFi in Hostel', 'WiFi connection is very slow in the hostel area during evening hours.', 'Pending', NULL, NULL, NULL),
('CMP-2026-00005', 4, 3, 'Book Not Available', 'Required book "Data Structures" is not available in the library.', 'Resolved', 'New copies have been ordered and will arrive next week.', 1, NOW()),
('CMP-2026-00006', 5, 6, 'Room Cleaning Issue', 'The hostel rooms are not being cleaned regularly.', 'Pending', NULL, NULL, NULL),
('CMP-2026-00007', 2, 7, 'Bus Timing Issue', 'The university bus often arrives late in the morning.', 'Under Review', NULL, NULL, NULL),
('CMP-2026-00008', 1, 8, 'ID Card Renewal', 'Need to renew my student ID card for the new semester.', 'Resolved', 'Please visit the administration office with your photo.', 1, NOW());

-- Insert Demo Feedback
INSERT INTO feedback (student_id, type, subject, message, rating) VALUES
(1, 'Service Feedback', 'Library Service', 'Library staff is very helpful and cooperative.', 5),
(2, 'Suggestion', 'Canteen Improvement', 'Please add more healthy food options in the canteen.', 4),
(3, 'General Feedback', 'Campus Environment', 'The campus environment is very good for studying.', 5),
(4, 'Service Feedback', 'Transportation', 'Bus service needs improvement in timing.', 3),
(1, 'Suggestion', 'Online Resources', 'Please provide more online learning resources.', 4),
(5, 'General Feedback', 'Overall Experience', 'Overall university experience is excellent.', 5);
