<?php
// admin_functions.php

// 1. Require config first
require_once __DIR__ . '/config.php';

function get_recent_activity(int $limit = 5): array {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                a.*,
                u.username,
                u.role,
                CASE 
                    WHEN a.action_type = 'login' THEN 'sign-in-alt'
                    WHEN a.action_type = 'logout' THEN 'sign-out-alt'
                    WHEN a.action_type = 'create' THEN 'plus-circle'
                    WHEN a.action_type = 'update' THEN 'edit'
                    WHEN a.action_type = 'delete' THEN 'trash-alt'
                    ELSE 'info-circle'
                END as icon,
                CASE 
                    WHEN a.action_type = 'login' THEN 'success'
                    WHEN a.action_type = 'logout' THEN 'secondary'
                    WHEN a.action_type = 'create' THEN 'primary'
                    WHEN a.action_type = 'update' THEN 'warning'
                    WHEN a.action_type = 'delete' THEN 'danger'
                    ELSE 'info'
                END as color
            FROM activity_log a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.timestamp DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Activity log error: " . $e->getMessage());
        return [];
    }
}

function time_ago(string $datetime): string {
    $now = new DateTime;
    $then = new DateTime($datetime);
    $diff = $now->diff($then);
    
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

function get_storage_usage(): int {
    try {
        $total = disk_total_space(__DIR__);
        $free = disk_free_space(__DIR__);
        return $total ? (int)round((($total - $free) / $total * 100)) : 0;
    } catch (Exception $e) {
        error_log("Storage check failed: " . $e->getMessage());
        return 0;
    }
}

function get_storage_status(): string {
    $usage = get_storage_usage();
    return match (true) {
        $usage > 90 => 'danger',
        $usage > 70 => 'warning',
        default => 'success'
    };
}

function get_active_users_count(): int {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM sessions WHERE last_activity > NOW() - INTERVAL 15 MINUTE");
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Active users count failed: " . $e->getMessage());
        return 0;
    }
}

function get_active_users_percentage(): int {
    global $pdo;
    try {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $active = get_active_users_count();
        return $total ? (int)round(($active / $total) * 100) : 0;
    } catch (PDOException $e) {
        error_log("Active percentage failed: " . $e->getMessage());
        return 0;
    }
}

function log_activity(int $userId, string $action, string $actionType): bool {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log 
            (user_id, action, action_type, ip_address) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $userId,
            $action,
            $actionType,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        error_log("Activity log failed: " . $e->getMessage());
        return false;
    }
}

function get_last_backup_time(): string {
    $backupDir = __DIR__ . '/backups/';
    if (!file_exists($backupDir)) {
        return 'Never';
    }
    
    $files = glob($backupDir . '*.sql');
    if (empty($files)) {
        return 'Never';
    }
    
    $latest = max(array_map('filemtime', $files));
    return date('M j, Y g:i a', $latest);
}