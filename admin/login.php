<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Redirect logged-in admins
if (isAdmin()) {
    header("Location: /admin/dashboard.php");
    exit();
}

$errors = [];
$debug_info = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug: Show what was submitted
    $debug_info[] = "Submitted email: " . $email;
    $debug_info[] = "Submitted password length: " . strlen($password);
    
    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        // Debug: Check if admin found
        $debug_info[] = "Admin found: " . ($admin ? "YES" : "NO");
        
        if (!$admin) {
            $errors[] = 'Admin account not found or inactive';
            
            // Debug: Check if admin exists at all (even inactive)
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            $any_admin = $stmt->fetch();
            $debug_info[] = "Admin exists (any status): " . ($any_admin ? "YES" : "NO");
            if ($any_admin) {
                $debug_info[] = "Admin status: " . $any_admin['status'];
            }
        } else {
            // Debug: Check password verification
            $debug_info[] = "Stored password hash: " . substr($admin['password'], 0, 20) . "...";
            $debug_info[] = "Password verify result: " . (password_verify($password, $admin['password']) ? "SUCCESS" : "FAILED");
            
            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['user_name'] = $admin['name'];
                
                $debug_info[] = "Session set. Redirecting to dashboard...";
                
                header("Location: /admin/dashboard.php");
                exit();
            } else {
                // Debug: Password mismatch
                $errors[] = 'Invalid email or password';
                error_log("Login failed for email: $email. Password verification failed.");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Complaint Management System</title>
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
            <h3 class="mt-3">Admin Login</h3>
            <p>Access administration panel</p>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($debug_info)): ?>
                <div class="alert alert-info">
                    <strong>Debug Information:</strong><br>
                    <?php foreach ($debug_info as $info): ?>
                        <?php echo $info; ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
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
                
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <div class="text-center mt-3">
                <p><a href="/index.php">Back to Home</a></p>
                <p><a href="/login.php">Student Login</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
