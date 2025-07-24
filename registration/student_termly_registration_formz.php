
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
        .form-group {
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
        }
        .submit-button {
            text-align: center;
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
                            <label for="UniqueID">Student Unique ID:</label>
                            <input type="text" class="form-control" id="UniqueID" name="UniqueID" required placeholder="Enter Student Unique ID" value="<?php echo htmlspecialchars($submittedData['UniqueID'] ?? ''); ?>">
                            <?php if (isset($errorMessages['UniqueID'])): ?>
                                <div class="error-message"><?php echo $errorMessages['UniqueID']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="StudentID">Student ID:</label>
                            <input type="text" class="form-control" id="StudentID" name="StudentID" placeholder="Auto-generated Student ID" value="<?php echo htmlspecialchars($studentID); ?>" readonly>
                            <?php if (isset($errorMessages['StudentID'])): ?>
                                <div class="error-message"><?php echo $errorMessages['StudentID']; ?></div>
                            <?php endif; ?>
                        </div>

                    <div class="form-group">
                        <label for="CurrentClassID">Current Class ID:</label>
                        <input type="text" class="form-control" id="CurrentClassID" name="CurrentClassID" readonly>
                        <?php if (isset($errorMessages['CurrentClassID'])): ?>
                            <div class="error-message"><?php echo $errorMessages['CurrentClassID']; ?></div>
                        <?php endif; ?>
                    </div>

                        <div class="form-group">
                            <label for="academicYear">Academic Year:</label>
                            <input type="text" class="form-control" id="academicYear" name="academicYear" placeholder="Enter Academic Year" value="<?php echo htmlspecialchars($academicYear); ?>" readonly>
                            <?php if (isset($errorMessages['academicYear'])): ?>
                                <div class="error-message"><?php echo $errorMessages['academicYear']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="class">Class:</label>
                            <?php
                            // Fetch the current class from the database based on the UniqueID
                            if (isset($submittedData['UniqueID'])) {
                                $query = "SELECT CurrentClass FROM students WHERE UniqueID = :UniqueID";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute(['UniqueID' => $submittedData['UniqueID']]);
                                $currentClass = $stmt->fetchColumn();

                                if ($currentClass) {
                                    echo '<select class="form-control" id="class" name="class">';
                                    echo '<option value="' . $currentClass . '" selected>' . $currentClass . '</option>';
                                    echo '<option value="Baby Class">Baby Class (N-1)</option>';
                                    echo '<option value="Middle Class">Middle Class (N-2)</option>';
                                    echo '<option value="Top Class">Top Class (N-3)</option>';
                                    echo '<option value="Primary - One">Primary - One (P-1)</option>';
                                    echo '<option value="Primary - Two">Primary - Two (P-2)</option>';
                                    echo '<option value="Primary - Three">Primary - Three (P-3)</option>';
                                    echo '<option value="Primary - Four">Primary - Four (P-4)</option>';
                                    echo '<option value="Primary - Five">Primary - Five (P-5)</option>';
                                    echo '<option value="Primary - Six">Primary - Six (P-6)</option>';
                                    echo '<option value="Primary - Seven">Primary - Seven (P-7)</option>';
                                    echo '</select>';
                                } else {
                                    echo '<select class="form-control" id="class" name="class" required>';
                                    echo '<option value="Baby Class" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Baby Class' ? 'selected' : '') . '>Baby Class (N-1)</option>';
                                    echo '<option value="Middle Class" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Middle Class' ? 'selected' : '') . '>Middle Class (N-2)</option>';
                                    echo '<option value="Top Class" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Top Class' ? 'selected' : '') . '>Top Class (N-3)</option>';
                                    echo '<option value="Primary - One" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - One' ? 'selected' : '') . '>Primary - One (P-1)</option>';
                                    echo '<option value="Primary - Two" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Two' ? 'selected' : '') . '>Primary - Two (P-2)</option>';
                                    echo '<option value="Primary - Three" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Three' ? 'selected' : '') . '>Primary - Three (P-3)</option>';
                                    echo '<option value="Primary - Four" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Four' ? 'selected' : '') . '>Primary - Four (P-4)</option>';
                                    echo '<option value="Primary - Five" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Five' ? 'selected' : '') . '>Primary - Five (P-5)</option>';
                                    echo '<option value="Primary - Six" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Six' ? 'selected' : '') . '>Primary - Six (P-6)</option>';
                                    echo '<option value="Primary - Seven" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Seven' ? 'selected' : '') . '>Primary - Seven (P-7)</option>';
                                    echo '</select>';
                                }
                            } else {
                                echo '<select class="form-control" id="class" name="class" required>';
                                echo '<option value="Baby Class" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Baby Class' ? 'selected' : '') . '>Baby Class (N-1)</option>';
                                echo '<option value="Middle Class" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Middle Class' ? 'selected' : '') . '>Middle Class (N-2)</option>';
                                echo '<option value="Top Class" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Top Class' ? 'selected' : '') . '>Top Class (N-3)</option>';
                                echo '<option value="Primary - One" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - One' ? 'selected' : '') . '>Primary - One (P-1)</option>';
                                echo '<option value="Primary - Two" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Two' ? 'selected' : '') . '>Primary - Two (P-2)</option>';
                                echo '<option value="Primary - Three" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Three' ? 'selected' : '') . '>Primary - Three (P-3)</option>';
                                echo '<option value="Primary - Four" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Four' ? 'selected' : '') . '>Primary - Four (P-4)</option>';
                                echo '<option value="Primary - Five" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Five' ? 'selected' : '') . '>Primary - Five (P-5)</option>';
                                echo '<option value="Primary - Six" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Six' ? 'selected' : '') . '>Primary - Six (P-6)</option>';
                                echo '<option value="Primary - Seven" ' . (isset($submittedData['class']) && $submittedData['class'] == 'Primary - Seven' ? 'selected' : '') . '>Primary - Seven (P-7)</option>';
                                echo '</select>';
                            }
                            ?>
                            <?php if (isset($errorMessages['class'])): ?>
                                <div class="error-message"><?php echo $errorMessages['class']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="term">Term:</label>
                            <input type="text" class="form-control" id="term" name="term" value="<?php echo htmlspecialchars($currentTerm); ?>" readonly>
                            <?php if (isset($errorMessages['term'])): ?>
                                <div class="error-message"><?php echo $errorMessages['term']; ?></div>
                            <?php endif; ?>
                        </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card mb-3">
                <div class="card-body">

            <div class="form-group">
                <br>
                <label>Student Name ::  </label>
                <span id="StudentName" style="font-weight: bolder;"></span><br>
            </div>

            <!-- ---------------------------------------------------------------------- -->
<div class="form-group subject-list">
    <label>Mandatory Subjects:</label>
    <div id="subjects">
        <p class="loading">Loading Major Subjects . . .</p>
    </div>
</div>

<div class="form-group" id="dynamic_content_placeholder"></div>

<div class="form-group Optional subject-list">
    <label>Optional Subjects:</label>
    <div id="optional_subjects">
        <p class="loading">Loading Optional Subjects . . .</p>
    </div>
</div>



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