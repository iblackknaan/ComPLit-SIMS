<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2); /* Primary blue shadow */
            transition: 0.3s;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 123, 255, 0.4); /* Darker blue shadow on hover */
        }
    </style>
</head>
<body>

<?php
require_once 'config.php';
require_once 'header_admin.php';
?>

<div class="container">
    <div class="row">
        <!-- Change Password Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Change Password</h5>
                    <p class="card-text">Allow admins to change their passwords.</p>
                    <a href="change_password.php" class="btn btn-primary">Change Admin Password</a>
                </div>
            </div>
        </div>

        <!-- Update Profile Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update Profile Information</h5>
                    <p class="card-text">Enable admins to update their profile information.</p>
                    <a href="update_profile.php" class="btn btn-primary">Update Admin Profile</a>
                </div>
            </div>
        </div>

        <!-- Theme Settings Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Theme Settings</h5>
                    <p class="card-text">Allow admins to change the theme of the dashboard.</p>
                    <a href="theme_settings.php" class="btn btn-primary">Theme Settings</a>
                </div>
            </div>
        </div>

        <!-- Language Settings Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Language Settings</h5>
                    <p class="card-text">Provide an option for admins to select their preferred language.</p>
                    <a href="language_settings.php" class="btn btn-primary">Language Settings</a>
                </div>
            </div>
        </div>

        <!-- Backup and Restore Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Backup and Restore</h5>
                    <p class="card-text">Allow admins to backup and restore the dashboard data.</p>
                    <a href="backup_restore.php" class="btn btn-primary">Backup and Restore</a>
                </div>
            </div>
        </div>

        <!-- Update, Edit, or Clear Students Data Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update, Edit, or Clear Students Data</h5>
                    <p class="card-text">Manage data related to students.</p>
                    <a href="manage_students_data.php" class="btn btn-primary">Manage Students Data</a>
                </div>
            </div>
        </div>

        <!-- Update, Edit, or Clear Registrations Data Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update, Edit, or Clear Registrations Data</h5>
                    <p class="card-text">Manage data related to student registrations.</p>
                    <a href="manage_registrations_data.php" class="btn btn-primary">Manage Registrations Data</a>
                </div>
            </div>
        </div>

        <!-- Update, Edit, or Clear Courses Data Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update, Edit, or Clear Courses Data</h5>
                    <p class="card-text">Manage data related to courses.</p>
                    <a href="manage_courses_data.php" class="btn btn-primary">Manage Courses Data</a>
                </div>
            </div>
        </div>

        <!-- Update, Edit, or Clear Registration Data Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update, Edit, or Clear Registration Data</h5>
                    <p class="card-text">Manage data related to registrations.</p>
                    <a href="manage_student_registrations_data.php" class="btn btn-primary">Manage Registration Data</a>
                </div>
            </div>
        </div>
        <!-- Update, Edit, or Clear Time Table -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update, Edit, or Clear Time Table</h5>
                    <p class="card-text">Manage data related to the Time Table.</p>
                    <a href="TimeTableZone.php" class="btn btn-primary">Manage Time Table</a>
                </div>
            </div>
        </div>        

        <!-- Add more cards here -->    
    </div>
</div>

<?php require_once 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
