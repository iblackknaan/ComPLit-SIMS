<?php
/**
 * User Authentication Handler
 * 
 * Processes login requests and establishes authenticated sessions
 * with CSRF protection and secure session management.
 */

declare(strict_types=1);

// Initialize secure session
session_start([
    'cookie_secure' => false,    // Enable in production with HTTPS
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_samesite' => 'Strict'
]);

require __DIR__ . '/config.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_error('Invalid request method');
}

// Verify CSRF token
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    redirect_with_error('Security token mismatch');
}

// Process and validate user input
[$username, $password, $usertype] = validate_user_input(
    $_POST['username'] ?? '',
    $_POST['password'] ?? '',
    $_POST['usertype'] ?? ''
);

// Authenticate user
try {
    $user = authenticate_user($username, $password, $usertype);
    establish_session($user, $usertype);
    redirect_to_dashboard($usertype);
    
} catch (PDOException $e) {
    error_log('Authentication Error: ' . $e->getMessage());
    redirect_with_error('System error');
}

/**
 * Validates CSRF token against session token
 */
function validate_csrf_token(string $token): bool 
{
    return !empty($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validates and sanitizes user input
 */
function validate_user_input(
    string $username, 
    string $password, 
    string $usertype
): array {
    $username = trim(htmlspecialchars($username));
    $password = trim($password);
    $usertype = htmlspecialchars($usertype);

    if (empty($username) || empty($password) || empty($usertype)) {
        redirect_with_error('All fields are required');
    }

    return [$username, $password, $usertype];
}

/**
 * Authenticates user against database
 */
function authenticate_user(
    string $username, 
    string $password, 
    string $usertype
): array {
    global $pdo;

    $config = [
        'admin' => ['table' => 'admins', 'id_field' => 'AdminID'],
        'teacher' => ['table' => 'teachers', 'id_field' => 'TeacherID'],
        'student' => ['table' => 'students', 'id_field' => 'StudentID'],
        'parent' => ['table' => 'parents', 'id_field' => 'ParentID']
    ][$usertype] ?? null;

    if (!$config) {
        redirect_with_error('Invalid user type');
    }

    $stmt = $pdo->prepare(
        "SELECT *, {$config['id_field']} as user_id 
         FROM {$config['table']} 
         WHERE Username = ? OR Email = ?"
    );
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['Password'])) {
        redirect_with_error('Invalid credentials');
    }

    return $user;
}

/**
 * Establishes secure session after successful authentication
 */
function establish_session(array $user, string $usertype): void
{
    session_regenerate_id(true);
    
    $_SESSION = [
        'authenticated' => true,
        'user_id' => $user['user_id'],
        'username' => $user['Username'],
        'usertype' => $usertype,
        'last_activity' => time(),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'csrf_token' => bin2hex(random_bytes(32))
    ];
}

/**
 * Redirects to appropriate dashboard
 */
function redirect_to_dashboard(string $usertype): void
{
    $dashboards = [
        'admin' => 'admin_dashboard.php',
        'teacher' => 'teacher_dashboard.php',
        'student' => 'student_dashboard.php',
        'parent' => 'parent_dashboard.php'
    ];
    
    header("Location: {$dashboards[$usertype]}");
    exit();
}

/**
 * Redirects with error message
 */
function redirect_with_error(string $message): void
{
    header("Location: index.php?error=" . urlencode($message));
    exit();
}