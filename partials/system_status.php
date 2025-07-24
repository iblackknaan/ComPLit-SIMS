<?php
defined('ADMIN_ACCESS') or die('Restricted access');
require_once __DIR__ . '/../admin_functions.php';
?>

<div class="card text-white bg-info">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Status</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Recent Activity Column -->
            <div class="col-md-8 mb-3 mb-md-0">
                <?php include 'recent_activity.php'; ?>
            </div>
            
            <!-- System Metrics Column -->
            <div class="col-md-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-database me-2"></i>Storage</span>
                        <span><?= get_storage_usage() ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-<?= get_storage_status() ?>" 
                             role="progressbar" style="width: <?= get_storage_usage() ?>%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fas fa-users me-2"></i>Active Users</span>
                        <span><?= get_active_users_count() ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" 
                             role="progressbar" style="width: <?= get_active_users_percentage() ?>%"></div>
                    </div>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white border-0">
                        <span><i class="fas fa-code-branch me-2"></i>System Version</span>
                        <span class="badge bg-dark">v<?= SYSTEM_VERSION ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white border-0">
                        <span><i class="fas fa-clock me-2"></i>Last Backup</span>
                        <span><?= get_last_backup_time() ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>