<?php
require_once '../includes/auth.php';
require_once '../includes/student_auth.php';
require_once '../config/database.php';

requireStudent();

$student = getCurrentStudent();
$complaint_id = $_GET['id'] ?? 0;

// Verify access
if (!canAccessComplaint($complaint_id)) {
    redirectWithMessage('/student/complaints.php', 'You do not have permission to view this complaint.', 'danger');
}

// Get complaint details
$stmt = $pdo->prepare("SELECT c.*, cat.name as category_name, s.name as student_name, s.student_id as student_reg_id,
    s.email as student_email, s.phone as student_phone, s.department as student_department,
    a.name as admin_name
    FROM complaints c 
    JOIN categories cat ON c.category_id = cat.id 
    JOIN students s ON c.student_id = s.id
    LEFT JOIN admins a ON c.responded_by = a.id
    WHERE c.id = ?");
$stmt->execute([$complaint_id]);
$complaint = $stmt->fetch();

if (!$complaint) {
    redirectWithMessage('/student/complaints.php', 'Complaint not found.', 'danger');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint - Complaint Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/includes/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/student/dashboard.php">
                <img src="/img/rtm-icon.png" alt="RTM Logo" width="40" height="40" class="me-2">
                Complaint Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($student['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/student/logout.php">Logout</a>
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
                    <a class="nav-link" href="/student/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/student/submit-complaint.php">
                        <i class="bi bi-plus-circle"></i> Submit Complaint
                    </a>
                    <a class="nav-link active" href="/student/complaints.php">
                        <i class="bi bi-list-ul"></i> My Complaints
                    </a>
                    <a class="nav-link" href="/student/feedback.php">
                        <i class="bi bi-chat-dots"></i> Submit Feedback
                    </a>
                    <a class="nav-link" href="/student/profile.php">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a class="nav-link" href="/student/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Complaint Details</h2>
                    <a href="/student/complaints.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>

                <!-- Complaint Information -->
                <div class="complaint-detail-section">
                    <h4 class="mb-4"><i class="bi bi-info-circle"></i> Complaint Information</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Complaint ID</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['complaint_number']); ?></div>
                            
                            <div class="detail-label">Subject</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['subject']); ?></div>
                            
                            <div class="detail-label">Category</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['category_name']); ?></div>
                            
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="badge badge-<?php echo str_replace(' ', '-', $complaint['status']); ?> fs-6">
                                    <?php echo htmlspecialchars($complaint['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Submission Date</div>
                            <div class="detail-value"><?php echo formatDateTime($complaint['created_at']); ?></div>
                            
                            <div class="detail-label">Last Updated</div>
                            <div class="detail-value"><?php echo formatDateTime($complaint['updated_at']); ?></div>
                            
                            <?php if ($complaint['attachment']): ?>
                                <div class="detail-label">Attachment</div>
                                <div class="detail-value">
                                    <a href="/includes/assets/uploads/<?php echo htmlspecialchars($complaint['attachment']); ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download Attachment
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="detail-label">Description</div>
                    <div class="detail-value"><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></div>
                </div>

                <!-- Admin Response -->
                <div class="complaint-detail-section">
                    <h4 class="mb-4"><i class="bi bi-chat-dots"></i> Administrator Response</h4>
                    
                    <?php if ($complaint['admin_response']): ?>
                        <div class="alert alert-info">
                            <div class="detail-label">Response</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($complaint['admin_response'])); ?></div>
                            
                            <div class="detail-label">Responded By</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['admin_name']); ?></div>
                            
                            <div class="detail-label">Response Date</div>
                            <div class="detail-value"><?php echo formatDateTime($complaint['responded_at']); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-clock"></i> No response has been added yet. Please check back later.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Student Information -->
                <div class="complaint-detail-section">
                    <h4 class="mb-4"><i class="bi bi-person"></i> Your Information</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Name</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_name']); ?></div>
                            
                            <div class="detail-label">Student ID</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_reg_id']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_email']); ?></div>
                            
                            <div class="detail-label">Department</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_department']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
