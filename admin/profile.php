<?php
require_once '../includes/auth.php';
require_once '../includes/admin_auth.php';
require_once '../config/database.php';

requireAdmin();

$admin = getCurrentAdmin();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Check if email is being changed and if it's already taken
    if (empty($errors) && $email !== $admin['email']) {
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
        $stmt->execute([$email, $admin['id']]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists';
        }
    }
    
    // Password change validation
    if (!empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = 'Current password is required to change password';
        } elseif (!password_verify($current_password, $admin['password'])) {
            $errors[] = 'Current password is incorrect';
        }
        
        if (empty($new_password)) {
            $errors[] = 'New password is required';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'New password must be at least 6 characters';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match';
        }
    }
    
    // Update profile
    if (empty($errors)) {
        try {
            // Update basic info
            $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $admin['id']]);
            
            // Update password if provided
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $admin['id']]);
            }
            
            // Update session
            $_SESSION['user_name'] = $name;
            
            redirectWithMessage('/admin/profile.php', 'Profile updated successfully!', 'success');
        } catch (PDOException $e) {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
    
    // Reload admin data
    $admin = getCurrentAdmin();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/includes/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg border-bottom border-dark" >
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/admin/dashboard.php">
                <img src="/img/rtm-icon.png" alt="RTM Logo" width="40" height="40" class="me-2">
                Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($admin['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link" href="/admin/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/admin/complaints.php">
                        <i class="bi bi-envelope-paper"></i> Complaints
                    </a>
                    <a class="nav-link" href="/admin/students.php">
                        <i class="bi bi-people"></i> Students
                    </a>
                    <a class="nav-link" href="/admin/categories.php">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    <a class="nav-link" href="/admin/feedback.php">
                        <i class="bi bi-chat-dots"></i> Feedback
                    </a>
                    <a class="nav-link active" href="/admin/profile.php">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a class="nav-link" href="/admin/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 py-4">
                <div class="mb-4">
                    <h2>Admin Profile</h2>
                    <p class="text-muted">Update your profile information</p>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card profile-section text-center">
                            <div class="profile-avatar">
                                <i class="bi bi-shield-lock" style="font-size: 4rem;"></i>
                            </div>
                            <h4><?php echo htmlspecialchars($admin['name']); ?></h4>
                            <p class="text-muted">Administrator</p>
                            <hr>
                            <p><strong>Member Since:</strong></p>
                            <p><?php echo formatDate($admin['created_at']); ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-person-gear"></i> Edit Profile
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error): ?>
                                            <?php echo $error; ?><br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" action="">
                                    <h5 class="mb-3">Personal Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h5 class="mb-3">Change Password (Optional)</h5>
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
