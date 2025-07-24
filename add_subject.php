<?php
// Include the database connection
require_once 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if subjectName and description are set and not empty
    if (isset($_POST["subjectName"]) && isset($_POST["description"]) && !empty($_POST["subjectName"]) && !empty($_POST["description"])) {
        // Prepare an SQL statement to insert a new subject
        $sql = "INSERT INTO subjects (SubjectName, Description) VALUES (?, ?)";
        
        // Prepare the SQL statement for execution
        $stmt = $pdo->prepare($sql);
        
        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("ss", $subjectName, $description);
            
            // Set parameters
            $subjectName = $_POST["subjectName"];
            $description = $_POST["description"];
            
            // Execute the statement
            if ($stmt->execute()) {
                // Subject added successfully
                echo "Subject added successfully.";
            } else {
                // Error in executing the statement
                echo "Error: Unable to add subject.";
            }
        } else {
            // Error in preparing the statement
            echo "Error: Unable to prepare statement.";
        }
        
        // Close the statement
        $stmt->close();
    } else {
        // SubjectName or description not provided
        echo "Error: SubjectName or description not provided.";
    }
}
?>