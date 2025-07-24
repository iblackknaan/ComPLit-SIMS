<?php
require 'config.php'; // Ensure this path is correct

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $stmt = $pdo->prepare("
        SELECT 'Student' as type, FirstName, LastName, Username, UniqueID, DateOfBirth, Gender, Address, Phone, Email FROM students WHERE FirstName LIKE ? OR LastName LIKE ? OR Username LIKE ? OR UniqueID LIKE ? OR DateOfBirth LIKE ? OR Gender LIKE ? OR Address LIKE ? OR Phone LIKE ? OR Email LIKE ?
        UNION ALL
        SELECT 'Teacher' as type, FirstName, LastName, Username, UniqueID, DateOfBirth, Gender, Address, Phone, Email FROM teachers WHERE FirstName LIKE ? OR LastName LIKE ? OR Username LIKE ? OR UniqueID LIKE ? OR DateOfBirth LIKE ? OR Gender LIKE ? OR Address LIKE ? OR Phone LIKE ? OR Email LIKE ?
        UNION ALL
        SELECT 'Parent' as type, FirstName, LastName, Username, UniqueID, DateOfBirth, Gender, Address, Phone, Email FROM parents WHERE FirstName LIKE ? OR LastName LIKE ? OR Username LIKE ? OR UniqueID LIKE ? OR DateOfBirth LIKE ? OR Gender LIKE ? OR Address LIKE ? OR Phone LIKE ? OR Email LIKE ?
    ");
    $stmt->execute([$query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query, $query]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Display search results
    if ($search_results) {
        echo "<table class='table mx-auto table-padding table-margin mt-3 mb-3' style='border-collapse: collapse;'>
                <tr>
                    <th>FirstName</th>
                    <th>LastName</th>
                    <th>Username</th>
                    <th>UniqueID</th>
                    <th>DateOfBirth</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>                                    
                </tr>";
        foreach ($search_results as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['FirstName']) . "</td>";
            echo "<td>" . htmlspecialchars($user['LastName']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['UniqueID']) . "</td>";
            echo "<td>" . htmlspecialchars($user['DateOfBirth']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Gender']) . "</td>"; 
            echo "<td>" . htmlspecialchars($user['Address']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Phone']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
                       
            echo "</tr>";
        }
        echo "</table>";
        echo "<br>";
echo '<a href="students_list.php" class="btn btn-sm btn-info text-center" style="display: block; margin: 0 auto;">Close Search</a>';
        echo "<br>";
    } else {
        echo "No results found.";
    }
}
?>