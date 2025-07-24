<?php
// Include the header
require_once 'header_teacher.php'; // Adjust the path if necessary
?>


<div class="container">
    <div class="jumbotron mt-4">
        <h1 class="display-4">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <p class="lead">This is your Teacher Dashboard.</p>
        <!-- Display the teacherID -->
        <p>Your Teacher ID: <?php echo $_SESSION['userID']; ?></p>
        <hr class="my-4">
        <p class="lead">
            <!-- Add links to various features here -->
            <!-- Example: <a class="btn btn-primary btn-lg" href="assignment_management.php" role="button">Assignment Management</a> -->
        </p>
    </div>
</div>

<!-- Include footer if necessary -->
<?php require_once 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
