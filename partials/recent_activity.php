<?php
// partials/recent_activity.php

// 1. Check for admin access first
defined('ADMIN_ACCESS') or die('Restricted access');

// 2. Require the functions file using absolute path
require_once __DIR__ . '/../admin_functions.php';

// 3. Get activities with error handling
try {
    $activities = get_recent_activity(5);
    
    if (empty($activities)) {
        echo '<div class="alert alert-info">No recent activity found</div>';
        return;
    }
} catch (Throwable $e) {
    error_log("Activity load failed: " . $e->getMessage());
    echo '<div class="alert alert-danger">Failed to load activity</div>';
    return;
}
?>

<div class="card shadow-sm h-100">
    <!-- Your existing card HTML here -->
    <?php foreach($activities as $activity): ?>
        <!-- Your activity items here -->
    <?php endforeach; ?>
</div>