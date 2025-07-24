<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loading Dashboard...</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            text-align: center;
            padding-top: 15%;
        }
        .spinner-border {
            width: 4rem;
            height: 4rem;
        }
    </style>
</head>
<body>
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <h4 class="mt-3">Loading Admin Dashboard, please wait...</h4>

    <script>
        setTimeout(function () {
            window.location.href = "admin_dashboard.php"; // After 3 seconds, redirect to admin dashboard
        }, 3000);
    </script>
</body>
</html>
