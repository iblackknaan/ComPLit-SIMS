<?php
// Start the session
session_start();

// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Check if the user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Fetch the student's details from the database using username if not already fetched
if (!isset($student)) {
    $username = $_SESSION['username']; // Assuming the username is stored in the session
    $stmt = $pdo->prepare("SELECT * FROM students WHERE Username = ?");
    $stmt->execute([$username]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        // Set session variables
        $_SESSION['studentID'] = $student['StudentID']; // Assuming 'StudentID' is the primary key in your students table
        $_SESSION['class_id'] = $student['CurrentClass']; // Assuming 'CurrentClass' is the column for the current class
    } else {
        // Handle the case where student data is not found
        // You may redirect or set default values or show an error message
        header("Location: index.php");
        exit();
    }
}

// Fetch notifications for the logged-in student if not already defined
if (!function_exists('getNotifications')) {
    function getNotifications() {
        global $pdo;
        $studentID = $_SESSION['studentID'];
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE recipient_type = 'student' AND recipient_id = ? ORDER BY created_at DESC");
        $stmt->execute([$studentID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$studentID = $_SESSION['studentID']; // Set $studentID as global

$notifications = getNotifications();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Student Management System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="student_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student_profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="class_schedule.php">Class Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="grades.php">Grades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notifications_student.php">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        <?php if (!empty($student['ProfilePicture'])): ?>
            <img src="<?php echo htmlspecialchars($student['ProfilePicture']); ?>" alt="Profile Picture" class="img-thumbnail" width="50">
        <?php else: ?>
            <img src="default_profile.png" alt="Default Profile Picture" class="img-thumbnail" width="50">
        <?php endif; ?>
    </nav>
</header>
