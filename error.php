<?php 
http_response_code((int)($_GET['code'] ?? 403)); 
?>
<!DOCTYPE html>
<html>
<head><title>Error <?= htmlspecialchars($_GET['code'] ?? '403') ?></title></head>
<body>
    <h1>Error <?= htmlspecialchars($_GET['code'] ?? '403') ?></h1>
</body>
</html>