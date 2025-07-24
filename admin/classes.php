<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['add_class'])) {
            // Validate and sanitize inputs
            $className = htmlspecialchars($_POST['class_name'], ENT_QUOTES, 'UTF-8');
            $classRoomNumber = htmlspecialchars($_POST['class_room_number'], ENT_QUOTES, 'UTF-8');
            
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO classes (ClassName, ClassRoomNumber) VALUES (?, ?)");
            $stmt->execute([$className, $classRoomNumber]);
            $message = "Class added successfully!";
        } elseif (isset($_POST['update_class'])) {
            // Validate and sanitize inputs
            $classID = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
            $className = htmlspecialchars($_POST['class_name'], ENT_QUOTES, 'UTF-8');
            $classRoomNumber = htmlspecialchars($_POST['class_room_number'], ENT_QUOTES, 'UTF-8');
            
            // Update class in the database
            $stmt = $pdo->prepare("UPDATE classes SET ClassName = ?, ClassRoomNumber = ? WHERE ClassID = ?");
            $stmt->execute([$className, $classRoomNumber, $classID]);
            $message = "Class updated successfully!";
        } elseif (isset($_POST['delete_class'])) {
            // Validate and sanitize input
            $classID = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
            
            // Delete class from the database
            $stmt = $pdo->prepare("DELETE FROM classes WHERE ClassID = ?");
            $stmt->execute([$classID]);
            $message = "Class deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}

// Pagination setup
$classes_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $classes_per_page) - $classes_per_page : 0;

// Search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8');
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE ClassName LIKE ? OR ClassRoomNumber LIKE ? LIMIT ?, ?");
    $stmt->execute(['%' . $search_query . '%', '%' . $search_query . '%', $start, $classes_per_page]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM classes LIMIT ?, ?");
    $stmt->execute([$start, $classes_per_page]);
}
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of classes for pagination
$total_classes = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$total_pages = ceil($total_classes / $classes_per_page);

// Include the header
require_once 'header_admin.php'; // Adjust the path if necessary
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        .container {
            margin-top: 30px;
        }
        .message, .error {
            margin-top: 15px;
        }
        .form-inline .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Manage Classes</h1>

        <?php if ($message): ?>
            <div class="alert alert-success message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h2>Add Class</h2>
        <form method="post" class="form-inline mb-4">
            <div class="row w-100">
                <div class="form-group col-md-4">
                    <label for="class_name">Class Name:</label>
                    <input type="text" class="form-control ml-2" id="class_name" name="class_name" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="class_room_number">Class Room Number:</label>
                    <input type="text" class="form-control ml-2" id="class_room_number" name="class_room_number" required>
                </div>
                <div class="form-group col-md-4 d-flex align-items-end">
                    <button type="submit" name="add_class" class="btn btn-primary ml-2">Add Class</button>
                </div>
            </div>
        </form>

        <h2>Classes List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Class Room Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?php echo $class['ClassID']; ?></td>
                        <td><?php echo $class['ClassName']; ?></td>
                        <td><?php echo $class['ClassRoomNumber']; ?></td>
                        <td>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-info mr-2" onclick="populateUpdateForm('<?php echo $class['ClassID']; ?>', '<?php echo $class['ClassName']; ?>', '<?php echo $class['ClassRoomNumber']; ?>')">Edit</button>
                                <form method="post">
                                    <input type="hidden" name="class_id" value="<?php echo $class['ClassID']; ?>">
                                    <button type="submit" name="delete_class" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 id="updateSection" style="display: none;">Update Class</h2>
        <form method="post" id="updateForm" class="form-inline" style="display: none;">
            <input type="hidden" id="update_class_id" name="class_id">
            <div class="row w-100">
                <div class="form-group col-md-4">
                    <label for="update_class_name">Class Name:</label>
                    <input type="text" class="form-control ml-2" id="update_class_name" name="class_name" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="update_class_room_number">Class Room Number:</label>
                    <input type="text" class="form-control ml-2" id="update_class_room_number" name="class_room_number" required>
                </div>
                <div class="form-group col-md-4 d-flex align-items-end">
                    <button type="submit" name="update_class" class="btn btn-primary ml-2">Update Class</button>
                </div>
            </div>
        </form>

        <!-- Pagination -->
        <div class='mx-auto text-center'>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($total_pages > 1): ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item<?php if ($i == $page) echo ' active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Add Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        function populateUpdateForm(classID, className, classRoomNumber) {
            document.getElementById('update_class_id').value = classID;
            document.getElementById('update_class_name').value = className;
            document.getElementById('update_class_room_number').value = classRoomNumber;
            document.getElementById('updateForm').style.display = 'block';
            document.getElementById('updateSection').style.display = 'block';
            window.scrollTo(0, document.getElementById('updateForm').offsetTop);
        }
    </script>
</body>
</html>
