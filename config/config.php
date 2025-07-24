<?php
// =============================================
// STRICT ERROR HANDLING
// =============================================
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die('PHP 8.0 or higher is required');
}

// =============================================
// ERROR CODE CONSTANTS
// =============================================
define('CONFIG_ERROR_MISSING_ENV', 'config_missing');
define('CONFIG_ERROR_INVALID_ENV', 'config_invalid');
define('CONFIG_ERROR_DB_HOST', 'db_host_invalid');
define('CONFIG_ERROR_DB_CONNECTION', 'db_connection');
define('CONFIG_ERROR_DB_FATAL', 'db_fatal');

// =============================================
// PATH & LOGGING CONFIGURATION
// =============================================
define('LOG_DIR', rtrim(__DIR__, '/') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);

// Secure log directory setup
if (!file_exists(LOG_DIR)) {
    if (!mkdir(LOG_DIR, 0750, true) || !chmod(LOG_DIR, 0750)) {
        error_log("Cannot create log directory at: " . LOG_DIR);
        define('LOG_FILE', 'syslog');
    }
    // Prevent directory listing
    file_put_contents(LOG_DIR . '.htaccess', "Deny from all");
} else {
    chmod(LOG_DIR, 0750);
}

// Secure daily log file
$currentDate = date('Y-m-d');
$logFile = LOG_DIR . 'errors_' . (
    preg_match('/^\d{4}-\d{2}-\d{2}$/', $currentDate)
    ? $currentDate
    : 'invalid-date-' . time()
) . '.log';

// =============================================
// ENVIRONMENT CONFIGURATION
// =============================================
$envPath = __DIR__ . DIRECTORY_SEPARATOR . '.env';

// Triple-check file accessibility
if (!file_exists($envPath) || !is_readable($envPath) || !is_file($envPath)) {
    logAndRedirect("Configuration file inaccessible", CONFIG_ERROR_MISSING_ENV);
}

// Parse with typed scanning and error context
$rawConfig = parse_ini_file($envPath, true, INI_SCANNER_TYPED);
if ($rawConfig === false) {
    $parseError = error_get_last();
    error_log('['.date('Y-m-d H:i:s').'] ENV PARSE FAIL: ' . 
        ($parseError['message'] ?? 'Unknown error') . 
        ' | File: ' . basename($envPath), 
        3, $logFile);
    logAndRedirect("Invalid configuration format", CONFIG_ERROR_INVALID_ENV);
}

// Normalize and validate
$config = array_change_key_case($rawConfig, CASE_UPPER);
define('APP_DEBUG', filter_var($config['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// Enhanced validation schema
$validationRules = [
    'DB_HOST' => [
        'type' => 'string',
        'pattern' => '/^[\w.-]+$/',
        'error' => CONFIG_ERROR_DB_HOST
    ],
    'DB_NAME' => [
        'type' => 'string',
        'min_length' => 2
    ],
    'DB_USER' => [
        'type' => 'string',
        'min_length' => 3
    ],
    'DB_PORT' => [
        'type' => 'int',
        'min' => 1,
        'max' => 65535,
        'default' => 3306
    ]
];

foreach ($validationRules as $key => $rule) {
    if (!isset($config[$key])) {
        if (!isset($rule['default'])) {
            logAndRedirect("Missing required: $key", CONFIG_ERROR_INVALID_ENV);
        }
        $config[$key] = $rule['default'];
        continue;
    }

    // Type checking
    if (($rule['type'] === 'int' && !is_numeric($config[$key])) ||
        ($rule['type'] === 'string' && !is_string($config[$key]))) {
        logAndRedirect("Invalid type for $key", $rule['error'] ?? CONFIG_ERROR_INVALID_ENV);
    }

    // Pattern validation
    if (isset($rule['pattern']) && !preg_match($rule['pattern'], $config[$key])) {
        logAndRedirect("Invalid format for $key", $rule['error'] ?? CONFIG_ERROR_INVALID_ENV);
    }
}

// =============================================
// DATABASE CONFIGURATION
// =============================================
$host    = filter_var($config['DB_HOST'] ?? 'localhost', FILTER_SANITIZE_SPECIAL_CHARS);
$port    = isset($config['DB_PORT']) ? (int)$config['DB_PORT'] : 3306;
$db      = filter_var($config['DB_NAME'] ?? 'knps2', FILTER_SANITIZE_SPECIAL_CHARS);
$user    = filter_var($config['DB_USER'] ?? 'root', FILTER_SANITIZE_SPECIAL_CHARS);
$pass    = $config['DB_PASS'] ?? '';
$charset = filter_var($config['DB_CHARSET'] ?? 'utf8mb4', FILTER_SANITIZE_SPECIAL_CHARS);

if (!preg_match('/^[\w.\-]+$/', $host)) {
    logAndRedirect("Invalid database host specified: $host", CONFIG_ERROR_DB_HOST);
}

// =============================================
// SESSION CONFIGURATION
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', '1800');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.sid_length', '128');
    ini_set('session.sid_bits_per_character', '6');

    session_set_cookie_params([
        'lifetime' => 1800,
        'path'     => '/',
        'domain'   => filter_var(
            $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost',
            FILTER_SANITIZE_SPECIAL_CHARS
        ),
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    session_start();
    session_regenerate_id(true);
}

// =============================================
// DATABASE CONNECTION
// =============================================
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => false,
    PDO::ATTR_TIMEOUT            => 5,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    PDO::MYSQL_ATTR_FOUND_ROWS   => true,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00', sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'"
];

$pdo = null;
$maxRetries = 3;
$retryDelay = 2;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $pdo->query("SELECT 1");
        break;
    } catch (PDOException $e) {
        error_log(sprintf(
            "[%s] DB Connection attempt %d/%d failed: %s\n[CONTEXT] %s",
            date('Y-m-d H:i:s'),
            $attempt,
            $maxRetries,
            $e->getMessage(),
            json_encode([
                'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null
            ])
        ), 3, $logFile);

        if ($attempt === $maxRetries) {
            logAndRedirect("Database connection permanently failed", CONFIG_ERROR_DB_CONNECTION);
        }
        sleep($retryDelay);
        $retryDelay *= 2;
    }
}

if ($pdo === null) {
    logAndRedirect("No database connection established", CONFIG_ERROR_DB_FATAL);
}



// System Constants (must come before any function calls that might use them)
define('SYSTEM_VERSION', '1.0.0');
define('SYSTEM_NAME', 'School Management System');
define('ADMIN_ACCESS', true);

// Autoload functions if not already loaded
if (!function_exists('get_recent_activity')) {
    require_once __DIR__ . '/admin_functions.php';
}


// =============================================
// DEVELOPMENT SAFEGUARDS
// =============================================
$devWarningFlag = LOG_DIR . 'dev_warning.flag';
if ($host === 'localhost' && $user === 'root' && empty($pass)) {
    if (!file_exists($devWarningFlag)) {
        $warning = "WARNING: Using default development DB credentials";
        error_log("[" . date('Y-m-d H:i:s') . "] " . $warning, 3, $logFile);
        file_put_contents($devWarningFlag, date('Y-m-d H:i:s') . " - " . $warning);
    }
}

// =============================================
// GLOBAL CONFIG
// =============================================
$GLOBALS['config'] = [
    'db'      => $pdo,
    'logFile' => $logFile,
    'env'     => $config,
    'debug'   => APP_DEBUG
];

// =============================================
// UTILITY FUNCTIONS
// =============================================
function logAndRedirect(string $message, string $errorCode) {
    global $logFile;
    error_log('[' . date('Y-m-d H:i:s') . '] ERROR: ' . $message, 3, $logFile);
    header("HTTP/1.1 500 Internal Server Error", true, 500);
    header("Location: /error.php?code={$errorCode}");
    exit;
}

function executeQuery(PDO $pdo, string $query, array $params = [], string $fetchMode = 'fetchAll') {
    global $logFile;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        return match($fetchMode) {
            'fetchAll' => $stmt->fetchAll(),
            'fetch'    => $stmt->fetch(),
            'rowCount' => $stmt->rowCount(),
            default    => true
        };
    } catch (PDOException $e) {
        $errorContext = [
            'error' => $e->getMessage(),
            'query' => $query,
            'params' => $params,
            'trace' => $e->getTraceAsString()
        ];
        error_log('[' . date('Y-m-d H:i:s') . '] QUERY ERROR: ' . json_encode($errorContext), 3, $logFile);
        throw $e;
    }
}

function transaction(callable $callback) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $result = $callback($pdo);
        $pdo->commit();
        return $result;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function debugLog(string $message, array $context = []) {
    if (APP_DEBUG) {
        global $logFile;
        $message = '[DEBUG] ' . date('Y-m-d H:i:s') . ' - ' . $message;
        if (!empty($context)) {
            $message .= PHP_EOL . json_encode($context, JSON_PRETTY_PRINT);
        }
        error_log($message . PHP_EOL, 3, $logFile);
    }
}

// =============================================
// PRODUCTION CHECKS
// =============================================
if (!APP_DEBUG) {
    if (empty($_SERVER['HTTPS'])) {
        header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
    if ($host === 'localhost' && $user === 'root' && empty($pass)) {
        logAndRedirect("Insecure database configuration", CONFIG_ERROR_DB_CONNECTION);
    }
}

// Initial debug log (must come after all config is set)
debugLog("Configuration initialized", [
    'db_host' => $host,
    'db_port' => $port,
    'db_name' => $db,
    'session' => session_id()
]);