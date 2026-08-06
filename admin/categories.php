<?php
require_once '../includes/auth.php';
require_once '../includes/admin_auth.php';
require_once '../config/database.php';

requireAdmin();

$admin = getCurrentAdmin();
$errors = [];

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitizeInput($_POST['name'] ?? '');
    
    if (empty($name)) {
        $errors[] = 'Category name is required';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            redirectWithMessage('/admin/categories.php', 'Category added successfully!', 'success');
        } catch (PDOException $e) {
            $errors[] = 'Category name already exists or failed to add.';
        }
    }
}

// Handle edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'] ?? 0;
    $name = sanitizeInput($_POST['name'] ?? '');
    
    if (empty($name)) {
        $errors[] = 'Category name is required';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $category_id]);
            redirectWithMessage('/admin/categories.php', 'Category updated successfully!', 'success');
        } catch (PDOException $e) {
            $errors[] = 'Category name already exists or failed to update.';
        }
    }
}

// Handle delete category
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $category_id = $_GET['id'];
    
    // Check if category is being used
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM complaints WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $count = $stmt->fetch()['count'];
    
    if ($count > 0) {
        redirectWithMessage('/admin/categories.php', 'Cannot delete category. It is being used by existing complaints.', 'danger');
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        redirectWithMessage('/admin/categories.php', 'Category deleted successfully!', 'success');
    } catch (PDOException $e) {
        redirectWithMessage('/admin/categories.php', 'Failed to delete category.', 'danger');
    }
}

// Handle status toggle
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $new_status = $_GET['toggle_status'] === 'activate' ? 'active' : 'inactive';
    
    try {
        $stmt = $pdo->prepare("UPDATE categories SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $category_id]);
        redirectWithMessage('/admin/categories.php', 'Category status updated successfully!', 'success');
    } catch (PDOException $e) {
        redirectWithMessage('/admin/categories.php', 'Failed to update category status.', 'danger');
    }
}

// Get categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Get category to edit
$edit_category = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_category = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin Panel</title>
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
                    <a class="nav-link active" href="/admin/categories.php">
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
                <h2 class="mb-4">Manage Categories</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <?php echo $error; ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Category Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-plus-circle"></i> <?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?php if ($edit_category): ?>
                                <input type="hidden" name="edit_category" value="1">
                                <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                            <?php else: ?>
                                <input type="hidden" name="add_category" value="1">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Category Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <?php if ($edit_category): ?>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-save"></i> Update Category
                                                </button>
                                                <a href="/admin/categories.php" class="btn btn-secondary">
                                                    <i class="bi bi-x-circle"></i> Cancel
                                                </a>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle"></i> Add Category
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul"></i> Category List
                    </div>
                    <div class="card-body">
                        <?php if (empty($categories)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No categories found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $category['status'] === 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo ucfirst($category['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($category['created_at']); ?></td>
                                                <td>
                                                    <a href="/admin/categories.php?edit=<?php echo $category['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary action-btn">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <?php if ($category['status'] === 'active'): ?>
                                                        <a href="/admin/categories.php?toggle_status=deactivate&id=<?php echo $category['id']; ?>" 
                                                           class="btn btn-sm btn-outline-warning action-btn">
                                                            <i class="bi bi-eye-slash"></i> Disable
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="/admin/categories.php?toggle_status=activate&id=<?php echo $category['id']; ?>" 
                                                           class="btn btn-sm btn-outline-success action-btn">
                                                            <i class="bi bi-eye"></i> Enable
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="/admin/categories.php?delete=<?php echo $category['id']; ?>&id=<?php echo $category['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger action-btn"
                                                       onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="bi bi-trash"></i> Delete
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
