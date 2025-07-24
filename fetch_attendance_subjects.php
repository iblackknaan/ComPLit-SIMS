<?php
require_once 'config.php';

// Ensure classID is provided
if (!isset($_GET['classID']) || empty($_GET['classID'])) {
    echo json_encode(['error' => 'No class ID provided']);
    exit;
}

$classID = $_GET['classID'];

// Fetch the section ID for the given class ID
$stmt = $pdo->prepare("SELECT SectionID FROM classes WHERE ClassID = :classID");
$stmt->execute(['classID' => $classID]);
$sectionID = $stmt->fetchColumn();

if (!$sectionID) {
    echo json_encode(['error' => 'No section found for the provided class ID']);
    exit;
}

$subjectsStmt = $pdo->prepare("
    SELECT DISTINCT s.SubjectID, s.SubjectName 
    FROM subjects s 
    JOIN (
        SELECT SubjectID, SectionID 
        FROM section_subjects 
        UNION ALL 
        SELECT SubjectID, SectionID 
        FROM section_subject_options 
        WHERE IsOptional = 1 OR IsElective = 1
    ) ss ON s.SubjectID = ss.SubjectID 
    WHERE ss.SectionID = :sectionID 
    AND s.SubjectID NOT IN (10, 11)  -- Exclude Local Language and Religious Education
    AND s.SubjectID NOT IN (SELECT SubjectID FROM section_subject_options WHERE IsElective = 0) -- Exclude subjects that are not electives
");


$subjectsStmt->execute(['sectionID' => $sectionID]);

$subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($subjects)) {
    echo json_encode(['error' => 'No subjects found for the provided section ID']);
    exit;
}

echo json_encode($subjects);
?>
