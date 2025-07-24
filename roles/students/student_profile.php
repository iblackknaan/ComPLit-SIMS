<?php
// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Include the header
require_once 'header_student.php'; // Adjust the path if necessary

// Check if the user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Retrieve the student details from the database based on the logged-in user's username
$username = $_SESSION['username'];
$sql = "SELECT * FROM students WHERE Username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if student data was retrieved
if (!$student) {
    echo "Error: Student data not found.";
    exit();
}


?>

<div class="container mt-4">
    <div class="row">
                    
    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col">
        <h2 class="text-center">Student Profile</h2>
    </div>

        <div class="col-lg-8 col-md-8 col-md-8 col-sm-8 col">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col bg-danger">
            <h5 class="text-center">Personal Information</h5></div>        
        <div class="col-lg-6 col-md-6 col-md-6 col-sm-6 col">
        <table class="table table-bordered">
            <tr>
                <th>First Name :</th>
                <td><?php echo htmlspecialchars($student['FirstName'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Last Name :</th>
                <td><?php echo htmlspecialchars($student['LastName'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Username :</th>
                <td><?php echo htmlspecialchars($student['Username'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Date of Birth :</th>
                <td><?php echo htmlspecialchars($student['DateOfBirth'] ?? ''); ?></td>
            </tr>            
        </table> 
        </div>
        <div class="col-lg-6 col-md-6 col-md-6 col-sm-6 col">
        <table class="table table-bordered">
            <tr>
                <th>Gender :</th>
                <td><?php echo htmlspecialchars($student['Gender'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Address :</th>
                <td><?php echo htmlspecialchars($student['Address'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Phone :</th>
                <td><?php echo htmlspecialchars($student['Phone'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Email :</th>
                <td><?php echo htmlspecialchars($student['Email'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Enrollment Date :</th>
                <td><?php echo htmlspecialchars($student['EnrollmentDate'] ?? ''); ?></td>
            </tr>
        </table>
        </div>
    </div>            
        </div>
        <div class="col-lg-4 col-md-4 col-md-4 col-sm-4 col ">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col bg-danger">
            <h5 class="text-center">Academic Information</h5></div>
        <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col">
        <table class="table table-bordered">
            <tr>
                <th>Joining Class :</th>
                <td><?php echo htmlspecialchars($student['JoiningClass'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Current Class :</th>
                <td><?php echo htmlspecialchars($student['CurrentClass'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Current Term :</th>
                <td><?php echo htmlspecialchars($student['CurrentTerm'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Current Academic Year :</th>
                <td><?php echo htmlspecialchars($student['CurrentAcademicYear'] ?? ''); ?></td>
            </tr>
        </table>
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
