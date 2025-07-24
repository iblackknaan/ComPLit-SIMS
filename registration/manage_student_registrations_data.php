<?php
require_once 'config.php';
require_once 'header_admin.php';

// Function to fetch registration data from the database
function fetchRegistrations($pdo, $limit, $offset) {
    $query = "SELECT r.id, r.UniqueID, s.FirstName, s.LastName, r.academic_year, r.term, r.class, r.created_at 
              FROM registrations r 
              LEFT JOIN students s ON r.UniqueID = s.UniqueID 
              LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to count total registrations
function countRegistrations($pdo) {
    $query = "SELECT COUNT(*) FROM registrations";
    $stmt = $pdo->query($query);
    return $stmt->fetchColumn();
}

// Function to delete a specific registration
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $query = "DELETE FROM registrations WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        exit(json_encode(['success' => true, 'message' => 'Registration deleted successfully.']));
    } else {
        exit(json_encode(['success' => false, 'message' => 'Failed to delete registration.']));
    }
}

// Function to update a specific registration
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $term_name = $_POST['term']; // Assuming this is the term name selected by the user
    $class_name = $_POST['class']; // Assuming this is the class name selected by the user

    // Update the registrations table with the selected term and class names
    $query = "UPDATE registrations SET term = :term, class = :class WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':term', $term_name);
    $stmt->bindParam(':class', $class_name);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        // Update the CurrentClass and CurrentTerm in the Students table
        $updateStudentQuery = "UPDATE students SET CurrentClass = :class_name, CurrentTerm = :term_name WHERE UniqueID IN (SELECT UniqueID FROM registrations WHERE id = :id)";
        $updateStudentStmt = $pdo->prepare($updateStudentQuery);
        $updateStudentStmt->bindParam(':class_name', $class_name);
        $updateStudentStmt->bindParam(':term_name', $term_name);
        $updateStudentStmt->bindParam(':id', $id);
        if ($updateStudentStmt->execute()) {
            exit(json_encode(['success' => true, 'message' => 'Registration updated successfully.']));
        } else {
            exit(json_encode(['success' => false, 'message' => 'Failed to update student record.']));
        }
    } else {
        exit(json_encode(['success' => false, 'message' => 'Failed to update registration.']));
    }
}


// Fetch available terms from the database
function fetchTerms($pdo) {
    $query = "SELECT TermID, TermName FROM term";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch available classes from the database
function fetchClasses($pdo) {
    $query = "SELECT ClassID, className FROM classes";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Determine the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$totalRegistrations = countRegistrations($pdo);
$totalPages = ceil($totalRegistrations / $limit);

$registrations = fetchRegistrations($pdo, $limit, $offset);
$terms = fetchTerms($pdo);
$classes = fetchClasses($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students' Registrations Data</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2 class="text-center my-4">Manage Student Registrations Data</h2>
    
    <!-- Registrations Table -->
    <h4 class="mt-4">Registrations</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Unique ID</th>
                    <th>Student Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

<?php foreach ($registrations as $row) { ?>
    <tr id="row_<?php echo $row['id']; ?>">
        <td class="p-0 border-0"><?php echo $row['id']; ?></td>
        <td class="p-0 border-0"><?php echo $row['UniqueID']; ?></td>
        <td class="p-0 border-0"><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
        <td class="p-0 border-0">
            <!-- Update and Delete Registration -->
            <form onsubmit="submitForm(event, <?php echo $row['id']; ?>)">
                <div class="form-row">

                    <div class="col">
                        <select name="class" class="form-control mb-2">
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class) { ?>
                                <option value="<?php echo $class['className']; ?>"><?php echo $class['className']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col">
                        <select name="term" class="form-control mb-2">
                            <option value="">Select Term</option>
                            <?php foreach ($terms as $term) { ?>
                                <option value="<?php echo $term['TermName']; ?>"><?php echo $term['TermName']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                                        
                    <div class="col">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                    <div class="col">
                        <button type="button" onclick="deleteRegistration(<?php echo $row['id']; ?>)" class="btn btn-danger">Delete</button>
                    </div>                    
                </div>
            </form>
        </td>
    </tr>
<?php } ?>

            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++){ ?>
                <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
            <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<?php require_once 'footer.php'; ?>

<script>
    function submitForm(event, id) {
        event.preventDefault();
        
        // Get form data
        var form = event.target;
        var formData = new FormData(form);
        formData.append('id', id);
        formData.append('action', 'update');

        // Display confirmation alert
        if (confirm('Are you sure you want to update this registration?')) {
            // Send AJAX request
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Handle response
                if (data.success) {
                    location.reload(); // Refresh the page
                } else {
                    alert('Failed to update registration: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else {
            // Display cancellation alert
            alert('Update cancelled.');
        }
    }

    function deleteRegistration(id) {
        if (confirm('Are you sure you want to delete this registration?')) {
            // Send AJAX request
            fetch('', {
                method: 'POST',
                body: 'id=' + id + '&action=delete',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Handle response
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload the page
                } else {
                    alert('Failed to delete registration: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }
</script>

</body>
</html>

