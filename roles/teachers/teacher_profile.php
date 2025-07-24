<?php
// Include the database connection
require_once 'config.php'; // Assuming this file contains the PDO connection setup

// Include the teacher header
require_once 'header_teacher.php'; // Assuming this file contains the PDO connection setup

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

// Prepare and execute the SQL query to fetch notifications for the teacher based on recipient_type
$recipientType = 'teacher'; // Assuming 'teacher' is the recipient_type for notifications meant for teachers
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE recipient_type = :recipientType AND recipient_id = :teacherID");
$stmt->bindParam(':recipientType', $recipientType);
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

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="text-center">Teacher Profile</h2>
        </div>

        <div class="col-lg-8">
            <div class="row">
                <div class="col-lg-12 bg-danger">
                    <h5 class="text-center">Personal Information</h5>
                </div>
                <div class="col-lg-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>First Name :</th>
                            <td><?php echo htmlspecialchars($teacher['FirstName'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Last Name :</th>
                            <td><?php echo htmlspecialchars($teacher['LastName'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Username :</th>
                            <td><?php echo htmlspecialchars($teacher['Username'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Date of Birth :</th>
                            <td><?php echo htmlspecialchars($teacher['DateOfBirth'] ?? ''); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Gender :</th>
                            <td><?php echo htmlspecialchars($teacher['Gender'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Address :</th>
                            <td><?php echo htmlspecialchars($teacher['Address'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Phone :</th>
                            <td><?php echo htmlspecialchars($teacher['Phone'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Email :</th>
                            <td><?php echo htmlspecialchars($teacher['Email'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>Hire Date :</th>
                            <td><?php echo htmlspecialchars($teacher['HireDate'] ?? ''); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-12 bg-danger">
                    <h5 class="text-center">Notifications</h5>
                </div>
                <div class="col-lg-12">
                    <ul class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($notification['message'] ?? ''); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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
