<?php
// Include database connection file
include_once 'config.php';

// Log received POST data
error_log('POST Data: ' . print_r($_POST, true));

if (isset($_POST['class_name'])) {
    $class_name = $_POST['class_name'];

    // Log the class name to ensure it's being received correctly
    error_log('Decoded class name: ' . $class_name);

    // Prepare the query
    $sql = "SELECT StudentID, CONCAT(FirstName, ' ', LastName) AS student_name FROM students WHERE CurrentClass = :class_name";

    try {
        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Log the SQL query and parameters
        error_log('Executing SQL: ' . $sql);
        error_log('With parameter: ' . $class_name);

        // Bind parameters
        $stmt->bindParam(':class_name', $class_name, PDO::PARAM_STR);

        // Execute statement
        $stmt->execute();

        // Fetch results
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Log the fetched students data
        error_log('Fetched Students: ' . print_r($students, true));

        // Check if results are found
        if ($students) {
            // Add checkboxes for each student
            foreach ($students as &$student) {
                $student['checkbox'] = '<input type="checkbox" name="student_ids[]" value="' . $student['StudentID'] . '">';
            }
            echo json_encode($students);
        } else {
            echo json_encode(array('error' => 'The class you have selected has no students currently registered to it.'));
        }
    } catch (PDOException $e) {
        // Log the error to a file for debugging purposes
        error_log('Database error: ' . $e->getMessage(), 0);
        // Return a JSON response indicating the error
        echo json_encode(array('error' => 'Database error'));
    }
} else {
    echo json_encode(array('error' => 'Class name not set'));
}
?>
