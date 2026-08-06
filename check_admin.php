<?php
/**
 * Admin Account Checker & Reset Tool
 * Run this to verify and reset admin credentials
 */

require_once 'config/database.php';

echo "<h2>Admin Account Checker</h2>";

// Check if admin exists
$stmt = $pdo->prepare("SELECT * FROM admins WHERE email = 'admin@rtm.edu'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    echo "<h3>✅ Admin account found:</h3>";
    echo "<pre>";
    print_r($admin);
    echo "</pre>";
    
    // Test password verification
    $test_password = 'admin123';
    if (password_verify($test_password, $admin['password'])) {
        echo "<h3>✅ Password 'admin123' is CORRECT</h3>";
    } else {
        echo "<h3>❌ Password 'admin123' is INCORRECT</h3>";
        echo "<p>Current password hash: " . $admin['password'] . "</p>";
    }
} else {
    echo "<h3>❌ Admin account NOT found in database</h3>";
}

echo "<hr>";
echo "<h3>Reset Admin Account</h3>";
echo "<form method='POST'>";
echo "<button type='submit' name='reset_admin' class='btn btn-primary'>Reset Admin to admin@rtm.edu / admin123</button>";
echo "</form>";

if (isset($_POST['reset_admin'])) {
    // Delete existing admin
    $stmt = $pdo->prepare("DELETE FROM admins WHERE email = 'admin@rtm.edu'");
    $stmt->execute();
    
    // Insert new admin with known password
    $new_password = 'admin123';
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, status) VALUES (?, ?, ?, 'active')");
    $stmt->execute(['Super Admin', 'admin@rtm.edu', $hashed_password]);
    
    echo "<div class='alert alert-success'>";
    echo "<h3>✅ Admin account reset successfully!</h3>";
    echo "<p><strong>Email:</strong> admin@rtm.edu</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>New Password Hash:</strong> " . $hashed_password . "</p>";
    echo "</div>";
    
    // Verify the reset
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = 'admin@rtm.edu'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if (password_verify('admin123', $admin['password'])) {
        echo "<h3>✅ Password verification PASSED after reset</h3>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Account Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
</body>
</html>
