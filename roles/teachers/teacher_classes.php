<?php
// Include the header
require_once 'header_teacher_profile.php'; // Adjust the path if necessary

// Check if the user is logged in as a teacher
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'teacher') {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once 'config.php'; // Assuming this file contains the PDO connection setup

// Retrieve the classes assigned to the teacher from the database
$username = $_SESSION['username'];
$sql = "SELECT c.* FROM classes c 
        INNER JOIN class_subjects_teachers cst ON c.ClassID = cst.ClassID
        INNER JOIN teachers t ON cst.TeacherID = t.TeacherID
        WHERE t.Username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are any errors in SQL execution
if ($stmt->errorCode() !== '00000') {
    $errorInfo = $stmt->errorInfo();
    echo "SQL error: " . $errorInfo[2];
    exit();
}

// If no classes are found, you can handle it accordingly
if (!$classes) {
    // Handle the case where no classes are assigned to the teacher
    echo "No classes assigned to this teacher.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Classes</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <!-- Your header content here -->
</header>

<div class="container mt-5">
    <h1 class="mb-4">My Classes</h1>
    <div class="row">
        <?php if (count($classes) > 0): ?>
            <?php foreach ($classes as $class): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($class['ClassName']); ?></h5>
                            <!-- Display more information about the class if needed -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No classes assigned.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
