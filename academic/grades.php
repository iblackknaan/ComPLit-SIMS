<?php
// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Include the header
require_once 'header_student.php'; // Adjust the path if necessary

// Ensure $studentID is set
if (!isset($studentID)) {
    // Redirect or handle the case where $studentID is not set
    // For now, I'll redirect to index.php
    header("Location: index.php");
    exit();
}

// Fetch grades data for the logged-in student
$sql = "SELECT subjects.SubjectName, report_cards.Grade, report_cards.Comments 
        FROM report_cards 
        JOIN subjects ON report_cards.SubjectID = subjects.SubjectID 
        WHERE report_cards.StudentID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$studentID]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-5">
    <h2>Your Grades</h2>
    <?php if ($grades): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['SubjectName']); ?></td>
                        <td><?php echo htmlspecialchars($grade['Grade']); ?></td>
                        <td><?php echo htmlspecialchars($grade['Comments']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No grades available.</p>
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
