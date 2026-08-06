<?php
require_once '../includes/auth.php';
require_once '../includes/student_auth.php';
require_once '../config/database.php';

requireStudent();

$student = getCurrentStudent();

// Get complaint statistics
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved
    FROM complaints WHERE student_id = ?");
$stmt->execute([$student['id']]);
$stats = $stmt->fetch();

// Get feedback count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM feedback WHERE student_id = ?");
$stmt->execute([$student['id']]);
$feedback_count = $stmt->fetch()['total'];

// Get recent complaints
$stmt = $pdo->prepare("SELECT c.*, cat.name as category_name 
    FROM complaints c 
    JOIN categories cat ON c.category_id = cat.id 
    WHERE c.student_id = ? 
    ORDER BY c.created_at DESC 
    LIMIT 5");
$stmt->execute([$student['id']]);
$recent_complaints = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Complaint Management System</title>
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
                    <a class="nav-link active" href="/student/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/student/submit-complaint.php">
                        <i class="bi bi-plus-circle"></i> Submit Complaint
                    </a>
                    <a class="nav-link" href="/student/complaints.php">
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
                    <h2>Student Dashboard</h2>
                    <a href="/student/submit-complaint.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Complaint
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card primary">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                                    <div class="stat-label">Total Complaints</div>
                                </div>
                                <i class="bi bi-envelope-paper stat-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card warning">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $stats['pending']; ?></div>
                                    <div class="stat-label">Pending</div>
                                </div>
                                <i class="bi bi-clock stat-icon text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card info">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $stats['in_progress']; ?></div>
                                    <div class="stat-label">In Progress</div>
                                </div>
                                <i class="bi bi-gear stat-icon text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $stats['resolved']; ?></div>
                                    <div class="stat-label">Resolved</div>
                                </div>
                                <i class="bi bi-check-circle stat-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback Count -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $feedback_count; ?></div>
                                    <div class="stat-label">Feedback Submitted</div>
                                </div>
                                <i class="bi bi-chat-dots stat-icon text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Complaints -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history"></i> Recent Complaints</span>
                        <a href="/student/complaints.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_complaints)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="mt-3 text-muted">No complaints submitted yet.</p>
                                <a href="/student/submit-complaint.php" class="btn btn-primary">Submit Your First Complaint</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Subject</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_complaints as $complaint): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($complaint['complaint_number']); ?></td>
                                                <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                                <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                                                <td><?php echo formatDate($complaint['created_at']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo str_replace(' ', '-', $complaint['status']); ?>">
                                                        <?php echo htmlspecialchars($complaint['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="/student/complaint-view.php?id=<?php echo $complaint['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary action-btn">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
