<?php
require_once 'config.php';

header('Content-Type: application/json');

// Update the required fields array
$requiredFields = ['StudentID', 'UniqueID', 'academicYear', 'term', 'CurrentClassID', 'otherNurseryAreas'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required field: ' . $field]);
        exit;
    }
}

$studentID = $_POST['StudentID'];
$uniqueID = $_POST['UniqueID'];
$academicYear = $_POST['academicYear'];
$term = $_POST['term'];
$currentClassID = $_POST['CurrentClassID'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert into registrations table
    $stmt = $pdo->prepare("
        INSERT INTO registrations (StudentID, UniqueID, academic_year, TermName, CurrentClassID) 
        VALUES (:StudentID, :UniqueID, :academic_year, :TermName, :CurrentClassID)
    ");
    $stmt->execute([
        ':StudentID' => $studentID,
        ':UniqueID' => $uniqueID,
        ':academic_year' => $academicYear,
        ':TermName' => $term,
        ':CurrentClassID' => $currentClassID
    ]);

    $registrationsID = $pdo->lastInsertId();

    // Debugging: Log received POST data
    error_log("Received POST Data: " . print_r($_POST, true));

    // Check and process subjects
    if (isset($_POST['subjects']) && !empty($_POST['subjects'])) {
        $subjectsData = json_decode($_POST['subjects'], true);

        if (is_array($subjectsData)) {
            foreach ($subjectsData as $subject) {
                if (isset($subject['SubjectID']) && isset($subject['SubjectName'])) {
                    $subjectID = $subject['SubjectID'];
                    $subjectName = $subject['SubjectName'];

                    // Debugging: Log each subject being inserted
                    error_log("Inserting Subject: " . print_r($subject, true));

                    // Apply the fix: Use INSERT IGNORE to avoid duplicate records
                    $stmt = $pdo->prepare("
                        INSERT IGNORE INTO registration_subjects (RegistrationsID, StudentID, SubjectID, SubjectName)
                        VALUES (:RegistrationsID, :StudentID, :SubjectID, :SubjectName)
                    ");
                    $stmt->execute([
                        ':RegistrationsID' => $registrationsID,
                        ':StudentID' => $studentID,
                        ':SubjectID' => $subjectID,
                        ':SubjectName' => $subjectName
                    ]);
                } else {
                    // Debugging: Log invalid subject data
                    error_log("Invalid subject data: " . print_r($subject, true));
                }
            }
        } else {
            error_log("Subjects data is not an array.");
        }
    } else {
        // Debugging: Log missing subjects
        error_log("No subjects data received.");
    }

    // Check and process other nursery areas of concern
    if (isset($_POST['otherNurseryAreas']) && !empty($_POST['otherNurseryAreas'])) {
        $otherNurseryAreas = json_decode($_POST['otherNurseryAreas'], true);

        if (is_array($otherNurseryAreas)) {
            foreach ($otherNurseryAreas as $area) {
                // Insert into registration_other_nursery_areas table
                $stmt = $pdo->prepare("
                    INSERT INTO registration_other_nursery_areas (RegistrationsID, AreaID)
                    VALUES (:RegistrationsID, :AreaID)
                ");
                $stmt->execute([
                    ':RegistrationsID' => $registrationsID,
                    ':AreaID' => $area
                ]);
            }
        } else {
            error_log("Other nursery areas data is not an array.");
        }
    } else {
        // Debugging: Log missing other nursery areas
        error_log("No other nursery areas data received.");
    }

    echo json_encode(['status' => 'success', 'message' => 'Registration submitted successfully.']);

} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        echo json_encode(['status' => 'error', 'message' => 'This student is already registered for this Term']);
    } else {
        error_log("PDOException: " . $e->getMessage()); // Log the PDOException
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>