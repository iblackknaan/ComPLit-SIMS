<?php
require_once 'config.php';

if (isset($_POST['section'])) {
    $section = $_POST['section'];

    // Fetch mandatory subjects based on the section
    $stmt = $pdo->prepare("
        SELECT s.SubjectID, s.SubjectName 
        FROM subjects s 
        JOIN subject_category_mapping scm ON s.SubjectID = scm.SubjectID 
        JOIN subject_categories sc ON scm.CategoryID = sc.CategoryID 
        WHERE sc.CategoryName = 'Mandatory' AND s.Section = ?
    ");
    $stmt->execute([$section]);
    $mandatorySubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch elective subjects based on the section
    $stmt = $pdo->prepare("
        SELECT s.SubjectID, s.SubjectName 
        FROM subjects s 
        JOIN subject_category_mapping scm ON s.SubjectID = scm.SubjectID 
        JOIN subject_categories sc ON scm.CategoryID = sc.CategoryID 
        WHERE sc.CategoryName = 'Elective' AND s.Section = ?
    ");
    $stmt->execute([$section]);
    $electiveSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch optional subjects based on the section
    $stmt = $pdo->prepare("
        SELECT s.SubjectID, s.SubjectName 
        FROM subjects s 
        JOIN subject_category_mapping scm ON s.SubjectID = scm.SubjectID 
        JOIN subject_categories sc ON scm.CategoryID = sc.CategoryID 
        WHERE sc.CategoryName = 'Optional' AND s.Section = ?
    ");
    $stmt->execute([$section]);
    $optionalSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data in JSON format
    echo json_encode([
        'mandatorySubjects' => $mandatorySubjects,
        'electiveSubjects' => $electiveSubjects,
        'optionalSubjects' => $optionalSubjects,
    ]);
}

function generateSubjectHtml($subjects, $inputType, $name) {
    $html = '';
    foreach ($subjects as $subject) {
        $html .= '<div><input type="' . $inputType . '" name="' . $name . '" value="' . $subject['SubjectID'] . '"> ' . $subject['SubjectName'] . '</div>';
    }
    return $html;
}
?>