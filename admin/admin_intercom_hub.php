<?php
// Start the session
session_start();

// Include the database configuration file
require_once 'config.php';

// Include the header file
require_once 'header_admin.php';

// Set the usertype session variable for admin
$_SESSION['usertype'] = 'Admin';

// Check if the session variable 'usertype' is set
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'Admin') {
    // Fetch stakeholders from database
    $students = $pdo->query("SELECT CONCAT(FirstName, ' ', LastName) AS FullName, StudentID FROM students")->fetchAll();
    $teachers = $pdo->query("SELECT CONCAT(FirstName, ' ', LastName) AS FullName, TeacherID FROM teachers")->fetchAll();
    $parents = $pdo->query("SELECT CONCAT(FirstName, ' ', LastName) AS FullName, ParentID FROM parents")->fetchAll();
}

// Display success or error message
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']); // Clear the message after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-scrollable {
            max-height: 300px; /* Adjust this height as needed */
            overflow-y: auto; /* Enable vertical scrolling */
        }
    </style>
</head>
<body>
    <h2 style="color: #007bff; text-align: center;">Admin Dashboard</h2>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <!-- Card 1: Message Composition -->
                <div class="card" style="width: 100%; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Message Composition</h5>
                        <form action="admin_send_intercom_message.php" method="post">
                            <div class="form-group">
                                <label for="stakeholder_id">Select Stakeholder:</label>
                                <select name="stakeholder_id" id="stakeholder_id" class="form-control">
                                    <?php foreach ($students as $student) { ?>
                                        <option value="<?php echo $student['StudentID']; ?>"><?php echo $student['FullName']; ?></option>
                                    <?php } ?>
                                    <?php foreach ($teachers as $teacher) { ?>
                                        <option value="<?php echo $teacher['TeacherID']; ?>"><?php echo $teacher['FullName']; ?></option>
                                    <?php } ?>
                                    <?php foreach ($parents as $parent) { ?>
                                        <option value="<?php echo $parent['ParentID']; ?>"><?php echo $parent['FullName']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="message">Message:</label>
                                <textarea name="message" id="message" class="form-control" placeholder="Type your message here..."></textarea>
                            </div>
                            <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>

                <!-- Card 3: Stakeholder Management -->
                <div class="card" style="width: 100%; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Stakeholder Management</h5>
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-6">
                                <input type="text" id="stakeholder-search" placeholder="Search for a stakeholder" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <button id="stakeholder-search-btn" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                        <div class="row" style="display: flex; flex-wrap: wrap; justify-content: center;">
                            <!-- Students subset card -->
                            <div class="col-md-4">
                                <div class="card" style="width: 100%; margin: 10px;">
                                    <div class="card-body card-scrollable">
                                        <h6>Students</h6>
                                        <ul id="students-list" style="list-style: none; padding: 0; margin: 0;">
                                            <?php foreach ($students as $student) { ?>
                                                <li style="padding: 10px; border-bottom: 1px solid #ccc;"><?php echo $student['FullName']; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Teachers subset card -->
                            <div class="col-md-4">
                                <div class="card" style="width: 100%; margin: 10px;">
                                    <div class="card-body card-scrollable">
                                        <h6>Teachers</h6>
                                        <ul id="teachers-list" style="list-style: none; padding: 0; margin: 0;">
                                            <?php foreach ($teachers as $teacher) { ?>
                                                <li style="padding: 10px; border-bottom: 1px solid #ccc;"><?php echo $teacher['FullName']; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Parents subset card -->
                            <div class="col-md-4">
                                <div class="card" style="width: 100%; margin: 10px;">
                                    <div class="card-body card-scrollable">
                                        <h6>Parents</h6>
                                        <ul id="parents-list" style="list-style: none; padding: 0; margin: 0;">
                                            <?php foreach ($parents as $parent) { ?>
                                                <li style="padding: 10px; border-bottom: 1px solid #ccc;"><?php echo $parent['FullName']; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Card 2: Private Conversation Monitoring -->
                <div class="card" style="width: 100%; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Private Conversation Monitoring</h5>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <!-- Display list of private conversations -->
                        </ul>
                    </div>
                </div>

                <!-- Card 4: Reporting and Analytics -->
                <div class="card" style="width: 100%; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Reporting and Analytics</h5>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <!-- Display reporting and analytics data -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer section -->
    <?php require_once 'footer.php'; ?>
</body>
</html>
