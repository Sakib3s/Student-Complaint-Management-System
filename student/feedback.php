<?php
require_once '../includes/auth.php';
require_once '../includes/student_auth.php';
require_once '../config/database.php';

requireStudent();

$student = getCurrentStudent();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = sanitizeInput($_POST['type'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $rating = $_POST['rating'] ?? 5;
    
    // Validation
    if (empty($type)) {
        $errors[] = 'Feedback type is required';
    }
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Rating must be between 1 and 5';
    }
    
    // Submit feedback
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (student_id, type, subject, message, rating) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student['id'], $type, $subject, $message, $rating]);
            
            redirectWithMessage('/student/feedback.php', 'Thank you for your feedback!', 'success');
        } catch (PDOException $e) {
            $errors[] = 'Failed to submit feedback. Please try again.';
        }
    }
}

// Get feedback history
$stmt = $pdo->prepare("SELECT * FROM feedback WHERE student_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$student['id']]);
$feedback_history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback - Complaint Management System</title>
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
                    <a class="nav-link" href="/student/complaints.php">
                        <i class="bi bi-list-ul"></i> My Complaints
                    </a>
                    <a class="nav-link active" href="/student/feedback.php">
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
                    <h2>Submit Feedback</h2>
                    <p class="text-muted">Share your feedback and suggestions with us</p>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-chat-dots"></i> Feedback Form
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error): ?>
                                            <?php echo $error; ?><br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Feedback Type <span class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="General Feedback" <?php echo (($_POST['type'] ?? '') === 'General Feedback') ? 'selected' : ''; ?>>General Feedback</option>
                                            <option value="Suggestion" <?php echo (($_POST['type'] ?? '') === 'Suggestion') ? 'selected' : ''; ?>>Suggestion</option>
                                            <option value="Service Feedback" <?php echo (($_POST['type'] ?? '') === 'Service Feedback') ? 'selected' : ''; ?>>Service Feedback</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="subject" name="subject" 
                                               value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?php echo $i <= ($_POST['rating'] ?? 5) ? '-fill' : ''; ?> star-icon" 
                                                   data-rating="<?php echo $i; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="range" class="form-range mt-2" id="rating" name="rating" 
                                               min="1" max="5" value="<?php echo $_POST['rating'] ?? 5; ?>">
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>Poor</span>
                                            <span>Excellent</span>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Submit Feedback
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-clock-history"></i> Recent Feedback
                            </div>
                            <div class="card-body">
                                <?php if (empty($feedback_history)): ?>
                                    <p class="text-muted">No feedback submitted yet.</p>
                                <?php else: ?>
                                    <?php foreach ($feedback_history as $feedback): ?>
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex justify-content-between">
                                                <strong><?php echo htmlspecialchars($feedback['subject']); ?></strong>
                                                <span class="rating-stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="bi bi-star<?php echo $i <= $feedback['rating'] ? '-fill' : ''; ?>"></i>
                                                    <?php endfor; ?>
                                                </span>
                                            </div>
                                            <small class="text-muted"><?php echo formatDate($feedback['created_at']); ?></small>
                                            <p class="small mt-1 mb-0"><?php echo htmlspecialchars(substr($feedback['message'], 0, 100)); ?>...</p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update stars based on rating
        const ratingInput = document.getElementById('rating');
        const stars = document.querySelectorAll('.star-icon');
        
        ratingInput.addEventListener('input', function() {
            stars.forEach((star, index) => {
                if (index < this.value) {
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            });
        });
        
        // Click on stars to set rating
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                ratingInput.value = index + 1;
                ratingInput.dispatchEvent(new Event('input'));
            });
        });
    </script>
</body>
</html>
