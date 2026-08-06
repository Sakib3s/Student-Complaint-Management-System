<?php
/**
 * Admin Authentication Functions
 */

require_once __DIR__ . '/auth.php';

// Get current admin
function getCurrentAdmin() {
    if (isAdmin()) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}
?>
