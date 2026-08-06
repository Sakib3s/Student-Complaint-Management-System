<?php
require_once '../includes/auth.php';
require_once '../includes/admin_auth.php';
require_once '../config/database.php';

requireAdmin();

$admin = getCurrentAdmin();
$complaint_id = $_GET['id'] ?? 0;
$errors = [];

// Get complaint details
$stmt = $pdo->prepare("SELECT c.*, cat.name as category_name, s.name as student_name, s.student_id as student_reg_id,
    s.email as student_email, s.phone as student_phone, s.department as student_department
    FROM complaints c 
    JOIN categories cat ON c.category_id = cat.id 
    JOIN students s ON c.student_id = s.id
    WHERE c.id = ?");
$stmt->execute([$complaint_id]);
$complaint = $stmt->fetch();

if (!$complaint) {
    redirectWithMessage('/admin/complaints.php', 'Complaint not found.', 'danger');
}

// Get categories
$stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
$categories = $stmt->fetchAll();

// Handle status update and response
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? $complaint['status'];
    $admin_response = sanitizeInput($_POST['admin_response'] ?? '');
    
    // Update complaint
    try {
        $stmt = $pdo->prepare("UPDATE complaints SET status = ?, admin_response = ?, responded_by = ?, responded_at = NOW() WHERE id = ?");
        
        if (!empty($admin_response)) {
            $stmt->execute([$status, $admin_response, $admin['id'], $complaint_id]);
        } else {
            $stmt->execute([$status, $complaint['admin_response'], $complaint['responded_by'], $complaint_id]);
        }
        
        redirectWithMessage('/admin/complaint-view.php?id=' . $complaint_id, 'Complaint updated successfully!', 'success');
    } catch (PDOException $e) {
        $errors[] = 'Failed to update complaint. Please try again.';
    }
    
    // Reload complaint data
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint - Admin Panel</title>
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
                    <a class="nav-link active" href="/admin/complaints.php">
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
                    <a class="nav-link" href="/admin/profile.php">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a class="nav-link" href="/admin/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Complaint Details</h2>
                    <a href="/admin/complaints.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <?php echo $error; ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Student Information -->
                <div class="complaint-detail-section">
                    <h4 class="mb-4"><i class="bi bi-person"></i> Student Information</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Name</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_name']); ?></div>
                            
                            <div class="detail-label">Student ID</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_reg_id']); ?></div>
                            
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_email']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_phone']); ?></div>
                            
                            <div class="detail-label">Department</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['student_department']); ?></div>
                        </div>
                    </div>
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

                <!-- Admin Action -->
                <div class="complaint-detail-section">
                    <h4 class="mb-4"><i class="bi bi-gear"></i> Admin Action</h4>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="Pending" <?php echo $complaint['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Under Review" <?php echo $complaint['status'] === 'Under Review' ? 'selected' : ''; ?>>Under Review</option>
                                        <option value="In Progress" <?php echo $complaint['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="Resolved" <?php echo $complaint['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                        <option value="Rejected" <?php echo $complaint['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_response" class="form-label">Admin Response</label>
                            <textarea class="form-control" id="admin_response" name="admin_response" rows="4"><?php echo htmlspecialchars($complaint['admin_response'] ?? ''); ?></textarea>
                            <small class="text-muted">Leave blank to keep existing response</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Complaint
                        </button>
                    </form>
                </div>

                <!-- Previous Response -->
                <?php if ($complaint['admin_response']): ?>
                    <div class="complaint-detail-section">
                        <h4 class="mb-4"><i class="bi bi-chat-dots"></i> Previous Response</h4>
                        
                        <div class="alert alert-info">
                            <div class="detail-label">Response</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($complaint['admin_response'])); ?></div>
                            
                            <div class="detail-label">Responded By</div>
                            <div class="detail-value"><?php echo htmlspecialchars($complaint['admin_name'] ?? 'Unknown'); ?></div>
                            
                            <div class="detail-label">Response Date</div>
                            <div class="detail-value"><?php echo formatDateTime($complaint['responded_at']); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
