<?php
// Start session and check if the user is an admin
session_start();

require_once 'config.php';

// Check if the user is an admin
if ($_SESSION['usertype'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Include the database configuration file
require_once 'config.php';

// Check if user ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

// Retrieve user ID from the URL
$user_id = $_GET['id'];

// Retrieve user's type from the database
try {
    $sql = "SELECT UserType FROM (
                SELECT StudentID AS id, 'student' AS UserType FROM students
                UNION
                SELECT TeacherID AS id, 'teacher' AS UserType FROM teachers
                UNION
                SELECT ParentID AS id, 'parent' AS UserType FROM parents
                UNION
                SELECT AdminID AS id, 'admin' AS UserType FROM admins
            ) AS users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_type = $stmt->fetchColumn();
} catch (PDOException $e) {
    echo "Error fetching user details: " . $e->getMessage();
    exit();
}

if (!$user_type) {
    echo "User not found!";
    exit();
}

// Prompt confirmation before deletion
echo "<script>
        var result = confirm('Are you sure you want to delete this user?');
        if (!result) {
            window.location.href = 'manage_users.php';
        }
      </script>";

// Delete user from the appropriate table based on user type
try {
    switch ($user_type) {
        case 'student':
            $sql = "DELETE FROM students WHERE StudentID = :id";
            break;
        case 'teacher':
            $sql = "DELETE FROM teachers WHERE TeacherID = :id";
            break;
        case 'parent':
            $sql = "DELETE FROM parents WHERE ParentID = :id";
            break;
        case 'admin':
            $sql = "DELETE FROM admins WHERE AdminID = :id";
            break;
        default:
            echo "Invalid user type!";
            exit();
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to manage_users.php after deletion
    header("Location: manage_users.php");
    exit();
} catch (PDOException $e) {
    echo "Error deleting user: " . $e->getMessage();
    exit();
}
?>