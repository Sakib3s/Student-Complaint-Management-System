<?php
/**
 * Student Authentication Functions
 */

require_once __DIR__ . '/auth.php';

// Get current student
function getCurrentStudent() {
    if (isStudent()) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Check if student can access complaint
function canAccessComplaint($complaintId) {
    if (!isStudent()) {
        return false;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT student_id FROM complaints WHERE id = ?");
    $stmt->execute([$complaintId]);
    $complaint = $stmt->fetch();
    
    return $complaint && $complaint['student_id'] == $_SESSION['user_id'];
}
?>
