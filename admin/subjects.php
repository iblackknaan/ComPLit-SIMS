<?php
session_start();
// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Include the header file
require_once 'header_admin.php';

// Add New Subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subjectName"]) && isset($_POST["description"])) {
    $subjectName = $_POST["subjectName"];
    $description = $_POST["description"];
    
    // Prepare SQL statement to insert new subject
    $insert_sql = "INSERT INTO subjects (SubjectName, Description) VALUES (:subjectName, :description)";
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->bindValue(':subjectName', $subjectName, PDO::PARAM_STR);
    $insert_stmt->bindValue(':description', $description, PDO::PARAM_STR);
    
    // Execute the insert statement
    if ($insert_stmt->execute()) {
        echo "<script>alert('Subject added successfully.'); window.scrollTo(0, 0);</script>";
        // Optionally, you can redirect the user to another page after successful insertion
        // header("Location: index.php");
        // exit();
    } else {
        echo "<script>alert('Error: Unable to add subject.'); window.scrollTo(0, 0);</script>";
    }
}

// Delete Subject
if (isset($_POST["delete_subject"])) {
    $subjectID = $_POST["subjectID"];
    $delete_sql = "DELETE FROM subjects WHERE SubjectID = :subjectID";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->bindValue(':subjectID', $subjectID, PDO::PARAM_INT);
    if ($delete_stmt->execute()) {
        // Delete related tagged classes
        $delete_tagged_sql = "DELETE FROM tagged_classes WHERE SubjectID = :subjectID";
        $delete_tagged_stmt = $pdo->prepare($delete_tagged_sql);
        $delete_tagged_stmt->bindValue(':subjectID', $subjectID, PDO::PARAM_INT);
        $delete_tagged_stmt->execute();
        echo "<script>alert('Subject deleted successfully.'); window.scrollTo(0, 0);</script>";
    } else {
        echo "<script>alert('Error: Unable to delete subject.'); window.scrollTo(0, 0);</script>";
    }
}

// Tag Subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "tag") {
    if (isset($_POST["subjectID"]) && isset($_POST["classID"])) {
        $subjectID = $_POST["subjectID"];
        $classID = $_POST["classID"];
        // Check if the subject is already tagged to the class
        $check_sql = "SELECT * FROM tagged_classes WHERE SubjectID = :subjectID AND ClassID = :classID";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->bindValue(':subjectID', $subjectID, PDO::PARAM_INT);
        $check_stmt->bindValue(':classID', $classID, PDO::PARAM_INT);
        $check_stmt->execute();
        if ($check_stmt->rowCount() > 0) {
            echo "<script>alert('Error: Subject is already tagged to this class.'); window.scrollTo(0, 0);</script>";
        } else {
            // Insert the record into tagged_classes table
            $tag_sql = "INSERT INTO tagged_classes (SubjectID, ClassID) VALUES (:subjectID, :classID)";
            $tag_stmt = $pdo->prepare($tag_sql);
            $tag_stmt->bindValue(':subjectID', $subjectID, PDO::PARAM_INT);
            $tag_stmt->bindValue(':classID', $classID, PDO::PARAM_INT);
            if ($tag_stmt->execute()) {
                echo "<script>alert('Subject tagged to class successfully.'); window.scrollTo(0, 0);</script>";
            } else {
                echo "<script>alert('Error: Unable to tag subject to class.'); window.scrollTo(0, 0);</script>";
            }
        }
    } else {
        echo "<script>alert('Error: Subject ID or Class ID not provided for tagging operation.'); window.scrollTo(0, 0);</script>";
    }
}

// Untag Subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "untag") {
    if (isset($_POST["subjectID"]) && isset($_POST["classID"])) {
        $subjectID = $_POST["subjectID"];
        $classID = $_POST["classID"];
        // Delete the record from tagged_classes table
        $untag_sql = "DELETE FROM tagged_classes WHERE SubjectID = :subjectID AND ClassID = :classID";
        $untag_stmt = $pdo->prepare($untag_sql);
        $untag_stmt->bindValue(':subjectID', $subjectID, PDO::PARAM_INT);
        $untag_stmt->bindValue(':classID', $classID, PDO::PARAM_INT);
        if ($untag_stmt->execute()) {
            echo "<script>alert('Subject untagged from class successfully.'); window.scrollTo(0, 0);</script>";
        } else {
            echo "<script>alert('Error: Unable to untag subject from class.'); window.scrollTo(0, 0);</script>";
        }
    } else {
        echo "<script>alert('Error: Subject ID or Class ID not provided for untagging operation.'); window.scrollTo(0, 0);</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Subjects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include your custom CSS file here if you have one -->
</head>
<body>
    <main>
        <div class="container mt-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4 text-center">
                        <div class="card-header">
                            Add New Subject
                        </div>
                        <div class="card-body">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-row">
        <div class="col-md-4">
            <div class="form-group">
                <input type="text" name="subjectName" class="form-control" placeholder="Subject Name" required>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <textarea name="description" class="form-control" placeholder="Description" required></textarea>
            </div>
        </div>
        <div class="col-md-3 text-center">
            <button type="submit" class="btn btn-primary mx-auto">Add Subject</button>
        </div>
    </div>
</form>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                    // SQL query to fetch subjects
                    $sql = "SELECT * FROM subjects";
                    $stmt = $pdo->query($sql);

                    if ($stmt->rowCount() > 0) {
                        // output data of each row
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<div class='col-lg-4'>";
                            echo "<div class='card mb-4'>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $row["SubjectName"] . "</h5>";
                            echo "<p class='card-text'>" . $row["Description"] . "</p>";
                            echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='post'>";
                            echo "<input type='hidden' name='subjectID' value='" . $row["SubjectID"] . "'>";
                            echo "<button type='submit' name='delete_subject' class='btn btn-sm btn-danger mr-2'>Delete</button>";
                            echo "</form>";
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='subjectID' value='" . $row["SubjectID"] . "'>";
                            echo "<input type='hidden' name='action' value='tag'>";
                            echo "<select name='classID' class='form-control'>";
                            
                            // Fetch classes from the database and embed the logic
                            $classQuery = "SELECT * FROM classes";
                            $classStmt = $pdo->query($classQuery);
                            while ($classRow = $classStmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $classRow['ClassID'] . "'>" . $classRow['ClassName'] . "</option>";
                            }
                            
                            echo "</select>";
                            echo "<button type='submit' class='btn btn-sm btn-primary mt-2'>Tag to Class</button>";
                            echo "</form>";
                            
                            // Fetch already tagged classes for the untag form
                            $taggedClassesQuery = "SELECT tc.ClassID, c.ClassName FROM tagged_classes tc JOIN classes c ON tc.ClassID = c.ClassID WHERE tc.SubjectID = :subjectID";
                            $taggedClassesStmt = $pdo->prepare($taggedClassesQuery);
                            $taggedClassesStmt->bindValue(':subjectID', $row["SubjectID"], PDO::PARAM_INT);
                            $taggedClassesStmt->execute();
                            
                            while ($taggedClass = $taggedClassesStmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<form method='post'>";
                                echo "<input type='hidden' name='subjectID' value='" . $row["SubjectID"] . "'>";
                                echo "<input type='hidden' name='classID' value='" . $taggedClass["ClassID"] . "'>";
                                echo "<input type='hidden' name='action' value='untag'>";
                                echo "<button type='submit' name='untag_subject' class='btn btn-sm btn-danger mt-2'>Untag from " . $taggedClass["ClassName"] . "</button>";
                                echo "</form>";
                            }

                            echo "</div>"; // Close card-body
                            echo "</div>"; // Close card
                            echo "</div>"; // Close col-lg-4
                        }
                    } else {
                        echo "<p>No subjects found.</p>";
                    }
                ?>
            </div> <!-- Close row -->
        </div> <!-- Close container -->
    </main>
    <footer class="mt-4">
        <div class="container">
            <p>© 2024 Your School</p>
        </div>
    </footer>

    <!-- Bootstrap JS and jQuery (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

