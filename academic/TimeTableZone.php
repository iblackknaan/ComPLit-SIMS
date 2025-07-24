<?php
// Include the database configuration file
require_once 'config.php';

// Include the Time Table Zone
require_once 'header_admin.php';

// Determine if an action has been triggered
$actionTriggered = false;

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ViewTimetable'])) {
        include 'ViewTimetable.php';
        $actionTriggered = true;
    } elseif (isset($_POST['GenerateTimetable'])) {
        include 'GenerateTimetable.php';
        $actionTriggered = true;
    }
}

// Check if action is set and handle accordingly
if (isset($_GET['action']) && $_GET['action'] === 'generate') {
    echo '<div class="alert alert-info text-center" role="alert">Continue Generating timetable...</div>';
    // Include GenerateTimeTable.php form
    include 'GenerateTimeTable.php';
    $actionTriggered = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Zone</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            display: <?php echo $actionTriggered ? 'block' : 'flex'; ?>;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Card for Viewing Timetable -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">View Timetable</h5>
                        <p class="card-text">Click the button below to view the current timetable.</p>
                        <form method="post">
                            <button type="submit" name="ViewTimetable" class="btn btn-primary">View Timetable</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Card for Generating Timetable -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Generate Timetable</h5>
                        <p class="card-text">Click the button below to generate a new timetable.</p>
                        <form method="post">
                            <button type="submit" name="GenerateTimetable" class="btn btn-success">Generate Timetable</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Card for Reloading the Page -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reload Page</h5>
                        <p class="card-text">Click the button below to reload the page.</p>
                        <form method="post">
                            <button type="submit" name="ReloadPage" class="btn btn-warning" onclick="window.location.reload();">Reload Page</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer if necessary -->
    <?php require_once 'footer.php'; ?>

    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
