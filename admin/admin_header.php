<?php
/**
 * Admin Dashboard Header
 * Contains security headers, session validation, and common includes
 */

// =============================================
// SECURITY HEADERS AND SESSION CONFIGURATION
// =============================================

// Check for HTTPS in a way that works with proxies and all server setups
$isHttps = 
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||  // Standard check
    ($_SERVER['SERVER_PORT'] ?? null) === 443 ||                  // Port-based check
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||  // Cloudflare/Load Balancer
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');           // Alternative proxy header

if ($isHttps && !in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
}

// Generate nonces
$scriptNonce = base64_encode(random_bytes(16));
$styleNonce = base64_encode(random_bytes(16));

// Define CSP
$csp = "default-src 'self'; " .
       "script-src 'self' 'nonce-$scriptNonce' https://cdn.jsdelivr.net; " .
       "style-src 'self' 'nonce-$styleNonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
       "img-src 'self' data:; " .
       "font-src 'self' https://cdnjs.cloudflare.com; " .
       "frame-ancestors 'none'";

// Apply headers
header("Content-Security-Policy: $csp");

// Security headers
$securityHeaders = [
    "X-Frame-Options" => "DENY",
    "X-Content-Type-Options" => "nosniff",
    "X-XSS-Protection" => "1; mode=block",
    "Referrer-Policy" => "strict-origin-when-cross-origin",
    "Content-Security-Policy" => $csp,
    "Permissions-Policy" => "geolocation=(), microphone=(), camera=()"
];

foreach ($securityHeaders as $header => $value) {
    header("$header: $value");
}

// Start secure session
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/..." as="script" crossorigin>
    
    <link rel="stylesheet" href="./css/admin_styles.css">

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
                    <!-- Other nav items... -->
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle" aria-hidden="true"></i> 
                            <span><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>