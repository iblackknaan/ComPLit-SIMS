<?php
session_start();

// Check if the user is logged in as a teacher
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'teacher') {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once 'config.php'; // Assuming this file contains the PDO connection setup

// Retrieve the teacher's information from the database based on the logged-in user's username
$username = $_SESSION['username'];
$sql = "SELECT * FROM teachers WHERE Username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// If teacher information is not found, handle the error
if (!$teacher) {
    echo "Error: Teacher information not found.";
    exit();
}

// Get the teacher's ID from the session
$teacherID = $_SESSION['userID']; // Assuming userID is the column name for teacher ID in your users table

// Prepare and execute the SQL query to fetch notifications for the teacher
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE ReceiverID = :teacherID");
$stmt->bindParam(':teacherID', $teacherID);
$stmt->execute();

// Fetch the results
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Teacher Management System</a>
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
                    <a class="nav-link" href="teacher_notifications.php">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</header>