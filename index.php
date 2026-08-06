<?php
require_once 'includes/auth.php';

// Redirect logged-in users
if (isLoggedIn()) {
    if (isStudent()) {
        header("Location: /student/dashboard.php");
        exit();
    } elseif (isAdmin()) {
        header("Location: /admin/dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Complaint Management System - RTM Al-KABIR Technical University</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/img/rtm-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/includes/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/index.php">
                <img src="/img/rtm-icon.png" alt="RTM Logo" width="45" height="45" class="me-2">
                RTM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="/register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Spacer for fixed navbar -->
    <div style="height: 80px;"></div>

    <!-- Hero Section -->
    <div class="university-header">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1>RTM Al-KABIR Technical University</h1>
                    <p class="lead mb-2">Student Complaint & Feedback Management System</p>
                    <p class="lead">Department of Computer Science and Engineering</p>
                    <div class="mt-4">
                        <a href="/register.php" class="btn btn-light btn-lg me-3 px-4">Get Started</a>
                        <a href="/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold" style="color: var(--secondary-color);">Our Services</h2>
                <p class="text-muted">Everything you need to manage complaints and feedback</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="bi bi-envelope-paper"></i>
                        </div>
                        <h4 class="card-title">Submit Complaints</h4>
                        <p class="card-text">Easily submit complaints about academic, administrative, or facility issues with attachments.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4 class="card-title">Track Status</h4>
                        <p class="card-text">Track the status of your complaints in real-time and view admin responses.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h4 class="card-title">Provide Feedback</h4>
                        <p class="card-text">Share your feedback and suggestions to help improve university services.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="how-it-works">
        <div class="container">
            <div class="text-center">
                <h2>How It Works</h2>
                <p class="text-muted mb-5">Simple steps to get started</p>
            </div>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h5>Register</h5>
                        <p>Create your student account with your university ID.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h5>Submit Complaint</h5>
                        <p>Fill out the complaint form with details and attachments.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h5>Track Progress</h5>
                        <p>Monitor your complaint status and receive admin responses.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h5>Get Resolution</h5>
                        <p>Receive timely resolution and provide feedback on the service.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section">
        <div class="container text-center">
            <h2>Ready to Get Started?</h2>
            <p class="lead">Register now to submit your complaints and feedback</p>
            <a href="/register.php" class="btn btn-light btn-lg me-3">Register Now</a>
            <a href="/login.php" class="btn btn-outline-light btn-lg">Login</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="/img/rtm-icon.png" alt="RTM Logo" width="40" height="40" class="me-2">
                        <h5 class="mb-0">RTM Al-KABIR Technical University</h5>
                    </div>
                    <p class="text-muted">Department of Computer Science and Engineering</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">&copy; 2026 Student Complaint Management System</p>
                    <p class="mb-0">Developed by: Sakib Hasan (ID: 0992320005101814)</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
