<?php
// Include the database configuration file
require_once 'config.php';

// Include the header file
require_once 'header_admin.php';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['StudentSubmit'])) {
        include 'StudentRegistrationForm.php';
    } elseif (isset($_POST['TeacherSubmit'])) {
        include 'TeacherRegistrationForm.php';
    } elseif (isset($_POST['ParentSubmit'])) {
        include 'ParentRegistrationForm.php';
    } 
}
?>


    <div class="container mt-5">
        <h2>Add User</h2>
        <div class="row">
            <!-- Student Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Register Student</h5>
                        <p class="card-text">Click the button below to register a new student.</p>
                        <form method="post">
                            <button type="submit" name="StudentSubmit" class="btn btn-primary">Register Student</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Teacher Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Register Teacher</h5>
                        <p class="card-text">Click the button below to register a new teacher.</p>
                        <form method="post">
                            <button type="submit" name="TeacherSubmit" class="btn btn-primary">Register Teacher</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Parent Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Register Parent</h5>
                        <p class="card-text">Click the button below to register a new parent.</p>
                        <form method="post">
                            <button type="submit" name="ParentSubmit" class="btn btn-primary">Register Parent</button>
                        </form>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
