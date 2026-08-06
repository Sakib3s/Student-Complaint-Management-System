<?php
require_once '../includes/auth.php';
require_once '../includes/admin_auth.php';
require_once '../config/database.php';

requireAdmin();

$admin = getCurrentAdmin();

// Get filter parameters
$type_filter = $_GET['type'] ?? '';
$rating_filter = $_GET['rating'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($type_filter)) {
    $where .= " AND f.type = ?";
    $params[] = $type_filter;
}

if (!empty($rating_filter)) {
    $where .= " AND f.rating = ?";
    $params[] = $rating_filter;
}

if (!empty($search)) {
    $where .= " AND (f.subject LIKE ? OR f.message LIKE ? OR s.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get feedback
$stmt = $pdo->prepare("SELECT f.*, s.name as student_name, s.student_id as student_reg_id 
    FROM feedback f 
    JOIN students s ON f.student_id = s.id 
    $where 
    ORDER BY f.created_at DESC");
$stmt->execute($params);
$feedback_list = $stmt->fetchAll();

// Get rating counts
$stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
    FROM feedback");
$rating_counts = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - Admin Panel</title>
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
                    <a class="nav-link active" href="/admin/feedback.php">
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
                <h2 class="mb-4">Manage Feedback</h2>

                <!-- Rating Summary -->
                <div class="row mb-4">
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number"><?php echo $rating_counts['total']; ?></div>
                                <div class="stat-label">Total</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-warning">5★</div>
                                <div class="stat-label"><?php echo $rating_counts['five_star']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-info">4★</div>
                                <div class="stat-label"><?php echo $rating_counts['four_star']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-primary">3★</div>
                                <div class="stat-label"><?php echo $rating_counts['three_star']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-secondary">2★</div>
                                <div class="stat-label"><?php echo $rating_counts['two_star']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-number text-danger">1★</div>
                                <div class="stat-label"><?php echo $rating_counts['one_star']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-section">
                    <form method="GET" action="">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by subject, message, or student..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-select" name="type">
                                    <option value="">All Types</option>
                                    <option value="General Feedback" <?php echo $type_filter === 'General Feedback' ? 'selected' : ''; ?>>General Feedback</option>
                                    <option value="Suggestion" <?php echo $type_filter === 'Suggestion' ? 'selected' : ''; ?>>Suggestion</option>
                                    <option value="Service Feedback" <?php echo $type_filter === 'Service Feedback' ? 'selected' : ''; ?>>Service Feedback</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-select" name="rating">
                                    <option value="">All Ratings</option>
                                    <option value="5" <?php echo $rating_filter === '5' ? 'selected' : ''; ?>>5 Stars</option>
                                    <option value="4" <?php echo $rating_filter === '4' ? 'selected' : ''; ?>>4 Stars</option>
                                    <option value="3" <?php echo $rating_filter === '3' ? 'selected' : ''; ?>>3 Stars</option>
                                    <option value="2" <?php echo $rating_filter === '2' ? 'selected' : ''; ?>>2 Stars</option>
                                    <option value="1" <?php echo $rating_filter === '1' ? 'selected' : ''; ?>>1 Star</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Feedback Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-chat-dots"></i> Feedback List
                    </div>
                    <div class="card-body">
                        <?php if (empty($feedback_list)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="mt-3 text-muted">No feedback found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Type</th>
                                            <th>Subject</th>
                                            <th>Rating</th>
                                            <th>Date</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($feedback_list as $feedback): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($feedback['student_name']); ?><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($feedback['student_reg_id']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($feedback['type']); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['subject']); ?></td>
                                                <td>
                                                    <span class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="bi bi-star<?php echo $i <= $feedback['rating'] ? '-fill' : ''; ?>"></i>
                                                        <?php endfor; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($feedback['created_at']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#message-<?php echo $feedback['id']; ?>">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                    <div class="collapse mt-2" id="message-<?php echo $feedback['id']; ?>">
                                                        <div class="card card-body">
                                                            <?php echo nl2br(htmlspecialchars($feedback['message'])); ?>
                                                        </div>
                                                    </div>
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
