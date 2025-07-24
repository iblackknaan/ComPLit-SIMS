<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'parent') {
    header("Location: index.php");
    exit();
}

// Include the header
require_once 'header_parent.php'; // Adjust the path if necessary
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to Bootstrap CSS -->
    <link rel="stylesheet" href="styles.css"> <!-- Link to your custom CSS file -->

</head>
<body>

    <header class="bg-light py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6 text-right">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="dashboard-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="overview">
                            <h2>Student Overview</h2>
                            <div class="student-info">
                                <img src="student_photo.jpg" alt="Student Photo" class="img-thumbnail">
                                <div class="info">
                                    <h3>Student Name</h3>
                                    <p>Class: Grade X, Section A</p>
                                    <p>Roll Number: XXX</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attendance">
                            <h2>Attendance Monitoring</h2>
                            <div class="attendance-table">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Attendance records will be dynamically generated here -->
                                        <tr>
                                            <td>2024-05-01</td>
                                            <td>P</td>
                                        </tr>
                                        <!-- Add more rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- More Sections Here (Academic Performance, School Announcements, etc.) -->
    </main>

    <footer class="bg-light py-4">
        <div class="container text-center">
            <p>&copy; 2024 School Name. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Bootstrap JavaScript dependencies -->
</body>
</html>
