<?php
require_once '../includes/auth.php';
require_once '../includes/admin_auth.php';
require_once '../config/database.php';

requireAdmin();

$admin = getCurrentAdmin();

// Get overall statistics
$stmt = $pdo->query("SELECT 
    COUNT(DISTINCT s.id) as total_students,
    COUNT(DISTINCT c.id) as total_complaints,
    SUM(CASE WHEN c.status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN c.status = 'Under Review' THEN 1 ELSE 0 END) as under_review,
    SUM(CASE WHEN c.status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN c.status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN c.status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    COUNT(DISTINCT f.id) as total_feedback
    FROM students s
    LEFT JOIN complaints c ON s.id = c.student_id
    LEFT JOIN feedback f ON s.id = f.student_id");
$stats = $stmt->fetch();

// Get complaints by status for chart
$status_data = [
    'pending' => $stats['pending'] ?? 0,
    'under_review' => $stats['under_review'] ?? 0,
    'in_progress' => $stats['in_progress'] ?? 0,
    'resolved' => $stats['resolved'] ?? 0,
    'rejected' => $stats['rejected'] ?? 0
];

// Get complaints by category for chart
$stmt = $pdo->query("SELECT cat.name, COUNT(c.id) as count 
    FROM categories cat 
    LEFT JOIN complaints c ON cat.id = c.category_id 
    GROUP BY cat.id, cat.name 
    ORDER BY count DESC 
    LIMIT 5");
$category_data = $stmt->fetchAll();

$category_labels = array_column($category_data, 'name');
$category_values = array_column($category_data, 'count');

// Get recent complaints
$stmt = $pdo->query("SELECT c.*, s.name as student_name, s.student_id as student_reg_id, cat.name as category_name 
    FROM complaints c 
    JOIN students s ON c.student_id = s.id 
    JOIN categories cat ON c.category_id = cat.id 
    ORDER BY c.created_at DESC 
    LIMIT 5");
$recent_complaints = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Complaint Management System</title>
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
                    <a class="nav-link active" href="/admin/dashboard.php">
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
                <h2 class="mb-4">Admin Dashboard</h2>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card primary">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $stats['total_students']; ?></div>
                                    <div class="stat-label">Total Students</div>
                                </div>
                                <i class="bi bi-people stat-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo $stats['total_complaints']; ?></div>
                                    <div class="stat-label">Total Complaints</div>
                                </div>
                                <i class="bi bi-envelope-paper stat-icon text-success"></i>
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
                                    <div class="stat-number"><?php echo $stats['total_feedback']; ?></div>
                                    <div class="stat-label">Total Feedback</div>
                                </div>
                                <i class="bi bi-chat-dots stat-icon text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Breakdown -->
                <div class="row mb-4">
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-warning"><?php echo $stats['pending']; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-info"><?php echo $stats['under_review']; ?></div>
                                <div class="stat-label">Under Review</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-primary"><?php echo $stats['in_progress']; ?></div>
                                <div class="stat-label">In Progress</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-success"><?php echo $stats['resolved']; ?></div>
                                <div class="stat-label">Resolved</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-danger"><?php echo $stats['rejected']; ?></div>
                                <div class="stat-label">Rejected</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-pie-chart"></i> Complaints by Status
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="statusChart" data-chart-data='<?php echo json_encode($status_data); ?>'></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-bar-chart"></i> Complaints by Category
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="categoryChart" data-chart-data='<?php echo json_encode(['labels' => $category_labels, 'values' => $category_values]); ?>'></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Complaints -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history"></i> Recent Complaints</span>
                        <a href="/admin/complaints.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_complaints)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No complaints yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Student</th>
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
                                                <td><?php echo htmlspecialchars($complaint['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                                <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                                                <td><?php echo formatDate($complaint['created_at']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo str_replace(' ', '-', $complaint['status']); ?>">
                                                        <?php echo htmlspecialchars($complaint['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="/admin/complaint-view.php?id=<?php echo $complaint['id']; ?>" 
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
    <script src="/includes/assets/js/script.js"></script>
</body>
</html>
