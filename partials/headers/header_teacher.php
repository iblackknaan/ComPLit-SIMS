<?php
session_start(); // Corrected missing semicolon

// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Check if the user is logged in as a teacher
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'teacher') {
    header("Location: index.php");
    exit();
}

// Retrieve the teacher's information from the database based on the logged-in user's username
$username = $_SESSION['username'];
$sql = "SELECT * FROM teachers WHERE Username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// Set the teacherID session variable as the TeacherID if found
if ($teacher && isset($teacher['TeacherID'])) {
    $_SESSION['userID'] = $teacher['TeacherID'];
} else {
    // Handle the case where the teacher ID is not found
    echo "Error: Teacher ID not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Teacher Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="teacher_dashboard.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="teacher_profile.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="teacher_classes.php">Classes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="assignment_management.php">Assignment Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gradebook.php">Gradebook</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="attendance_tracker.php">Attendance Tracker</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="teacher_communication_hub">Communication Hub</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="student_performance_analytics.php">Student Performance Analytics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="resource_library.php">Resource Library</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lesson_plans.php">Lesson Plans</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
        </div>
    </nav>
</header>
