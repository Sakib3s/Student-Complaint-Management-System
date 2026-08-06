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
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Check student login
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $student = $stmt->fetch();
        
        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['user_id'] = $student['id'];
            $_SESSION['user_type'] = 'student';
            $_SESSION['user_name'] = $student['name'];
            header("Location: /student/dashboard.php");
            exit();
        }
        
        // Check admin login
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['user_name'] = $admin['name'];
            header("Location: /admin/dashboard.php");
            exit();
        }
        
        $errors[] = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Complaint Management System</title>
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
            <h3 class="mt-3">Welcome Back</h3>
            <p>Login to your account</p>
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
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="/register.php">Register here</a></p>
                <p><a href="/index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
