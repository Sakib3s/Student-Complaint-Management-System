<?php
require_once '../includes/auth.php';
require_once '../includes/student_auth.php';
require_once '../config/database.php';

requireStudent();

$student = getCurrentStudent();

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = "WHERE c.student_id = ?";
$params = [$student['id']];

if (!empty($status_filter)) {
    $where .= " AND c.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $where .= " AND (c.complaint_number LIKE ? OR c.subject LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get complaints
$stmt = $pdo->prepare("SELECT c.*, cat.name as category_name 
    FROM complaints c 
    JOIN categories cat ON c.category_id = cat.id 
    $where 
    ORDER BY c.created_at DESC");
$stmt->execute($params);
$complaints = $stmt->fetchAll();

// Get status counts
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Under Review' THEN 1 ELSE 0 END) as under_review,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
    FROM complaints WHERE student_id = ?");
$stmt->execute([$student['id']]);
$status_counts = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - Complaint Management System</title>
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
                    <h2>My Complaints</h2>
                    <a href="/student/submit-complaint.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Complaint
                    </a>
                </div>

                <!-- Status Filters -->
                <div class="row mb-4">
                    <div class="col-md-2 mb-2">
                        <a href="/student/complaints.php" class="btn <?php echo empty($status_filter) ? 'btn-primary' : 'btn-outline-primary'; ?> w-100">
                            All (<?php echo $status_counts['total']; ?>)
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="/student/complaints.php?status=Pending" class="btn <?php echo $status_filter === 'Pending' ? 'btn-warning' : 'btn-outline-warning'; ?> w-100">
                            Pending (<?php echo $status_counts['pending']; ?>)
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="/student/complaints.php?status=Under Review" class="btn <?php echo $status_filter === 'Under Review' ? 'btn-info' : 'btn-outline-info'; ?> w-100">
                            Under Review (<?php echo $status_counts['under_review']; ?>)
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="/student/complaints.php?status=In Progress" class="btn <?php echo $status_filter === 'In Progress' ? 'btn-primary' : 'btn-outline-primary'; ?> w-100">
                            In Progress (<?php echo $status_counts['in_progress']; ?>)
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="/student/complaints.php?status=Resolved" class="btn <?php echo $status_filter === 'Resolved' ? 'btn-success' : 'btn-outline-success'; ?> w-100">
                            Resolved (<?php echo $status_counts['resolved']; ?>)
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="/student/complaints.php?status=Rejected" class="btn <?php echo $status_filter === 'Rejected' ? 'btn-danger' : 'btn-outline-danger'; ?> w-100">
                            Rejected (<?php echo $status_counts['rejected']; ?>)
                        </a>
                    </div>
                </div>

                <!-- Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search by complaint ID or subject..." 
                                           value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Complaints Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul"></i> Complaint List
                    </div>
                    <div class="card-body">
                        <?php if (empty($complaints)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="mt-3 text-muted">No complaints found.</p>
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
                                        <?php foreach ($complaints as $complaint): ?>
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
