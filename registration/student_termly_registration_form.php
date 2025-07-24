<?php
session_start();
require_once 'config.php';

// Initialize variables to hold form data
$errorMessages = $_SESSION['errorMessages'] ?? [];
$submittedData = $_SESSION['submittedData'] ?? [];
unset($_SESSION['errorMessages'], $_SESSION['submittedData']); // Clear session variables

// Determine the current academic year
$currentYear = date('Y');
$academicYear = $currentYear;

// Generate Student ID
function generateStudentID($pdo) {
    // Fetch the latest student ID from the database and increment
    $query = "SELECT MAX(StudentID) AS max_id FROM students";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxID = $result['max_id'];

    if ($maxID === null) {
        $maxID = 0; // If no records, start from 0
    } else {
        $maxID = intval($maxID);
    }

    $newID = str_pad($maxID + 1, 6, '0', STR_PAD_LEFT); // Assuming 6 digits for Student ID

    return $newID;
}

// Determine the term based on the academic year and current date
function getCurrentTerm() {
    $currentMonth = date('n');
    if ($currentMonth >= 2 && $currentMonth <= 4) {
        return '1st Term';
    } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
        return '2nd Term';
    } else {
        return '3rd Term';
    }
}

// Check if an academic year is already set in the form submission
if (isset($submittedData['academicYear'])) {
    $academicYear = $submittedData['academicYear'];
}

// Fetch subject categories and subjects from the database
function fetchSubjectsByCategory($pdo, $categoryName) {
    $query = "
        SELECT s.SubjectID, s.SubjectName 
        FROM subjects s
        JOIN subject_category_mapping scm ON s.SubjectID = scm.SubjectID
        JOIN subject_categories sc ON scm.CategoryID = sc.CategoryID
        WHERE sc.CategoryName = :categoryName
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['categoryName' => $categoryName]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$studentID = generateStudentID($pdo);
$currentTerm = getCurrentTerm();

// Fetch subjects based on categories
$mandatorySubjects = fetchSubjectsByCategory($pdo, 'Mandatory');
$electiveSubjects = fetchSubjectsByCategory($pdo, 'Elective');
$optionalSubjects = fetchSubjectsByCategory($pdo, 'Optional');

require_once 'header_admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Termly Registration Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            margin-top: 20px;
        }
        .form-heading {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group-a {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
        }
        .submit-button {
            text-align: center;
        }

        .success-message {
            color: green;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid green;
            background-color: #f0f9f0;
        }

        .error-message {
            color: red;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid red;
            background-color: #f9f0f0;
        }


        /* Container for two columns of nursery areas */
        .other-nursery-areas-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px; /* Adjust as needed */
        }

        /* Each column of nursery areas */
        .other-nursery-area-column {
            flex: 1;
            margin-right: 10px; /* Adjust spacing between columns */
        }

        /* Last column should not have right margin */
        .other-nursery-areas-container .other-nursery-area-column:last-child {
            margin-right: 0;
        }

        /* Each nursery area */
        .other-nursery-area {
            margin-bottom: 5px; /* Adjust spacing between items */
        }

        /* Adjust spacing for checkbox and label */
        .other-nursery-area input[type="checkbox"] {
            margin-right: 5px;
        }

    </style>
</head>
<body>

<div class="container">
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>
</div>

<div class="container form-container">
    <div class="form-heading">
        <h3>Student Termly Registration</h3>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <form id="registrationForm" action="student_termly_registration_logic.php" method="POST">

                        <div class="form-group">
                            <label for="UniqueID"><strong>Student Unique ID:</strong></label>
                            <input type="text" class="form-control" id="UniqueID" name="UniqueID" required placeholder="Enter Student Unique ID" value="<?php echo htmlspecialchars($submittedData['UniqueID'] ?? ''); ?>">
                            <?php if (isset($errorMessages['UniqueID'])): ?>
                                <div class="error-message"><?php echo $errorMessages['UniqueID']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="StudentID"><strong>Student ID:</strong></label>
                            <input type="text" class="form-control" id="StudentID" name="StudentID" placeholder="Auto-generated Student ID" value="<?php echo htmlspecialchars($studentID); ?>" readonly>
                            <?php if (isset($errorMessages['StudentID'])): ?>
                                <div class="error-message"><?php echo $errorMessages['StudentID']; ?></div>
                            <?php endif; ?>
                        </div>

                    <div class="form-group">
                        <label for="CurrentClassID"><strong>Current Class ID:</strong></label>
                        <input type="text" class="form-control" id="CurrentClassID" name="CurrentClassID" readonly>
                        <?php if (isset($errorMessages['CurrentClassID'])): ?>
                            <div class="error-message"><?php echo $errorMessages['CurrentClassID']; ?></div>
                        <?php endif; ?>
                    </div>

                        <div class="form-group">
                            <label for="academicYear"><strong>Academic Year:</strong></label>
                            <input type="text" class="form-control" id="academicYear" name="academicYear" placeholder="Enter Academic Year" value="<?php echo htmlspecialchars($academicYear); ?>" readonly>
                            <?php if (isset($errorMessages['academicYear'])): ?>
                                <div class="error-message"><?php echo $errorMessages['academicYear']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="class"><strong>Class:</strong></label>
                            <?php
                            // Fetch the current class from the database based on the UniqueID
                            if (isset($submittedData['UniqueID'])) {
                                $query = "SELECT CurrentClass FROM students WHERE UniqueID = :UniqueID";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute(['UniqueID' => $submittedData['UniqueID']]);
                                $currentClass = $stmt->fetchColumn();

                                if ($currentClass) {
                                    echo '<input type="text" class="form-control" id="class" name="class" value="' . $currentClass . '" readonly>';
                                } else {
                                    echo '<input type="text" class="form-control" id="class" name="class" value="" readonly>';
                                }
                            } else {
                                echo '<input type="text" class="form-control" id="class" name="class" value="" readonly>';
                            }
                            ?>
                            <?php if (isset($errorMessages['class'])): ?>
                                <div class="error-message"><?php echo $errorMessages['class']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="term"><strong>Term:</strong></label>
                            <input type="text" class="form-control" id="term" name="term" value="<?php echo htmlspecialchars($currentTerm); ?>" readonly>
                            <?php if (isset($errorMessages['term'])): ?>
                                <div class="error-message"><?php echo $errorMessages['term']; ?></div>
                            <?php endif; ?>
                        </div>

                </div>
            </div>
        </div>

        <!-- ---------------------------------------------------------------------- -->        

        <div class="col-lg-6 col-md-6">
            <div class="card mb-3">
                <div class="card-body">


<div class="form-group-a" style="text-align: center;">
    <span id="StudentName" style="font-weight: bolder; margin-left: 25px; font-weight: bolder; font-size: 20px;;"></span><br>
</div>                    

<div class="form-group">
    <div id="mandatory_subjects">
    </div>
</div>

<div class="form-group">
    <div id="elective_subjects">
    </div>
</div>

<div class="form-group Optional subject-list">
    <div id="optional_subjects">               
    </div>
</div>

<div class="form-group">
    <div id="other_nursery_areas">                  
    </div>
</div>


</div></div></div>



<div class="submit-button">
    <button type="submit" class="btn btn-primary">Register Student</button>
</div>
</form>
</div>
<script src="student_termly_registration_javascript.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<?php require_once 'footer.php'; ?>

</body>
</html>