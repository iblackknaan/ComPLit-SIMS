<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to Bootstrap CSS -->
    <link rel="stylesheet" href="styles.css"> <!-- Link to your custom CSS file -->
    <style>
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand,
        .nav-link,
        .form-inline .btn {
            color: #fff;
        }
        .navbar-brand:hover,
        .nav-link:hover,
        .form-inline .btn:hover {
            color: #adb5bd;
        }
        .navbar-toggler-icon {
            filter: invert(1);
        }
        .dashboard-section {
            padding: 50px 0;
        }
        .overview, .attendance {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .student-info {
            display: flex;
            align-items: center;
        }
        .student-info img {
            width: 100px;
            height: auto;
            margin-right: 20px;
            border-radius: 50%;
        }
        .attendance-table {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="school_logo.png" width="30" height="30" class="d-inline-block align-top" alt="School Logo">
    School Name
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="parent_dashboard.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="student_overview.php">Student Overview</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="attendance_monitoring.php">Attendance Monitoring</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="academic_performance.php">Academic Performance</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="school_announcements.php">School Announcements</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="assignment_tracking.php">Assignment Tracking</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="communication_channels.php">Communication Channels</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <button class="btn btn-outline-success my-2 my-sm-0" type="button" onclick="location.href='logout.php';">Logout</button>
    </form>
  </div>
</nav>

<main>
  <!-- Main content goes here -->
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap JavaScript dependencies -->
</body>
</html>
