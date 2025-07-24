<?php
// Include the database configuration file
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $day = $_POST['day'];
    $timeSlotID = $_POST['time_slot_id'];
    $classID = $_POST['class_id'];
    $subjectID = $_POST['subject_id'];

    // Validate inputs
    if (empty($day) || empty($timeSlotID) || empty($classID) || empty($subjectID)) {
        $alertMessage = "All fields are required.";
        header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
        exit();
    }

    // Fetch class name based on class ID
    $classStmt = $pdo->prepare("SELECT ClassName FROM classes WHERE ClassID = ?");
    $classStmt->execute([$classID]);
    $class = $classStmt->fetchColumn();

    // Fetch subject name based on subject ID
    $subjectStmt = $pdo->prepare("SELECT SubjectName FROM subjects WHERE SubjectID = ?");
    $subjectStmt->execute([$subjectID]);
    $subject = $subjectStmt->fetchColumn();

    // Define allowed subjects for Baby, Middle, and Top classes
    $allowedSubjectsBabyMiddleTop = ['Reading', 'Writing', 'Drawing', 'Physical Education'];

    // Define allowed subjects for Primary - Three to Primary - Seven classes
    $allowedSubjectsPrimaryThreeToSeven = [
        'Mathematics', 'Science', 'Social Studies', 'English Comprehension',
        'English Grammar', 'Christian Religious Education', 'Islamic Education',
        'Art and Craft', 'Computer Science'
    ];

    // Validate subject based on class type
    if (in_array($class, ['Baby Class', 'Middle Class', 'Top Class', 'Primary - One', 'Primary - Two'])) {
        // Validate for Baby, Middle, Top, Primary - One, and Primary - Two classes
        if (!in_array($subject, $allowedSubjectsBabyMiddleTop)) {
            $alertMessage = "Error: Only Reading, Writing, Drawing, and Physical Education are allowed for Baby, Middle, Top, Primary - One, and Primary - Two classes.";
            header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
            exit();
        }
    } elseif (in_array($class, ['Primary - Three', 'Primary - Four', 'Primary - Five', 'Primary - Six', 'Primary - Seven'])) {
        // Validate for Primary - Three to Primary - Seven classes
        if (!in_array($subject, $allowedSubjectsPrimaryThreeToSeven)) {
            $alertMessage = "Error: Invalid subject selected for this class.";
            header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
            exit();
        }
    } else {
        // Invalid class selected
        $alertMessage = "Error: Invalid class selected.";
        header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
        exit();
    }

    // Check if the subject is already scheduled for the same class on the same day
    $checkClassSubjectStmt = $pdo->prepare("SELECT COUNT(*) FROM timetable WHERE day = ? AND class_id = ? AND subject_id = ?");
    $checkClassSubjectStmt->execute([$day, $classID, $subjectID]);
    $classSubjectCount = $checkClassSubjectStmt->fetchColumn();

    if ($classSubjectCount > 0) {
        $alertMessage = "Error: Subject already scheduled for this class on this day.";
        header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
        exit();
    }

    // Check if the subject is already scheduled in the same time slot on the same day for a different class
    $checkSubjectStmt = $pdo->prepare("SELECT COUNT(*) FROM timetable WHERE day = ? AND time_slot_id = ? AND subject_id = ?");
    $checkSubjectStmt->execute([$day, $timeSlotID, $subjectID]);
    $subjectCount = $checkSubjectStmt->fetchColumn();

    if ($subjectCount > 0) {
        $alertMessage = "Error: Subject already scheduled in the same time slot on this day.";
        header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
        exit();
    }

    // Insert data into timetable table
    $stmt = $pdo->prepare("INSERT INTO timetable (day, time_slot_id, class_id, subject_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$day, $timeSlotID, $classID, $subjectID]);

    // Check if insertion was successful
    if ($stmt->rowCount() > 0) {
        $alertMessage = "Timetable generated successfully!";
    } else {
        $alertMessage = "Error generating timetable.";
    }
    header("Location: InputTimetableData.php?alertMessage=" . urlencode($alertMessage));
    exit();
} else {
    // Redirect to the input form if accessed directly without POST data
    header("Location: InputTimetableData.php");
    exit();
}
?>
