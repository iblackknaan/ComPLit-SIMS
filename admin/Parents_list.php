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

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $stmt = $pdo->prepare("
        SELECT 'Student' as type, FirstName, LastName, Username, UniqueID, DateOfBirth, Gender, Address, Phone, Email, NULL as HireDate, NULL as Qualification, NULL as Experience FROM students WHERE FirstName LIKE ? OR LastName LIKE ? OR Username LIKE ? OR UniqueID LIKE ? OR DateOfBirth LIKE ? OR Gender LIKE ? OR Address LIKE ? OR Phone LIKE ? OR Email LIKE ?
        UNION ALL
        SELECT 'Teacher' as type, FirstName, LastName, Username, UniqueID, DateOfBirth, Gender, Address, Phone, Email, HireDate, Qualification, Experience FROM teachers WHERE FirstName LIKE ? OR LastName LIKE ? OR Username LIKE ? OR UniqueID LIKE ? OR DateOfBirth LIKE ? OR Gender LIKE ? OR Address LIKE ? OR Phone LIKE ? OR Email LIKE ? OR HireDate LIKE ? OR Qualification LIKE ? OR Experience LIKE ?
        UNION ALL
        SELECT 'Parent' as type, FirstName, LastName, Username, UniqueID, DateOfBirth, Gender, Address, Phone, Email, NULL as HireDate, NULL as Qualification, NULL as Experience FROM parents WHERE FirstName LIKE ? OR LastName LIKE ? OR Username LIKE ? OR UniqueID LIKE ? OR DateOfBirth LIKE ? OR Gender LIKE ? OR Address LIKE ? OR Phone LIKE ? OR Email LIKE ?
    ");

    $params = array_fill(0, 30, $query); // 9 for students, 12 for teachers, 9 for parents
    $stmt->execute($params);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display search results
    if ($search_results) {
        echo "<table class='table mx-auto table-padding table-margin mt-3 mb-3' style='border-collapse: collapse;'>";
        echo "<tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Unique ID</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Hire Date</th>
                <th>Qualification</th>
                <th>Experience</th>
              </tr>";

    foreach ($search_results as $user) {
    echo "<tr>";
    echo "<td>" . ($user['FirstName'] !== null ? htmlspecialchars($user['FirstName']) : '') . "</td>";
    echo "<td>" . ($user['LastName'] !== null ? htmlspecialchars($user['LastName']) : '') . "</td>";
    echo "<td>" . ($user['Username'] !== null ? htmlspecialchars($user['Username']) : '') . "</td>";
    echo "<td>" . ($user['UniqueID'] !== null ? htmlspecialchars($user['UniqueID']) : '') . "</td>";
    echo "<td>" . ($user['DateOfBirth'] !== null ? htmlspecialchars($user['DateOfBirth']) : '') . "</td>";
    echo "<td>" . ($user['Gender'] !== null ? htmlspecialchars($user['Gender']) : '') . "</td>";
    echo "<td>" . ($user['Address'] !== null ? htmlspecialchars($user['Address']) : '') . "</td>";
    echo "<td>" . ($user['Phone'] !== null ? htmlspecialchars($user['Phone']) : '') . "</td>";
    echo "<td>" . ($user['Email'] !== null ? htmlspecialchars($user['Email']) : '') . "</td>";
    echo "<td>" . ($user['HireDate'] !== null ? htmlspecialchars($user['HireDate']) : '') . "</td>"; 
    echo "<td>" . ($user['Qualification'] !== null ? htmlspecialchars($user['Qualification']) : '') . "</td>"; 
    echo "<td>" . ($user['Experience'] !== null ? htmlspecialchars($user['Experience']) : '') . "</td>";            
    echo "</tr>";
        }
        echo "</table>";
        echo "<br>";
echo '<a href="parents_list.php" class="btn btn-sm btn-info text-center" style="display: block; margin: 0 auto;">Close Search</a>';
        echo "<br>";


    } else {
        echo "No results found.";
    }
}
?>

  </div>
</div>
</div>






    <h1 class="text-center">Parents' List</h1>
    <br>
    <?php

    // Pagination variables
    $limit = 10; // Number of records per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
    $offset = ($page - 1) * $limit; // Offset for the query

    // Query to fetch Parents with pagination
    $sql = "SELECT * FROM Parents LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $Parents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display Parents
    if ($Parents) {
      echo "<table class='table mx-auto' style='border-collapse: collapse;'>
                <tr>
                    <th style='border: 1px solid black;'>Parent ID</th>
                    <th style='border: 1px solid black;'>First Name</th>
                    <th style='border: 1px solid black;'>Last Name</th>
                    <th style='border: 1px solid black;'>Username</th>
                    <th style='border: 1px solid black;'>UniqueID</th>  
                    <th style='border: 1px solid black;'>Phone</th>
                    <th style='border: 1px solid black;'>Email</th>
                    <th style='border: 1px solid black;'>Address</th>                                    
                    <th style='border: 1px solid black;'>Date of Birth</th>
                    <th style='border: 1px solid black;'>Gender</th>
                    <th style='border: 1px solid black;'>Relationship To Student</th>                    
                </tr>";
        foreach ($Parents as $Parent) {
            echo "<tr>
                    <td style='border: 1px solid black;'>{$Parent['ParentID']}</td>
                    <td style='border: 1px solid black;'>{$Parent['FirstName']}</td>
                    <td style='border: 1px solid black;'>{$Parent['LastName']}</td>
                    <td style='border: 1px solid black;'>{$Parent['Username']}</td>
                    <td style='border: 1px solid black;'>{$Parent['UniqueID']}</td>
                    <td style='border: 1px solid black;'>{$Parent['Phone']}</td>
                    <td style='border: 1px solid black;'>{$Parent['Email']}</td>
                    <td style='border: 1px solid black;'>{$Parent['Address']}</td>
                    <td style='border: 1px solid black;'>{$Parent['DateOfBirth']}</td>
                    <td style='border: 1px solid black;'>{$Parent['Gender']}</td>
                    <td style='border: 1px solid black;'>{$Parent['RelationshipToStudent']}</td>                    
                </tr>";
        }
        echo "</table>";

    } else {
        echo "No Parents found.";
    }

    // Pagination links
    $sql = "SELECT COUNT(*) AS total FROM Parents";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_pages = ceil($row['total'] / $limit);

    echo "<br><br>";
    echo "<div class='pagination mx-auto'>";
    if ($page > 1) {
      echo "<a href='Parents_list.php?page=" . ($page - 1) . "' class='previous'>Previous</a>";
    }
    for ($i = 1; $i <= $total_pages; $i++) {
      echo "<a href='Teachers_list.php?page=$i' class='page " . ($i == $page ? 'active' : '') . "'>$i</a>";
    }
    if ($page < $total_pages) {
      echo "<a href='Teachers_list.php?page=" . ($page + 1) . "' class='next'>Next</a>";
    }
    echo "</div>";
    ?>




    </body>
    </html>



