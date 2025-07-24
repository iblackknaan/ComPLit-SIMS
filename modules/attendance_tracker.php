<?php
require_once 'config.php';
require_once 'header_teacher.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getCurrentTerm() {
    $currentMonth = date('n');
    if ($currentMonth >= 2 && $currentMonth <= 4) {
        return '1'; // Term 1
    } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
        return '2'; // Term 2
    } else {
        return '3'; // Term 3
    }
}

$classes = $pdo->query('SELECT ClassID, ClassName FROM classes')->fetchAll(PDO::FETCH_ASSOC);
$sections = $pdo->query('SELECT SectionID, SectionName FROM sections')->fetchAll(PDO::FETCH_ASSOC);
$terms = $pdo->query('SELECT TermID, TermName FROM term')->fetchAll(PDO::FETCH_ASSOC);

$currentTermID = getCurrentTerm();
$currentTermName = array_filter($terms, function($term) use ($currentTermID) {
    return $term['TermID'] == $currentTermID;
});
$currentTermName = !empty($currentTermName) ? reset($currentTermName)['TermName'] : 'Unknown Term';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
            text-align: center; /* Center the heading */
        }
        form {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        select, input[type="text"] {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block; /* Make the button a block element */
            margin: 20px auto; /* Center the button */
        }
        button:hover {
            background-color: #45a049;
        }
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e0e0e0;
        }

        caption {
            font-size: 1.5em;
            margin: 10px 0;
            color: #333;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Student Attendance Tracker</h1>

    <form>
        <!-- Class input area -->
        <label for="classID">Select Class:</label>
        <select name="classID" id="classID" onchange="fetchSubjects(this.value)">
            <option value="">Select Class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['ClassID']); ?>"><?php echo htmlspecialchars($class['ClassName']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <!-- Subject input area -->
        <label for="subjectID">Select Subject:</label>
        <select name="subjectID" id="subjectID" onchange="fetchStudents()">
            <option value="">Select Subject</option>
            <!-- Options will be populated dynamically -->
        </select>
        <br>
        <!-- Term input area -->
        <label for="termID">Current Term:</label>
        <input type="text" id="termID" value="<?php echo htmlspecialchars($currentTermName); ?>" readonly>
        <input type="hidden" name="termID" id="hiddenTermID" value="<?php echo htmlspecialchars($currentTermID); ?>">
        <br>
        <div class="form-group">
            <label for="academicYear"><strong>Academic Year:</strong></label>
            <input type="text" class="form-control" id="academicYear" name="academicYear" value="<?php echo date('Y'); ?>" readonly>
        </div>
        <br>
    </form>

    <div id="student-list">
        <!-- Student list will be displayed here -->
    </div>

    <button id="save-attendance" style="display: none;" onclick="saveAttendance()">Save Attendance</button>

<script src="attendance_tracker.js"></script>

</body>
</html>
