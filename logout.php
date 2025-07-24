<?php
// Start session
session_start();

// Destroy the session
session_destroy();

// Clear any cache traces
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Redirect to index page
header("Location: index.php");
exit();
?>
