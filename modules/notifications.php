<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Notifications</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
session_start();

// Check if the user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Retrieve the student ID from the session
$studentID = $_SESSION['userID'];

// Fetch notifications for the logged-in student
$sql = "SELECT NotificationID, SenderID, Message, DateSent FROM notifications WHERE ReceiverID = ? ORDER BY DateSent DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$studentID]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include the header
require_once 'header_student.php'; // Adjust the path if necessary
?>



<div class="container mt-5">
    <h2>Your Notifications</h2>
    <?php if ($notifications): ?>
        <ul class="list-group">
            <?php foreach ($notifications as $notification): ?>
                <li class="list-group-item">
                    <strong>Date:</strong> <?php echo htmlspecialchars($notification['Date']); ?><br>
                    <strong>Message:</strong> <?php echo htmlspecialchars($notification['Message']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</div>

<!-- Include footer if necessary -->
<?php require_once 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
