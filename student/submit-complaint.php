<?php
require_once '../includes/auth.php';
require_once '../includes/student_auth.php';
require_once '../config/database.php';

requireStudent();

$student = getCurrentStudent();
$errors = [];
$success = false;

// Get active categories
$stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $description = sanitizeInput($_POST['description'] ?? '');
    $attachment = null;
    
    // Validation
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    if (empty($category_id)) {
        $errors[] = 'Category is required';
    }
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    
    // File upload handling
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['attachment'];
        
        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = 'File size must be less than 5MB';
        }
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = 'Invalid file type. Allowed types: JPG, JPEG, PNG, PDF, DOC, DOCX';
        }
        
        // Check file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = 'Invalid file extension';
        }
        
        if (empty($errors)) {
            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = '../includes/assets/uploads/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $attachment = $filename;
            } else {
                $errors[] = 'Failed to upload file';
            }
        }
    }
    
    // Submit complaint
    if (empty($errors)) {
        // Generate complaint number
        $year = date('Y');
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM complaints WHERE YEAR(created_at) = $year");
        $count = $stmt->fetch()['count'] + 1;
        $complaint_number = 'CMP-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO complaints (complaint_number, student_id, category_id, subject, description, attachment, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$complaint_number, $student['id'], $category_id, $subject, $description, $attachment]);
            
            redirectWithMessage('/student/complaints.php', 'Your complaint has been submitted successfully. Complaint Number: ' . $complaint_number, 'success');
        } catch (PDOException $e) {
            $errors[] = 'Failed to submit complaint. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint - Complaint Management System</title>
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
                    <a class="nav-link active" href="/student/submit-complaint.php">
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
                <div class="mb-4">
                    <h2>Submit New Complaint</h2>
                    <p class="text-muted">Fill out the form below to submit your complaint</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-envelope-paper"></i> Complaint Details
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <?php echo $error; ?><br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Attachment (Optional)</label>
                                <div class="file-upload-wrapper">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p class="mb-2">Drag and drop or click to upload</p>
                                    <p class="text-muted small">Allowed types: JPG, JPEG, PNG, PDF, DOC, DOCX (Max 5MB)</p>
                                    <input type="file" class="form-control" id="attachment" name="attachment" 
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Submit Complaint
                                </button>
                                <a href="/student/dashboard.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
