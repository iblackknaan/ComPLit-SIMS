<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classID = $_POST['classID'] ?? null;
    $subjectID = $_POST['subjectID'] ?? null;
    $termID = $_POST['termID'] ?? null;
    $academicYear = $_POST['academicYear'] ?? null;
    $attendance = $_POST['attendance'] ?? null;

    // Validate input
    if (!$classID || !$subjectID || !$termID || !$academicYear || !$attendance) {
        $data = array('error' => 'Please ensure all fields are filled correctly.');
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Create DateTime object with timezone
    $currentDateTime = (new DateTime('now', new DateTimeZone('Africa/Kampala')))->format('Y-m-d H:i:s');

    // Prepare the final data to be inserted
    $cleanedAttendanceData = json_decode($attendance, true);

    try {
        $pdo->beginTransaction();

        foreach ($cleanedAttendanceData as $studentID => $status) {
            // Check if a record already exists for the same class, subject, term, academic year, and date
            $stmt = $pdo->prepare("SELECT 1 FROM attendance WHERE StudentID = :studentID AND SubjectID = :subjectID AND ClassID = :classID AND TermID = :termID AND AcademicYear = :academicYear AND DATE(DateTime) = DATE(:dateTime)");
            $stmt->execute([
                ':studentID' => $studentID,
                ':subjectID' => $subjectID,
                ':classID' => $classID,
                ':termID' => $termID,
                ':academicYear' => $academicYear,
                ':dateTime' => $currentDateTime
            ]);

            if ($stmt->fetchColumn()) {
                // Record already exists, return an error message
                $data = array('error' => 'This class attendance has already been recorded.');
                header('Content-Type: application/json');
                echo json_encode($data);
                $pdo->rollBack();
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO attendance (StudentID, SubjectID, ClassID, TermID, AcademicYear, DateTime, Status)
                VALUES (:studentID, :subjectID, :classID, :termID, :academicYear, :dateTime, :status)
                ON DUPLICATE KEY UPDATE Status = VALUES(Status), DateTime = VALUES(DateTime)
            ");
            $params = [
                ':studentID' => $studentID,
                ':subjectID' => $subjectID,
                ':classID' => $classID,
                ':termID' => $termID,
                ':academicYear' => $academicYear,
                ':dateTime' => $currentDateTime,
                ':status' => $status
            ];

            // Debugging output
            error_log("Current DateTime: " . $currentDateTime);
            error_log("Status: " . $status);
            error_log("Executing query with params: " . print_r($params, true)); // Log parameters

            $stmt->execute($params);
        }

        $pdo->commit();
        $data = array('success' => 'Attendance records saved successfully.');
        header('Content-Type: application/json');
        echo json_encode($data);
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('Database error: ' . $e->getMessage()); // Log the error
        $data = array('error' => 'Database error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($data);
    }
} else {
    $data = array('error' => 'Invalid request method.');
    header('Content-Type: application/json');
    echo json_encode($data);
}