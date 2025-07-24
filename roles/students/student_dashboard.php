<?php
// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Include the header
require_once 'header_student.php'; // Adjust the path if necessary



// Check if the user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Fetch the student's details from the database using username
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

<div class="container">
    <div class="jumbotron mt-4">
        <h1 class="display-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p class="lead">This is your student dashboard.</p>
        <!-- Display the studentID -->
        <p>Your Student ID: <?php echo htmlspecialchars($_SESSION['studentID']); ?></p>
        <hr class="my-4">
        <p class="lead">
            <a class="btn btn-primary btn-lg" href="student_profile.php" role="button">View Profile</a>
        </p>
    </div>
</div>

<!-- Include footer if necessary -->
<?php require_once 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
