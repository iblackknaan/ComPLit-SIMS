// Save as display_subjects.php
<?php
// Include the database configuration file
require_once 'config.php';

// Function to fetch and display subjects for a specific class and term
function fetchAndDisplaySubjects($pdo, $classID, $termID) {
    try {
        // Define the SQL query to fetch subjects
        $query = "SELECT s.SubjectID, s.SubjectName
                  FROM class_subjects cs
                  JOIN subjects s ON cs.SubjectID = s.SubjectID
                  WHERE cs.ClassID = :classID AND cs.TermID = :termID";
        
        // Prepare the SQL statement
        $stmt = $pdo->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':classID', $classID, PDO::PARAM_INT);
        $stmt->bindParam(':termID', $termID, PDO::PARAM_INT);
        
        // Execute the statement
        $stmt->execute();
        
        // Fetch all results
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check if subjects were found
        if ($subjects) {
            foreach ($subjects as $subject) {
                echo htmlspecialchars($subject['SubjectName']) . "<br>";
            }
        } else {
            echo "No subjects found for the selected class and term.";
        }
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

// Replace with actual ClassID and TermID
$classID = 1; // Example ClassID
$termID = 1;  // Example TermID

// Call the function to fetch and display subjects
fetchAndDisplaySubjects($pdo, $classID, $termID);
?>
