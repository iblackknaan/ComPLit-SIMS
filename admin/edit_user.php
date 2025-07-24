<?php
// Start session and check if the user is an admin
session_start();

require_once 'config.php';

// Check if the user is an admin
if ($_SESSION['usertype'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if user ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

// Initialize $user variable
$user = null;

// Retrieve user details from the database based on user ID
try {
    $sql = "SELECT * FROM (
                SELECT StudentID AS id, FirstName, LastName, Username, Password, 'Student' AS usertype FROM students
                UNION
                SELECT TeacherID AS id, FirstName, LastName, Username, Password, 'Teacher' AS usertype FROM teachers
                UNION
                SELECT ParentID AS id, FirstName, LastName, Username, Password, 'Parent' AS usertype FROM parents
                UNION
                SELECT AdminID AS id, FirstName, LastName, Username, Password, 'Admin' AS usertype FROM admins
            ) AS users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching user details: " . $e->getMessage();
    exit();
}

// Check if user exists
if (!$user) {
    echo "User not found!";
    exit();
}

// Initialize variables to hold user details
$id = $user['id'];
$firstName = $user['FirstName'];
$lastName = $user['LastName'];
$username = $user['Username'];
$password = $user['Password'];
$usertype = $user['usertype'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = $_POST['id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $newPassword = $_POST['password'];

    // Hash the password if a new one is provided, else use the existing one
    if (!empty($newPassword)) {
        $password = password_hash($newPassword, PASSWORD_BCRYPT);
    }

    // Update user details in the database
    try {
        $sql = "UPDATE ";
        if ($usertype === 'Student') {
            $sql .= "students ";
            $idColumnName = "StudentID";
        } elseif ($usertype === 'Teacher') {
            $sql .= "teachers ";
            $idColumnName = "TeacherID";
        } elseif ($usertype === 'Parent') {
            $sql .= "parents ";
            $idColumnName = "ParentID";
        } elseif ($usertype === 'Admin') {
            $sql .= "admins ";
            $idColumnName = "AdminID";
        }
        $sql .= "SET FirstName = :firstName, LastName = :lastName, Username = :username, Password = :password WHERE $idColumnName = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect to manage_users.php after updating
        header("Location: manage_users.php");
        exit();
    } catch (PDOException $e) {
        echo "Error updating record: " . $e->getMessage();
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!-- Navbar content -->
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Edit User</h2>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                    </div>

                    <!-- Additional fields based on user type -->
                    <!-- You can add additional fields here if needed -->

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
