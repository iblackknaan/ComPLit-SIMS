<?php
// Include the database configuration file
require_once 'config.php';

// Include the header file
require_once 'header_admin.php';
?>

<head>
    <style>
        .table {
          border-collapse: collapse;
        }
        .table, .table th, .table td {
          border: 1px solid black;
        }
        .pagination {
          display: flex;
          justify-content: center;
          align-items: center;
          margin-top: 20px;
        }
        .pagination a {
          margin: 0 5px;
          padding: 5px 10px;
          border-radius: 5px;
          background-color: #ddd;
          color: #333;
          text-decoration: none;
        }
        .pagination a:hover {
          background-color: #ccc;
        }
        .pagination .active {
          background-color: #333;
          color: #fff;
        }
        .previous, .next {
          margin: 0 10px;
          font-size: 18px;
          font-weight: bold;
        }
        .previous:hover, .next:hover {
          color: #fff;
        }
    </style>

</head>
<body>
    
<br>

<div class="container-fluid">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-12">
<?php
// Include the database configuration file
include 'SearchSystemUsers.php';
?>
  </div>
</div>
</div>


<h1 class="text-center">Students List</h1>
<br>
<?php

// Pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for the query

// Query to fetch students with pagination
$sql = "SELECT * FROM students LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display students
if ($students) {
  echo "<table class='table mx-auto' style='border-collapse: collapse;'>
            <tr>
                <th style='border: 1px solid black;'>Student ID</th>
                <th style='border: 1px solid black;'>First Name</th>
                <th style='border: 1px solid black;'>Last Name</th>
                <th style='border: 1px solid black;'>Username</th>
                <th style='border: 1px solid black;'>Date of Birth</th>
                <th style='border: 1px solid black;'>Gender</th>
                <th style='border: 1px solid black;'>Address</th>
                <th style='border: 1px solid black;'>Phone</th>
                <th style='border: 1px solid black;'>Email</th>
                <th style='border: 1px solid black;'>Enrollment Date</th>
                <th style='border: 1px solid black;'>Joining Class</th>
                <th style='border: 1px solid black;'>Current Class</th>
                <th style='border: 1px solid black;'>Current Academic Year</th>
            </tr>";
    foreach ($students as $student) {
        echo "<tr>
                <td style='border: 1px solid black;'>{$student['StudentID']}</td>
                <td style='border: 1px solid black;'>{$student['FirstName']}</td>
                <td style='border: 1px solid black;'>{$student['LastName']}</td>
                <td style='border: 1px solid black;'>{$student['Username']}</td>
                <td style='border: 1px solid black;'>{$student['DateOfBirth']}</td>
                <td style='border: 1px solid black;'>{$student['Gender']}</td>
                <td style='border: 1px solid black;'>{$student['Address']}</td>
                <td style='border: 1px solid black;'>{$student['Phone']}</td>
                <td style='border: 1px solid black;'>{$student['Email']}</td>
                <td style='border: 1px solid black;'>{$student['EnrollmentDate']}</td>
                <td style='border: 1px solid black;'>{$student['JoiningClass']}</td>
                <td style='border: 1px solid black;'>{$student['CurrentClass']}</td>
                <td style='border: 1px solid black;'>{$student['CurrentAcademicYear']}</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No students found.";
}

// Pagination links
$sql = "SELECT COUNT(*) AS total FROM students";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_pages = ceil($row['total'] / $limit);

echo "<br><br>";
echo "<div class='pagination mx-auto'>";
if ($page > 1) {
  echo "<a href='students_list.php?page=" . ($page - 1) . "' class='previous'>Previous</a>";
}
for ($i = 1; $i <= $total_pages; $i++) {
  echo "<a href='students_list.php?page=$i' class='page " . ($i == $page ? 'active' : '') . "'>$i</a>";
}
if ($page < $total_pages) {
  echo "<a href='students_list.php?page=" . ($page + 1) . "' class='next'>Next</a>";
}
echo "</div>";
?>

</body>
</html>
