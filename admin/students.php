<?php
require_once '../includes/auth.php';
require_once '../includes/admin_auth.php';
require_once '../config/database.php';

requireAdmin();

$admin = getCurrentAdmin();

// Get search parameter
$search = $_GET['search'] ?? '';

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND (name LIKE ? OR student_id LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get students
$stmt = $pdo->prepare("SELECT * FROM students $where ORDER BY created_at DESC");
$stmt->execute($params);
$students = $stmt->fetchAll();

// Handle status toggle
if (isset($_GET['toggle_status']) && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $new_status = $_GET['toggle_status'] === 'activate' ? 'active' : 'inactive';
    
    try {
        $stmt = $pdo->prepare("UPDATE students SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $student_id]);
        redirectWithMessage('/admin/students.php', 'Student status updated successfully!', 'success');
    } catch (PDOException $e) {
        redirectWithMessage('/admin/students.php', 'Failed to update student status.', 'danger');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin Panel</title>
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
                    <a class="nav-link active" href="/admin/students.php">
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
                <h2 class="mb-4">Manage Students</h2>

                <!-- Search -->
                <div class="search-filter-section">
                    <form method="GET" action="">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by name, student ID, or email..." 
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

                <!-- Students Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-people"></i> Student List
                    </div>
                    <div class="card-body">
                        <?php if (empty($students)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="mt-3 text-muted">No students found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Student ID</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Registered</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($student['department']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $student['status'] === 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo ucfirst($student['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($student['created_at']); ?></td>
                                                <td>
                                                    <?php if ($student['status'] === 'active'): ?>
                                                        <a href="/admin/students.php?toggle_status=deactivate&student_id=<?php echo $student['id']; ?>" 
                                                           class="btn btn-sm btn-outline-warning action-btn"
                                                           onclick="return confirm('Are you sure you want to deactivate this student?')">
                                                            <i class="bi bi-person-x"></i> Deactivate
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="/admin/students.php?toggle_status=activate&student_id=<?php echo $student['id']; ?>" 
                                                           class="btn btn-sm btn-outline-success action-btn"
                                                           onclick="return confirm('Are you sure you want to activate this student?')">
                                                            <i class="bi bi-person-check"></i> Activate
                                                        </a>
                                                    <?php endif; ?>
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
