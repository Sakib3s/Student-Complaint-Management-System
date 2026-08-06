<?php
/**
 * Database Configuration
 * Student Complaint & Feedback Management System
 * 
 * For Docker environments, set these environment variables:
 * - DB_HOST
 * - DB_NAME
 * - DB_USER
 * - DB_PASS
 */

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'db');
define('DB_PASS', getenv('DB_PASS') ?: 'db');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Auto-create tables if they don't exist
    if (!file_exists(__DIR__ . '/.installed')) {
        // Create admins table
        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_admin_email (email),
            INDEX idx_admin_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create students table
        $pdo->exec("CREATE TABLE IF NOT EXISTS students (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create categories table
        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category_name (name),
            INDEX idx_category_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create complaints table
        $pdo->exec("CREATE TABLE IF NOT EXISTS complaints (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create feedback table
        $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Insert default admin if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE email = 'admin@rtm.edu'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute(['Super Admin', 'admin@rtm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']);
        }

        // Insert default categories if not exists
        $default_categories = ['Academic', 'Examination', 'Library', 'Laboratory', 'Internet / Network', 'Hostel', 'Transportation', 'Administration', 'Other'];
        foreach ($default_categories as $category) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
            $stmt->execute([$category]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO categories (name, status) VALUES (?, 'active')");
                $stmt->execute([$category]);
            }
        }

        // Insert demo students if not exists
        $demo_students = [
            ['Sakib Hasan', '0992320005101814', 'sakib@rtm.edu', '01712345678', 'Computer Science and Engineering'],
            ['Rahim Ahmed', '0992320005101815', 'rahim@rtm.edu', '01712345679', 'Computer Science and Engineering'],
            ['Karim Uddin', '0992320005101816', 'karim@rtm.edu', '01712345680', 'Electrical Engineering'],
            ['Fatima Begum', '0992320005101817', 'fatima@rtm.edu', '01712345681', 'Computer Science and Engineering'],
            ['Jamal Hossain', '0992320005101818', 'jamal@rtm.edu', '01712345682', 'Mechanical Engineering']
        ];
        
        foreach ($demo_students as $student) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE email = ?");
            $stmt->execute([$student[2]]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO students (name, student_id, email, phone, department, password, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
                $stmt->execute([$student[0], $student[1], $student[2], $student[3], $student[4], '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']);
            }
        }

        // Mark as installed
        file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
    }
    
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
