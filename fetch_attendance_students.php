<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classID = $_POST['classID'] ?? null;
    $subjectID = $_POST['subjectID'] ?? null;
    $termID = $_POST['termID'] ?? null;
    $academicYear = $_POST['academicYear'] ?? null;

    // Check if all required fields are provided
    if (!$classID || !$subjectID || !$termID || !$academicYear) {
        echo json_encode(['error' => 'Please ensure all fields are filled.']);
        exit;
    }

    try {
        // Fetch the TermName from TermID
        $termStmt = $pdo->prepare("SELECT TermName FROM term WHERE TermID = :termID");
        $termStmt->execute([':termID' => $termID]);
        $term = $termStmt->fetch(PDO::FETCH_ASSOC);
        $termName = $term ? $term['TermName'] : '';

        if ($termName === '') {
            echo json_encode(['error' => 'Invalid TermID. No term found.']);
            exit;
        }

        // Prepare and execute the SQL query to fetch students
        $sql = "
            SELECT s.StudentID, CONCAT(s.FirstName, ' ', s.LastName) AS StudentName
            FROM students s
            JOIN registrations r ON s.StudentID = r.StudentID
            JOIN registration_subjects rs ON r.RegistrationsID = rs.RegistrationsID
            WHERE r.CurrentClassID = :classID
              AND rs.SubjectID = :subjectID
              AND r.TermName = :termName
              AND r.academic_year = :academicYear
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':classID' => $classID,
            ':subjectID' => $subjectID,
            ':termName' => $termName,
            ':academicYear' => $academicYear
        ]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($students);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
