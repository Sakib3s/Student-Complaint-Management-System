# Student Complaint & Feedback Management System

A complete web-based complaint and feedback management system for universities, built as a CSE Practicum project.

**University:** RTM Al-KABIR TECHNICAL UNIVERSITY  
**Department:** Computer Science and Engineering (CSE)  
**Student:** Sakib Hasan  
**Student ID:** 0992320005101814

---

## 📋 Project Overview

This system provides a platform for students to submit complaints and feedback about university services, and for administrators to manage and respond to these submissions efficiently. The system features role-based access control, real-time status tracking, and comprehensive reporting.

---

## ✨ Features

### Student Features
- **Registration & Login**: Secure student registration with university ID validation
- **Dashboard**: Personalized dashboard showing complaint statistics and recent activity
- **Complaint Submission**: Submit complaints with attachments (JPG, PNG, PDF, DOC, DOCX)
- **Complaint Tracking**: Track complaint status in real-time
- **Complaint History**: View all submitted complaints with detailed information
- **Admin Responses**: View administrator responses to complaints
- **Feedback System**: Submit general feedback, suggestions, and service feedback with ratings
- **Profile Management**: Update personal information and change password

### Administrator Features
- **Dashboard**: Comprehensive dashboard with statistics and charts
- **Complaint Management**: View, search, filter, and manage all complaints
- **Status Updates**: Update complaint status and add responses
- **Student Management**: View students and activate/deactivate accounts
- **Category Management**: Add, edit, delete, and manage complaint categories
- **Feedback Management**: View and analyze student feedback with ratings
- **Reports**: Visual charts showing complaints by status and category
- **Profile Management**: Update admin profile and change password

---

## 🛠 Technology Stack

### Backend
- **PHP 8+**: Server-side scripting
- **MySQL 8+**: Database management
- **PDO**: Database communication with prepared statements
- **Session-based Authentication**: Secure user authentication

### Frontend
- **HTML5**: Markup
- **CSS3**: Styling
- **Bootstrap 5**: Responsive UI framework
- **Bootstrap Icons**: Icon library
- **Vanilla JavaScript**: Client-side functionality
- **Chart.js**: Data visualization

### Development Environment
- **XAMPP/Apache**: Web server
- **MySQL**: Database server
- **Visual Studio Code**: Code editor

---

## 📦 Requirements

### Software Requirements
- XAMPP (or any PHP/MySQL server)
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Modern web browser (Chrome, Firefox, Edge, Safari)

### Hardware Requirements
- Minimum 2GB RAM
- 500MB free disk space
- Internet connection (for CDN resources)

---

## 🚀 Installation Guide

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP following the installation wizard
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Copy Project Files
1. Copy the entire `student-complaint-management` folder
2. Paste it into `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)
3. Rename the folder to `complaint-system` (optional)

### Step 3: Create Database
1. Open phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click on "New" to create a new database
3. Name the database: `student_complaint_management`
4. Click "Create"

### Step 4: Import Database Tables
1. Select the `student_complaint_management` database
2. Click on "Import" tab
3. Choose the `database.sql` file from the project folder
4. Click "Go" to import

### Step 5: Configure Database Connection
1. Open `config/database.php`
2. Verify the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'student_complaint_management');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
3. Update if your MySQL credentials are different

### Step 6: Access the Application
1. Open your web browser
2. Navigate to: [http://localhost/complaint-system/](http://localhost/complaint-system/)
3. You should see the landing page

---

## 🔑 Demo Login Credentials

### Administrator
- **Email:** admin@rtm.edu
- **Password:** admin123

### Demo Students
All demo students use the same password for testing:

1. **Sakib Hasan**
   - Student ID: 0992320005101814
   - Email: sakib@rtm.edu
   - Password: student123

2. **Rahim Ahmed**
   - Student ID: 0992320005101815
   - Email: rahim@rtm.edu
   - Password: student123

3. **Karim Uddin**
   - Student ID: 0992320005101816
   - Email: karim@rtm.edu
   - Password: student123

4. **Fatima Begum**
   - Student ID: 0992320005101817
   - Email: fatima@rtm.edu
   - Password: student123

5. **Jamal Hossain**
   - Student ID: 0992320005101818
   - Email: jamal@rtm.edu
   - Password: student123

---

## 📁 Project Structure

```
student-complaint-management/
│
├── config/
│   └── database.php          # Database configuration
│
├── includes/
│   ├── auth.php              # General authentication functions
│   ├── student_auth.php      # Student-specific authentication
│   ├── admin_auth.php        # Admin-specific authentication
│   ├── header.php            # HTML header
│   ├── footer.php            # HTML footer
│   └── assets/
│       ├── css/
│       │   └── style.css     # Custom styles
│       ├── js/
│       │   └── script.js     # Custom JavaScript
│       └── uploads/           # File upload directory
│
├── student/
│   ├── dashboard.php         # Student dashboard
│   ├── profile.php           # Student profile
│   ├── submit-complaint.php  # Complaint submission form
│   ├── complaints.php        # Student complaint list
│   ├── complaint-view.php    # Complaint details view
│   ├── feedback.php          # Feedback submission
│   └── logout.php            # Student logout
│
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── login.php             # Admin login
│   ├── students.php          # Student management
│   ├── categories.php        # Category management
│   ├── complaints.php        # Complaint management
│   ├── complaint-view.php    # Complaint details and response
│   ├── feedback.php          # Feedback management
│   ├── profile.php           # Admin profile
│   └── logout.php            # Admin logout
│
├── index.php                 # Landing page
├── login.php                 # General login page
├── register.php              # Student registration
├── database.sql              # Database schema and seed data
└── README.md                 # This file
```

---

## 🎯 How to Use

### For Students

#### Registration
1. Visit the application homepage
2. Click "Register" button
3. Fill in the registration form:
   - Full Name
   - Student ID (must be unique)
   - Email (must be unique)
   - Phone Number
   - Department
   - Password
   - Confirm Password
4. Click "Register"
5. Login with your credentials

#### Submitting a Complaint
1. Login to your student account
2. Navigate to "Submit Complaint"
3. Fill in the complaint details:
   - Subject
   - Category (select from dropdown)
   - Description
   - Attachment (optional - JPG, PNG, PDF, DOC, DOCX)
4. Click "Submit Complaint"
5. Note your complaint tracking number

#### Tracking Complaint Status
1. Go to "My Complaints"
2. View your complaint list with status badges
3. Click "View" to see detailed information
4. Check admin responses if available

#### Submitting Feedback
1. Navigate to "Submit Feedback"
2. Choose feedback type:
   - General Feedback
   - Suggestion
   - Service Feedback
3. Enter subject and message
4. Provide rating (1-5 stars)
5. Click "Submit Feedback"

### For Administrators

#### Admin Login
1. Visit [http://localhost/complaint-system/admin/login.php](http://localhost/complaint-system/admin/login.php)
2. Enter admin credentials
3. Access the admin dashboard

#### Managing Complaints
1. View all complaints from the dashboard or "Complaints" page
2. Use filters to search by:
   - Status (Pending, Under Review, In Progress, Resolved, Rejected)
   - Category
   - Search terms (ID, subject, student name)
3. Click "View" on a complaint to see details
4. Update status and add admin response
5. Click "Update Complaint" to save changes

#### Managing Students
1. Navigate to "Students" page
2. View all registered students
3. Search by name, student ID, or email
4. Activate or deactivate student accounts

#### Managing Categories
1. Go to "Categories" page
2. Add new categories for complaints
3. Edit existing category names
4. Enable or disable categories
5. Delete unused categories

#### Viewing Feedback
1. Navigate to "Feedback" page
2. View all student feedback
3. Filter by type and rating
4. View detailed feedback messages

---

## 🔒 Security Features

### Authentication
- **Password Hashing**: All passwords are hashed using PHP's `password_hash()` function
- **Session Management**: Secure session-based authentication
- **Authorization**: Role-based access control (Student vs Admin)
- **Logout**: Proper session destruction on logout

### Database Security
- **PDO Prepared Statements**: All SQL queries use prepared statements to prevent SQL injection
- **Input Validation**: Server-side validation for all user inputs
- **Output Escaping**: HTML escaping to prevent XSS attacks

### File Upload Security
- **File Type Validation**: Only allowed file types (JPG, PNG, PDF, DOC, DOCX)
- **File Size Limit**: Maximum 5MB file size
- **Unique Filenames**: Generated unique filenames to prevent overwriting
- **Extension Validation**: Double-check file extensions

### CSRF Protection
- CSRF tokens for important POST requests (can be extended)

### Access Control
- Students can only access their own data
- Admins have full access to administrative features
- Unauthorized URL access is prevented

---

## 📊 Database Schema

### Tables

#### `admins`
- id (INT, Primary Key, Auto Increment)
- name (VARCHAR)
- email (VARCHAR, Unique)
- password (VARCHAR, Hashed)
- status (ENUM: active, inactive)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

#### `students`
- id (INT, Primary Key, Auto Increment)
- name (VARCHAR)
- student_id (VARCHAR, Unique)
- email (VARCHAR, Unique)
- phone (VARCHAR)
- department (VARCHAR)
- password (VARCHAR, Hashed)
- status (ENUM: active, inactive)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

#### `categories`
- id (INT, Primary Key, Auto Increment)
- name (VARCHAR, Unique)
- status (ENUM: active, inactive)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

#### `complaints`
- id (INT, Primary Key, Auto Increment)
- complaint_number (VARCHAR, Unique)
- student_id (INT, Foreign Key → students)
- category_id (INT, Foreign Key → categories)
- subject (VARCHAR)
- description (TEXT)
- attachment (VARCHAR)
- status (ENUM: Pending, Under Review, In Progress, Resolved, Rejected)
- admin_response (TEXT)
- responded_by (INT, Foreign Key → admins)
- responded_at (TIMESTAMP)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

#### `feedback`
- id (INT, Primary Key, Auto Increment)
- student_id (INT, Foreign Key → students)
- type (ENUM: General Feedback, Suggestion, Service Feedback)
- subject (VARCHAR)
- message (TEXT)
- rating (INT, 1-5)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

### Relationships
- Each complaint belongs to one student
- Each complaint belongs to one category
- Each complaint can have one admin response
- Each feedback belongs to one student
- Students, categories, and complaints have foreign key relationships

---

## 🎨 UI/UX Features

### Design
- **Responsive Design**: Works on desktop, laptop, tablet, and mobile
- **Bootstrap 5**: Modern, clean UI framework
- **Bootstrap Icons**: Consistent iconography
- **Color Scheme**: Professional blue and white theme
- **Cards**: Organized content display
- **Tables**: Clean, responsive data tables
- **Forms**: User-friendly input forms
- **Alerts**: Contextual feedback messages
- **Badges**: Visual status indicators

### User Experience
- **Intuitive Navigation**: Clear menu structure
- **Quick Actions**: Easy access to common tasks
- **Visual Feedback**: Status badges and color coding
- **Search & Filter**: Powerful search and filtering capabilities
- **Real-time Updates**: Immediate status updates
- **Mobile-Friendly**: Touch-friendly interface on mobile devices

---

## 🧪 Testing Checklist

### Student Functionality
- [ ] Student registration with validation
- [ ] Student login with correct credentials
- [ ] Student login with incorrect credentials
- [ ] Profile update
- [ ] Password change
- [ ] Complaint submission
- [ ] Complaint submission with attachment
- [ ] Complaint list view
- [ ] Complaint detail view
- [ ] Complaint status tracking
- [ ] Feedback submission
- [ ] Logout
- [ ] Unauthorized access prevention

### Admin Functionality
- [ ] Admin login with correct credentials
- [ ] Admin login with incorrect credentials
- [ ] Dashboard statistics display
- [ ] Chart rendering
- [ ] Complaint list view
- [ ] Complaint search and filter
- [ ] Complaint detail view
- [ ] Complaint status update
- [ ] Admin response addition
- [ ] Student list view
- [ ] Student search
- [ ] Student activation/deactivation
- [ ] Category addition
- [ ] Category editing
- [ ] Category deletion
- [ ] Category enable/disable
- [ ] Feedback list view
- [ ] Feedback search and filter
- [ ] Admin profile update
- [ ] Admin password change
- [ ] Logout

### Security Testing
- [ ] SQL injection prevention
- [ ] XSS prevention
- [ ] File upload validation
- [ ] Session management
- [ ] Access control
- [ ] Password hashing

### Responsive Design
- [ ] Desktop view (1920x1080)
- [ ] Laptop view (1366x768)
- [ ] Tablet view (768x1024)
- [ ] Mobile view (375x667)

---

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` to change database settings:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_complaint_management');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### File Upload Configuration
File upload settings can be adjusted in individual PHP files:
- Maximum file size: 5MB
- Allowed types: JPG, JPEG, PNG, PDF, DOC, DOCX
- Upload directory: `includes/assets/uploads/`

### Session Configuration
Session settings are managed by PHP default configuration. To customize:
- Session timeout
- Session cookie parameters
- Session save path

---

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
**Problem:** "Database Connection Failed"
**Solution:**
1. Verify MySQL is running in XAMPP
2. Check database credentials in `config/database.php`
3. Ensure database `student_complaint_management` exists
4. Verify user has proper permissions

#### File Upload Not Working
**Problem:** Files not uploading
**Solution:**
1. Check `includes/assets/uploads/` directory exists
2. Verify directory has write permissions
3. Check PHP upload_max_filesize and post_max_size settings
4. Ensure file type and size validation passes

#### Session Not Working
**Problem:** User logged out automatically
**Solution:**
1. Check PHP session.save_path is writable
2. Verify session cookie settings
3. Check browser cookie settings

#### Charts Not Displaying
**Problem:** Charts not visible on dashboard
**Solution:**
1. Check internet connection (Chart.js loads from CDN)
2. Verify browser supports canvas element
3. Check browser console for JavaScript errors

#### 404 Errors
**Problem:** Pages not found
**Solution:**
1. Verify project is in correct htdocs directory
2. Check URL is correct
3. Ensure Apache is running
4. Check .htaccess if using URL rewriting

---

## 📈 Future Improvements

### Planned Features
- Email notifications for complaint updates
- PDF generation for complaint reports
- Advanced analytics and reporting
- Mobile app (React Native)
- Two-factor authentication
- Multi-language support
- Real-time chat support
- Complaint escalation system
- API for third-party integration
- Backup and restore functionality

### Technical Improvements
- Implement full CSRF protection
- Add rate limiting
- Implement caching for performance
- Add unit and integration tests
- Use Docker for deployment
- Implement logging system
- Add database backup automation

---

## 📝 Project Documentation

### For Practicum Presentation

#### Key Points to Cover
1. **Project Overview**: Purpose and scope
2. **Technology Stack**: Why PHP, MySQL, Bootstrap?
3. **Database Design**: Tables and relationships
4. **User Roles**: Student vs Admin functionality
5. **Security**: Authentication, authorization, input validation
6. **Features**: Complaint submission, tracking, feedback
7. **Challenges**: How problems were solved
8. **Future Scope**: Possible improvements

#### Demonstration Steps
1. Show landing page
2. Demonstrate student registration
3. Show student login and dashboard
4. Submit a sample complaint
5. Show complaint tracking
6. Submit feedback
7. Show admin login
8. Demonstrate admin dashboard
9. Show complaint management
10. Update complaint status and add response
11. Show student viewing admin response
12. Show reports and statistics

---

## 📞 Support

For issues or questions about this project:
- **Student:** Sakib Hasan
- **Student ID:** 0992320005101814
- **Department:** Computer Science and Engineering
- **University:** RTM Al-KABIR TECHNICAL UNIVERSITY

---

## 📄 License

This project is developed as an academic CSE Practicum project. It is available for educational purposes.

---

## 🙏 Acknowledgments

- RTM Al-KABIR TECHNICAL UNIVERSITY
- Department of Computer Science and Engineering
- Project Supervisors and Faculty Members
- Open Source Community (PHP, MySQL, Bootstrap, Chart.js)

---

## 📅 Project Timeline

- **Phase 1**: Planning and Requirements Analysis
- **Phase 2**: Database Design and Implementation
- **Phase 3**: Backend Development
- **Phase 4**: Frontend Development
- **Phase 5**: Testing and Debugging
- **Phase 6**: Documentation and Deployment

---

## 🎯 Conclusion

This Student Complaint & Feedback Management System provides a comprehensive solution for managing student complaints and feedback in a university setting. The system is designed to be user-friendly, secure, and efficient, making it suitable for academic institutions looking to improve their complaint management process.

The project demonstrates practical application of web development concepts including database design, server-side programming, client-side scripting, and security best practices. It serves as a complete practicum project showcasing the skills and knowledge acquired in the Computer Science and Engineering program.

---

**Version:** 1.0  
**Last Updated:** August 2026  
**Status:** Complete ✅
