<?php
require 'config.php'; // Include the config file

function get_current_term() {
    global $pdo; // Access the global $pdo variable

    // Prepare and execute the SQL statement
    $sql = "SELECT TermName FROM term WHERE CURRENT_DATE BETWEEN StartDate AND EndDate";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $currentTerm = $stmt->fetchColumn(); // Fetch the current term name

    // If no term is found, determine the term based on the current month
    if ($currentTerm === false) {
        $currentMonth = date('n');
        if ($currentMonth >= 2 && $currentMonth <= 4) {
            $currentTerm = '1st Term';
        } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
            $currentTerm = '2nd Term';
        } else {
            $currentTerm = '3rd Term';
        }
    }

    return $currentTerm;
}

$currentterm = get_current_term();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            margin-top: 50px;
        }
        .card {
            margin-bottom: 30px;
        }
        .error-message {
            color: red;
        }
    </style>
    <script>
        // Function to update the Current Class field and CurrentClassID field based on Joining Class selection
        function updateCurrentClass() {
            const joiningClass = document.getElementById('studentjoiningclass').value;
            document.getElementById('studentcurrentclass').value = joiningClass;

            // Update CurrentClassID based on the selected Joining Class
            const classes = <?php
                // Fetch class data from the database and convert it to JSON
                try {
                    $sql = "SELECT ClassID, ClassName FROM classes";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($classes);
                } catch (Exception $e) {
                    echo json_encode([]);
                }
            ?>;
            
            const selectedClass = classes.find(cls => cls.ClassName === joiningClass);
            if (selectedClass) {
                document.getElementById('currentclassid').value = selectedClass.ClassID;
            } else {
                document.getElementById('currentclassid').value = '';
            }
        }

        // Password confirmation validation
        function validatePassword() {
            const password = document.getElementById('studentpassword').value;
            const confirmPassword = document.getElementById('studentconfirmpassword').value;
            if (password !== confirmPassword) {
                document.getElementById('studentconfirmpassword').setCustomValidity('Passwords do not match.');
            } else {
                document.getElementById('studentconfirmpassword').setCustomValidity('');
            }
        }

        // Add event listener for password confirmation
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('studentconfirmpassword').addEventListener('input', validatePassword);
        });
    </script>
</head>
<body>
<br>

<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12 bg-success">
            <br>
            <form method="post" action="StudentRegistrationLogic.php" enctype="multipart/form-data">

                <h2 class="text-center">STUDENT FORM</h2>

                <div class="form-group">
                    <label for="studentfirstname">First Name:</label>
                    <input type="text" class="form-control" id="studentfirstname" name="studentfirstname" required>
                </div>
                <div class="form-group">
                    <label for="studentlastname">Last Name:</label>
                    <input type="text" class="form-control" id="studentlastname" name="studentlastname" required>
                </div>
                <div class="form-group">
                    <label for="studentusername">Username:</label>
                    <input type="text" id="studentusername" name="studentusername" class="form-control" required readonly>
                </div>
                <div class="form-group">
                    <label for="studentpassword">Password:</label>
                    <input type="password" class="form-control" id="studentpassword" name="studentpassword" required>
                    <small id="passwordHelpBlock" class="form-text text-muted">
                        Your password must be at least 8 characters long, contain at least one letter, one digit, one uppercase letter, and one special character.
                    </small>
                </div>
                <div class="form-group">
                    <label for="studentconfirmpassword">Confirm Password:</label>
                    <input type="password" class="form-control" id="studentconfirmpassword" name="studentconfirmpassword" required>
                </div>
                <div class="form-group">
                    <label for="studentdob">Date of Birth:</label>
                    <input type="date" class="form-control" id="studentdob" name="studentdob">
                </div>
                <div class="form-group">
                    <label for="studentgender">Gender:</label>
                    <select id="studentgender" name="studentgender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="studentaddress">Address:</label>
                    <input type="text" class="form-control" id="studentaddress" name="studentaddress">
                </div>
                <div class="form-group">
                    <label for="studentphone">Phone:</label>
                    <input type="tel" id="studentphone" name="studentphone" pattern="[0-9]{4}-[0-9]{3}-[0-9]{3}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="studentemail">Email:</label>
                    <input type="email" class="form-control" id="studentemail" name="studentemail">
                </div>
                <div class="form-group">
                    <label for="studentenrollmentdate">Enrollment Date:</label>
                    <input type="date" id="studentenrollmentdate" name="studentenrollmentdate" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="studentjoiningclass">Joining Class:</label>
                    <select id="studentjoiningclass" name="studentjoiningclass" class="form-control" onchange="updateCurrentClass()">
                        <option value="">Select Class</option>
                        <?php
                        // Fetch class data from the database
                        try {
                            $sql = "SELECT ClassName FROM classes";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Loop through the classes and populate the select options
                            foreach ($classes as $class) {
                                echo "<option value='{$class['ClassName']}'>{$class['ClassName']}</option>";
                            }
                        } catch (Exception $e) {
                            echo "<option value=''>Error fetching classes</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="studentcurrentclass">Current Class:</label>
                    <input type="text" id="studentcurrentclass" name="studentcurrentclass" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="currentclassid">Current Class ID:</label>
                    <input type="text" id="currentclassid" name="currentclassid" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="studentcurrentacademicyear">Current Academic Year:</label>
                    <input type="text" class="form-control" id="studentcurrentacademicyear" name="studentcurrentacademicyear" value="<?php echo date('Y'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="studentcurrentterm">Current Term:</label>
                    <input type="text" id="studentcurrentterm" name="studentcurrentterm" class="form-control" value="<?php echo get_current_term(); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="student_ProfilePicture">Profile Picture:</label>
                    <input type="file" id="student_ProfilePicture" name="student_ProfilePicture" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Add Student</button>
            </form>
            <br><br>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
