<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

// Redirect logged-in users
if (isLoggedIn()) {
    if (isStudent()) {
        header("Location: /student/dashboard.php");
        exit();
    } elseif (isAdmin()) {
        header("Location: /admin/dashboard.php");
        exit();
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $student_id = sanitizeInput($_POST['student_id'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $department = sanitizeInput($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Full name is required';
    }
    if (empty($student_id)) {
        $errors[] = 'Student ID is required';
    }
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    if (empty($department)) {
        $errors[] = 'Department is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    // Check if student ID already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Student ID already exists';
        }
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists';
        }
    }
    
    // Register student
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO students (name, student_id, email, phone, department, password) VALUES (?, ?, ?, ?, ?, ?)");
        
        try {
            $stmt->execute([$name, $student_id, $email, $phone, $department, $hashed_password]);
            redirectWithMessage('/login.php', 'Registration successful! Please login with your credentials.', 'success');
        } catch (PDOException $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Student Complaint Management System</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/img/rtm-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/includes/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-container">
    <div class="login-card">
        <div class="login-header">
            <img src="/img/rtm-icon.png" alt="RTM Logo" width="80" height="80" class="mb-3">
            <h3 class="mt-3">Student Registration</h3>
            <p>Create your account</p>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <?php echo $error; ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        <input type="text" class="form-control" id="student_id" name="student_id" 
                               value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <select class="form-select" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Computer Science and Engineering" <?php echo (($_POST['department'] ?? '') === 'Computer Science and Engineering') ? 'selected' : ''; ?>>Computer Science and Engineering</option>
                            <option value="Electrical Engineering" <?php echo (($_POST['department'] ?? '') === 'Electrical Engineering') ? 'selected' : ''; ?>>Electrical Engineering</option>
                            <option value="Mechanical Engineering" <?php echo (($_POST['department'] ?? '') === 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                            <option value="Civil Engineering" <?php echo (($_POST['department'] ?? '') === 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
                            <option value="Textile Engineering" <?php echo (($_POST['department'] ?? '') === 'Textile Engineering') ? 'selected' : ''; ?>>Textile Engineering</option>
                            <option value="Other" <?php echo (($_POST['department'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Already have an account? <a href="/login.php">Login here</a></p>
                <p><a href="/index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
