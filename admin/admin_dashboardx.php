<?php
// =============================================
// SECURITY HEADERS AND SESSION CONFIGURATION
// =============================================

// Enforce HTTPS with HSTS header (except on localhost)
if (($_SERVER['HTTPS'] ?? false) && !in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
}

// Define CSP first (before headers array)
$csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
       "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
       "img-src 'self' data:; " .
       "font-src 'self' https://cdnjs.cloudflare.com; " .
       "frame-ancestors 'none'";

// Security headers to protect against common web vulnerabilities
$securityHeaders = [
    "X-Frame-Options" => "DENY",
    "X-Content-Type-Options" => "nosniff",
    "X-XSS-Protection" => "1; mode=block",
    "Referrer-Policy" => "strict-origin-when-cross-origin",
    "Content-Security-Policy" => $csp, // Using the variable here
    "Permissions-Policy" => "geolocation=(), microphone=(), camera=()",
    "X-Debug-Headers-Sent" => implode(', ', headers_list())
];

foreach ($securityHeaders as $header => $value) {
    header("$header: $value");
}

// Start secure session configuration
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Include database configuration
require_once 'config.php';

// =============================================
// AUTHENTICATION AND SESSION VALIDATION
// =============================================

// Enhanced authentication check
$requiredSessionVars = ['authenticated', 'user_id', 'username', 'usertype', 'ip_address', 'user_agent'];
foreach ($requiredSessionVars as $var) {
    if (!isset($_SESSION[$var])) {
        header("Location: index.php?error=unauthorized");
        exit();
    }
}

// Verify admin access
if ($_SESSION['usertype'] !== 'admin') {
    header("Location: index.php?error=insufficient_privileges");
    exit();
}

// Session hijacking protection
if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_destroy();
    header("Location: index.php?error=session_security_breach");
    exit();
}

// Session timeout (30 minutes)
$sessionInactivityLimit = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionInactivityLimit)) {
    session_destroy();
    header("Location: index.php?error=session_timeout");
    exit();
}
$_SESSION['last_activity'] = time();

// Session regeneration for fixation protection
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Log dashboard access (uncomment when logger is available)
// $logger->logActivity($_SESSION['user_id'], 'ADMIN_DASHBOARD_ACCESS', 'Navigation');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Primary School Educational Management System - Admin Dashboard">
    <meta name="author" content="School Administration">
    
    <title>Admin Dashboard | School Management System</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style" crossorigin>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" crossorigin>
    
    <!-- DNS prefetch for CDN domains -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- CSS Resources -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <style>
        :root {
            --transition-speed: 0.3s;
            --card-shadow: 0 4px 6px rgba(0,0,0,0.05);
            --card-shadow-hover: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Enhanced Card Styling */
        .card {
            transition: all var(--transition-speed) ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
            box-shadow: var(--card-shadow);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .card-header {
            font-weight: 600;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            padding: 1rem 1.25rem;
        }
        
        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        
        .card-title {
            margin-bottom: 0.75rem;
        }
        
        .card-text {
            flex-grow: 1;
            margin-bottom: 1rem;
        }
        
        .btn-light {
            background-color: rgba(255,255,255,0.9);
            border: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-light:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Navigation */
        .navbar-brand {
            font-weight: 600;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Welcome section */
        .welcome-section {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Back to top button */
        #topBtn {
            display: none;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
            border: none;
            outline: none;
            background-color: #343a40;
            color: white;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 50%;
            transition: all var(--transition-speed) ease;
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.25rem;
        }
        
        #topBtn:hover {
            background-color: #212529;
            transform: translateY(-2px);
        }
        
        /* Footer */
        footer {
            padding: 20px 0;
            background-color: #343a40;
            margin-top: auto;
        }
        
        /* Loading animation for card clicks */
        .card-loading {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        
        /* Accessibility improvements */
        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .welcome-section {
                padding: 1.5rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php" aria-label="Admin Dashboard Home">
                <i class="fas fa-school me-2" aria-hidden="true"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php" aria-current="page">
                            <i class="fas fa-home" aria-hidden="true"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="TimeTableZone.php">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i> Time Table
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="student_termly_registration_form.php">
                            <i class="fas fa-user-edit" aria-hidden="true"></i> Termly Registration
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle" aria-hidden="true"></i> 
                            <span><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog" aria-hidden="true"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-4">
        <div class="welcome-section">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="display-4">Welcome, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
                    <p class="lead">Use the navigation bar to manage users and other administrative tasks.</p>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <!-- User Management Card -->
            <div class="col">
                <div class="card text-white bg-success h-100">
                    <div class="card-header">
                        <i class="fas fa-user-cog" aria-hidden="true"></i> User Management
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Manage all users</h2>
                        <p class="card-text">Update, edit, or delete users in the system.</p>
                        <a href="manage_users.php" class="btn btn-light">Manage Users</a>
                    </div>
                </div>
            </div>
            
            <!-- Reports Card -->
            <div class="col">
                <div class="card text-white bg-warning h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie" aria-hidden="true"></i> Reports
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Generate reports</h2>
                        <p class="card-text">View and generate all System reports.</p>
                        <a href="reports.php" class="btn btn-light">Reports</a>
                    </div>
                </div>
            </div>

            <!-- Students List Card -->
            <div class="col">
                <div class="card text-white bg-danger h-100">
                    <div class="card-header">
                        <i class="fas fa-user-graduate" aria-hidden="true"></i> Students List
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Students List</h2>
                        <p class="card-text">Show all registered students in the system.</p>
                        <a href="Students_list.php" class="btn btn-light">Students</a>
                    </div>
                </div>
            </div>

            <!-- Add Users Card -->
            <div class="col">
                <div class="card text-white bg-dark h-100">
                    <div class="card-header">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> Add Users
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Add Users</h2>
                        <p class="card-text">Register new Students, Teachers, and parents to the System</p>
                        <a href="add_user.php" class="btn btn-light">Signup</a>
                    </div>
                </div>
            </div>                        
            
            <!-- Teachers List Card -->
            <div class="col">
                <div class="card text-white bg-primary h-100">
                    <div class="card-header">
                        <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> Teachers List
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Teachers List</h2>
                        <p class="card-text">Show all the Teachers that have been registered to the System</p>
                        <a href="Teachers_list.php" class="btn btn-light">Teachers</a>
                    </div>
                </div>
            </div>
            
            <!-- Parents List Card -->
            <div class="col">
                <div class="card text-white bg-secondary h-100">
                    <div class="card-header">
                        <i class="fas fa-user-tie" aria-hidden="true"></i> Parents List
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Parents List</h2>
                        <p class="card-text">Show all the Parents that have been registered to the System</p>
                        <a href="Parents_list.php" class="btn btn-light">Parents</a>
                    </div>
                </div>
            </div>
            
            <!-- Create Classes Card -->
            <div class="col">
                <div class="card text-white bg-info h-100">
                    <div class="card-header">
                        <i class="fas fa-door-open" aria-hidden="true"></i> Create Classes
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Classes and Class rooms</h2>
                        <p class="card-text">Create the various classes with their corresponding classrooms</p>
                        <a href="classes.php" class="btn btn-light">Classes</a>
                    </div>
                </div>
            </div> 
            
            <!-- Create Subjects Card -->
            <div class="col">
                <div class="card text-white bg-success h-100">
                    <div class="card-header">
                        <i class="fas fa-book" aria-hidden="true"></i> Create Subjects
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Subjects</h2>
                        <p class="card-text">Define and manage subjects taught in the school</p>
                        <a href="subjects.php" class="btn btn-light">Subjects</a>
                    </div>
                </div>
            </div>
            
            <!-- Intercom Access Card -->
            <div class="col">
                <div class="card text-white bg-warning h-100">
                    <div class="card-header">
                        <i class="fas fa-comment-dots" aria-hidden="true"></i> Intercom Access
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View System Intercom</h2>
                        <p class="card-text">Access the system intercom platform to monitor stakeholder communication</p>
                        <a href="admin_intercom_hub.php" class="btn btn-light">Intercom</a>
                    </div>
                </div>
            </div>
            
            <!-- Set Assignments Card -->
            <div class="col">
                <div class="card text-white bg-secondary h-100">
                    <div class="card-header">
                        <i class="fas fa-tasks" aria-hidden="true"></i> Set Assignments
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">Create Assignments</h2>
                        <p class="card-text">Generate the Assignments for the students using the System</p>
                        <a href="Assignments.php" class="btn btn-light">Assignments</a>
                    </div>
                </div>
            </div> 
            
            <!-- Manage Uploads Card -->
            <div class="col">
                <div class="card text-white bg-danger h-100">
                    <div class="card-header">
                        <i class="fas fa-upload" aria-hidden="true"></i> Manage Uploads
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">View Uploads</h2>
                        <p class="card-text">View and manage the Uploads in the System</p>
                        <a href="system_uploads.php" class="btn btn-light">Uploads</a>
                    </div>
                </div>
            </div>
            
            <!-- Settings Card -->
            <div class="col">
                <div class="card text-white bg-primary h-100">
                    <div class="card-header">
                        <i class="fas fa-cogs" aria-hidden="true"></i> Settings
                    </div>
                    <div class="card-body">
                        <h2 class="card-title h5">System settings</h2>
                        <p class="card-text">Configure system settings and preferences.</p>
                        <a href="settings.php" class="btn btn-light">Settings</a>
                    </div>
                </div>
            </div>            
        </div>
    </main>

        <!-- Back to Top Button -->
    <button id="topBtn" class="btn btn-dark position-fixed bottom-0 end-0 mb-4 me-4 rounded-circle shadow" 
            aria-label="Back to top" title="Back to top" style="width: 50px; height: 50px; display: none; opacity: 0; transition: opacity 0.3s ease;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-3 mb-md-0">Primary School Educational Management System &copy; <?= date('Y') ?></p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">All rights reserved. <span class="d-none d-lg-inline">|</span> <a href="privacy.php" class="text-white text-decoration-none">Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Resources -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"
            defer></script>
    
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Back to top button functionality
            const topBtn = document.getElementById('topBtn');
            const scrollThreshold = 300;
            const fadeDuration = 300;
            
            // Improved scroll handler with throttling
            let isScrolling;
            window.addEventListener('scroll', function() {
                // Clear our timeout throughout the scroll
                window.clearTimeout(isScrolling);
                
                // Set a timeout to run after scrolling ends
                isScrolling = setTimeout(function() {
                    const shouldShow = window.scrollY > scrollThreshold;
                    
                    // Smooth fade in/out effect
                    if (shouldShow) {
                        topBtn.style.display = 'block';
                        setTimeout(() => { topBtn.style.opacity = '1'; }, 10);
                    } else {
                        topBtn.style.opacity = '0';
                        setTimeout(() => { topBtn.style.display = 'none'; }, fadeDuration);
                    }
                }, 100);
            });

            // Smooth scroll to top
            topBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                // Blur the button after click for better accessibility
                this.blur();
            });

            // Enhanced card loading animation with error handling
            document.querySelectorAll('.card a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Only handle internal links
                    if (this.href && this.href.startsWith(window.location.origin)) {
                        e.preventDefault();
                        const card = this.closest('.card');
                        
                        // Save original content for potential restoration
                        const originalContent = card.innerHTML;
                        
                        // Show loading state
                        card.innerHTML = `
                            <div class="card-body text-center py-4 card-loading">
                                <div class="spinner-border text-light" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Loading content...</p>
                            </div>
                        `;
                        
                        // Simulate navigation (replace with actual fetch if needed)
                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 500);
                        
                        // Fallback in case navigation fails
                        setTimeout(() => {
                            if (window.location.href !== this.href) {
                                card.innerHTML = originalContent;
                                alert('Navigation failed. Please try again.');
                            }
                        }, 3000);
                    }
                });
            });

            // Add keyboard accessibility for cards
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        const link = this.querySelector('a');
                        if (link) {
                            e.preventDefault();
                            link.click();
                        }
                    }
                });
                
                // Make cards focusable
                if (!card.hasAttribute('tabindex')) {
                    card.setAttribute('tabindex', '0');
                }
            });
        });
    </script>
</body>
</html>