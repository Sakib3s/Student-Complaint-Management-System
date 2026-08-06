# Student Complaint & Feedback Management System

<div align="center">

![GitHub repo size](https://img.shields.io/github/repo-size/username/student-complaint-management-system)
![GitHub language count](https://img.shields.io/github/languages/count/username/student-complaint-management-system)
![GitHub last commit](https://img.shields.io/github/last-commit/username/student-complaint-management-system)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

A complete web-based complaint and feedback management system for universities.

[Features](#-features) • [Installation](#-installation) • [Contributing](#-contributing)

</div>

---

## 📋 Overview

This system provides a platform for students to submit complaints and feedback about university services, and for administrators to manage and respond to these submissions efficiently. Built as a CSE Practicum project for RTM Al-KABIR Technical University.

---

## ✨ Features

### Student Features
- ✅ Secure registration and login
- ✅ Submit complaints with file attachments
- ✅ Track complaint status in real-time
- ✅ View admin responses
- ✅ Submit feedback with ratings
- ✅ Profile management

### Admin Features
- ✅ Comprehensive dashboard with statistics
- ✅ Manage complaints (view, filter, update status)
- ✅ Add admin responses
- ✅ Manage students and categories
- ✅ View and analyze feedback
- ✅ Visual charts and reports

### Security Features
- ✅ Password hashing with PHP `password_hash()`
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ XSS protection
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ File upload validation

---

## 🛠 Tech Stack

- **Backend:** PHP 8+, MySQL 8+, PDO
- **Frontend:** HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
- **Icons:** Bootstrap Icons
- **Charts:** Chart.js
- **Environment:** XAMPP/Apache

---

## 📦 Installation

### Prerequisites
- XAMPP (or PHP + MySQL)
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Modern web browser

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/student-complaint-management-system.git
cd student-complaint-management-system
```

### Step 2: Configure Database
1. Start Apache and MySQL in XAMPP
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create database: `student_complaint_management`
4. Import `database.sql` file

### Step 3: Configure Connection
Edit `config/database.php` if needed:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_complaint_management');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 4: Access the Application
Open browser: `http://localhost/student-complaint-management-system/`

---

## 🔑 Demo Credentials

### Administrator
- **Email:** admin@rtm.edu
- **Password:** admin123

### Demo Students (Password: student123)
- sakib@rtm.edu (ID: 0992320005101814)
- rahim@rtm.edu (ID: 0992320005101815)
- karim@rtm.edu (ID: 0992320005101816)
- fatima@rtm.edu (ID: 0992320005101817)
- jamal@rtm.edu (ID: 0992320005101818)

---

## 📁 Project Structure

```
student-complaint-management-system/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth.php              # Authentication functions
│   ├── student_auth.php      # Student auth functions
│   ├── admin_auth.php        # Admin auth functions
│   ├── header.php            # HTML header
│   ├── footer.php            # HTML footer
│   └── assets/
│       ├── css/style.css     # Custom styles
│       ├── js/script.js      # Custom JavaScript
│       └── uploads/          # File upload directory
├── student/
│   ├── dashboard.php         # Student dashboard
│   ├── profile.php           # Student profile
│   ├── submit-complaint.php  # Complaint submission
│   ├── complaints.php        # Complaint list
│   ├── complaint-view.php    # Complaint details
│   ├── feedback.php          # Feedback submission
│   └── logout.php            # Student logout
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── login.php             # Admin login
│   ├── students.php          # Student management
│   ├── categories.php        # Category management
│   ├── complaints.php        # Complaint management
│   ├── complaint-view.php    # Complaint details
│   ├── feedback.php          # Feedback management
│   ├── profile.php           # Admin profile
│   └── logout.php            # Admin logout
├── index.php                 # Landing page
├── login.php                 # General login
├── register.php              # Student registration
├── database.sql              # Database schema
└── README.md                 # This file
```

---

## 🗄️ Database Schema

### Tables
- `admins` - Administrator accounts
- `students` - Student accounts
- `categories` - Complaint categories
- `complaints` - Complaint records
- `feedback` - Student feedback

### Key Features
- Foreign key relationships
- Indexed columns for performance
- Proper data types and constraints
- Timestamps for tracking

---

## 🎨 UI/UX

- **Responsive Design:** Works on desktop, tablet, and mobile
- **Modern UI:** Bootstrap 5 with custom styling
- **Brand Colors:** Custom RTM University colors
- **Intuitive Navigation:** Clear menu structure
- **Visual Feedback:** Status badges and color coding
- **Charts:** Data visualization with Chart.js

---

## 🔒 Security

- **Authentication:** Session-based with password hashing
- **Authorization:** Role-based access control
- **SQL Injection:** PDO prepared statements
- **XSS:** Input sanitization and output escaping
- **File Upload:** Type and size validation
- **CSRF:** Token-based protection (can be extended)

---

## 🧪 Testing

### Manual Testing Checklist
- [ ] Student registration and login
- [ ] Complaint submission with files
- [ ] Complaint status tracking
- [ ] Admin response functionality
- [ ] Feedback submission
- [ ] Search and filter operations
- [ ] Responsive design testing
- [ ] Security validation

---

## 🐛 Known Issues

- No known issues at this time

---

## 🚀 Future Improvements

- [ ] Email notifications for complaint updates
- [ ] PDF generation for reports
- [ ] Mobile app (React Native)
- [ ] Two-factor authentication
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
- [ ] API for third-party integration

---

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

---


## 🙏 Acknowledgments

- RTM Al-KABIR Technical University
- Department of Computer Science and Engineering
- Project Supervisors and Faculty Members
- Open Source Community (PHP, MySQL, Bootstrap, Chart.js)

---

<div align="center">

**⭐ If you find this project helpful, please consider giving it a star!**

Made with ❤️ for RTM Al-KABIR Technical University

</div>
