<?php
// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Include the header
require_once 'header_student.php'; // Adjust the path if necessary


// Assuming the student's username is stored in the session
$username = $_SESSION['username'];

// Fetch the student's information including first and last names
$stmt = $pdo->prepare("
    SELECT s.FirstName, s.LastName, s.CurrentClass, c.ClassID
    FROM students s
    JOIN classes c ON s.CurrentClass = c.ClassName
    WHERE s.Username = ?
");
$stmt->execute([$username]);
$studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$studentInfo) {
    echo "No student information found.";
    exit();
}

$firstName = $studentInfo['FirstName'];
$lastName = $studentInfo['LastName'];
$studentClassID = $studentInfo['ClassID'];

if (empty($studentClassID)) {
    echo "No class selected.";
    exit();
}

// Fetch the timetable for the student's class including subject description and classroom number
$stmt = $pdo->prepare("
    SELECT t.day, ts.TimeRange, s.SubjectName, subj.Description AS SubjectDescription, c.ClassroomNumber
    FROM timetable t
    JOIN time_slots ts ON t.time_slot_id = ts.TimeSlotID
    JOIN subjects s ON t.subject_id = s.SubjectID
    JOIN subjects subj ON s.SubjectID = subj.SubjectID
    JOIN classes c ON t.class_id = c.ClassID
    WHERE t.class_id = ?
    ORDER BY t.day, ts.TimeRange;
");
$stmt->execute([$studentClassID]);
$timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$timetable) {
    echo "No timetable available for this class.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Additional custom styles can be added here */
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .container {
            margin-top: 50px; /* Adjust the top margin for spacing */
        }
        .table {
            background-color: #ffffff; /* White background for the table */
            border-radius: 10px; /* Rounded corners for a modern look */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
        }
        th, td {
            text-align: center; /* Center align text in table cells */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="jumbotron">
        <h1 class="display-4">Timetable for <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h1>
        <hr class="my-4">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Day</th>
                        <th>Time Range</th>
                        <th>Subject</th>
                        <th>Subject Description</th>
                        <th>Classroom Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timetable as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['day']); ?></td>
                            <td><?php echo htmlspecialchars($entry['TimeRange']); ?></td>
                            <td><?php echo htmlspecialchars($entry['SubjectName']); ?></td>
                            <td><?php echo htmlspecialchars($entry['SubjectDescription']); ?></td>
                            <td><?php echo htmlspecialchars($entry['ClassroomNumber']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
