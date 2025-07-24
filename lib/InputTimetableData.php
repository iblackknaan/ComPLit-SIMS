<?php
// Include the database configuration file
require_once 'config.php';

// Include the Time Table Zone
require_once 'header_admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Timetable Data</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Input Timetable Data</h1>

        <?php
        // Display alert message if set
        if (isset($_GET['alertMessage'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['alertMessage']) . '</div>';
        } elseif (isset($_GET['successMessage'])) {
            echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_GET['successMessage']) . '</div>';
        }
        ?>

        <!-- Back button to return to TimeTableZone.php with action -->
        <a href="TimeTableZone.php?action=generate" class="btn btn-primary" id="backButton">Back to Time Table Zone</a>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        // Automatically redirect after 5 seconds
        setTimeout(function() {
            document.getElementById('backButton').click();
        }, 5000); // 15000 milliseconds = 5 seconds
    </script>
</body>
</html>
