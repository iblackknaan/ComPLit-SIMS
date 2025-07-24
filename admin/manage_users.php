<?php
// Start session and check if the user is an admin
session_start();
if ($_SESSION['usertype'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Include the database configuration file
require_once 'config.php';

// Include the header file
require_once 'header_admin.php';

// Define array to hold user data
$users = array();

// Retrieve users from the database based on usertype
$sql = "(SELECT StudentID AS id, FirstName AS first_name, LastName AS last_name, Username AS username, 'Student' AS usertype FROM students)
        UNION
        (SELECT TeacherID AS id, FirstName AS first_name, LastName AS last_name, Username AS username, 'Teacher' AS usertype FROM teachers)
        UNION
        (SELECT ParentID AS id, FirstName AS first_name, LastName AS last_name, Username AS username, 'Parent' AS usertype FROM parents)
        UNION
        (SELECT AdminID AS id, FirstName AS first_name, LastName AS last_name, Username AS username, 'Admin' AS usertype FROM admins)";

try {
    // Prepare and execute the SQL query
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all rows as an associative array
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
}

// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <!-- User Management Table -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Users</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>User Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['first_name']; ?></td>
                                <td><?php echo $user['last_name']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['usertype']; ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-dark text-white mt-5 p-3 text-center">
        <div class="container">
            <span>&copy; 2024 KISA NURSERY AND PRIMARY SCHOOL</span>
        </div>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
