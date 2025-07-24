<?php
class Logger {
    private $pdo;
    private $logFile;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->logFile = __DIR__.'/logs/activity_'.date('Y-m-d').'.log';
        
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0750, true);
        }
    }

    public function logActivity(
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        try {
            // Database log
            $stmt = $this->pdo->prepare("
                INSERT INTO activity_logs 
                (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $action,
                $entityType,
                $entityId,
                $oldData ? json_encode($oldData) : null,
                $newData ? json_encode($newData) : null,
                $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            // File log (redundant backup)
            $logMsg = sprintf(
                "[%s] [USER:%s] [%s] %s:%d - %s\nOld: %s\nNew: %s\nIP: %s\nUA: %s\n\n",
                date('Y-m-d H:i:s'),
                $userId ?? 'SYSTEM',
                $action,
                $entityType ?? 'SYSTEM',
                $entityId ?? 0,
                $oldData ? json_encode($oldData) : 'N/A',
                $newData ? json_encode($newData) : 'N/A',
                $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
            );
            
            file_put_contents($this->logFile, $logMsg, FILE_APPEND);
            
        } catch (\Throwable $e) {
            // Fallback to file-only if DB fails
            error_log('Logger failed: '.$e->getMessage());
        }
    }
}