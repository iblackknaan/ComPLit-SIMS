<?php
require_once 'config.php';

header('Content-Type: application/json');

if (isset($_GET['UniqueID']) && !empty($_GET['UniqueID'])) {
    // Handle UniqueID request
    $uniqueID = $_GET['UniqueID'];

    function generateSubjectHtml($subjects, $inputType = 'checkbox', $name = 'subjects[]', $checkedSubjects = []) {
        $html = '';
        foreach ($subjects as $subject) {
            $isChecked = in_array($subject['SubjectID'], $checkedSubjects) ? 'checked' : '';
            $html .= '<div><input type="' . htmlspecialchars($inputType) . '" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars(json_encode($subject)) . '" ' . $isChecked . '> ' . htmlspecialchars($subject['SubjectName']) . '</div>';
        }
        return $html;
    }

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch student details including the class name and section
        $stmt = $pdo->prepare("
            SELECT s.StudentID, CONCAT(s.FirstName, ' ', s.LastName) AS StudentName, s.CurrentClassID, c.ClassName, sec.SectionName
            FROM students s
            JOIN classes c ON s.CurrentClassID = c.ClassID
            JOIN sections sec ON c.SectionID = sec.SectionID
            WHERE s.UniqueID = :UniqueID
        ");
        $stmt->execute(['UniqueID' => $uniqueID]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            echo json_encode(['error' => 'Student not found.']);
            exit;
        }

        // Determine mandatory subjects based on the student's section
        $checkedSubjects = [];
        $mandatorySubjectsQuery = '';
        if ($student['SectionName'] == 'Nursery Section') {
            $mandatorySubjectsQuery = "
                SELECT SubjectID, SubjectName
                FROM subjects
                WHERE SubjectName IN ('Social Development', 'Health Habits', 'Numeric Concepts', 'Language Development', 'Environmental Awareness')
            ";
            $checkedSubjects = [1, 2, 3, 4, 5]; // IDs for Nursery Section subjects
        } elseif ($student['SectionName'] == 'Lower Primary Section') {
            $mandatorySubjectsQuery = "
                SELECT SubjectID, SubjectName
                FROM subjects
                WHERE SubjectName IN ('Mathematics', 'English', 'Literacy - One', 'Literacy - Two')
            ";
            $checkedSubjects = [8, 9, 6, 7]; // IDs for Lower Primary Section subjects
        } elseif ($student['SectionName'] == 'Upper Primary Section') {
            $mandatorySubjectsQuery = "
                SELECT SubjectID, SubjectName
                FROM subjects
                WHERE SubjectName IN ('Mathematics', 'English', 'Science', 'Social Studies')
            ";
            $checkedSubjects = [8, 9, 15, 16]; // IDs for Upper Primary Section subjects
        } else {
            $mandatorySubjectsQuery = "
                SELECT s.SubjectID, s.SubjectName 
                FROM subjects s 
                JOIN subject_category_mapping scm ON s.SubjectID = scm.SubjectID 
                JOIN subject_categories sc ON scm.CategoryID = sc.CategoryID 
                WHERE sc.CategoryName = 'Mandatory'
            ";
        }

        // Fetch mandatory subjects
        $stmt = $pdo->prepare($mandatorySubjectsQuery);
        $stmt->execute();
        $mandatorySubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Determine elective subjects based on the student's section
        $electiveCheckedSubjects = [];
        $electiveSubjectsHtml = '';
        if ($student['SectionName'] == 'Nursery Section') {
            // No elective subjects for Nursery students
        } elseif ($student['SectionName'] == 'Lower Primary Section') {
            // Fetch Religious Education electives
            $stmt = $pdo->prepare("
                SELECT SubjectID, SubjectName 
                FROM subjects 
                WHERE SubjectName IN ('Christian Education', 'Islamic Education')
            ");
            $stmt->execute();
            $religiousEducationSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch Local Language electives
            $stmt = $pdo->prepare("
                SELECT SubjectID, SubjectName 
                FROM subjects 
                WHERE SubjectName IN ('Swahili', 'Luganda')
            ");
            $stmt->execute();
            $localLanguageSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add IDs for default checked subjects
            $electiveCheckedSubjects = [20]; // Assuming ID for 'Christian Education' is 20
            $electiveSubjectsHtml .= '<div><strong>Religious Education</strong></div>';
                        $electiveSubjectsHtml .= generateSubjectHtml($religiousEducationSubjects, 'radio', 'religious_education', $electiveCheckedSubjects);

            $electiveCheckedSubjects = [18]; // Assuming ID for 'Luganda' is 18
            $electiveSubjectsHtml .= '<div><strong>Local Language</strong></div>';
            $electiveSubjectsHtml .= generateSubjectHtml($localLanguageSubjects, 'radio', 'local_language', $electiveCheckedSubjects);
        } elseif ($student['SectionName'] == 'Upper Primary Section') {
            $stmt = $pdo->prepare("
                SELECT SubjectID, SubjectName 
                FROM subjects 
                WHERE SubjectName IN ('Christian Education', 'Islamic Education')
            ");
            $stmt->execute();
            $electiveSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add IDs for default checked subjects
            $electiveCheckedSubjects = [20]; // Assuming ID for 'Christian Education' is 20
            $electiveSubjectsHtml .= generateSubjectHtml($electiveSubjects, 'radio', 'elective_subject', $electiveCheckedSubjects);
        }

        // Fetch optional subjects
        $optionalSubjectsHtml = '';
        if ($student['SectionName'] !== 'Nursery Section') {
            $stmt = $pdo->prepare("
                SELECT s.SubjectID, s.SubjectName 
                FROM subjects s 
                JOIN subject_category_mapping scm ON s.SubjectID = scm.SubjectID 
                JOIN subject_categories sc ON scm.CategoryID = sc.CategoryID 
                WHERE sc.CategoryName = 'Optional'
            ");
            $stmt->execute();
            $optionalSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $optionalSubjectsHtml = generateSubjectHtml($optionalSubjects, 'checkbox', 'optional_subjects[]');
        }

        // Fetch other nursery areas of concern (displayed in two columns)
        $otherNurseryAreasHtml = '';
        if ($student['SectionName'] == 'Nursery Section') {
            $stmt = $pdo->prepare("
                SELECT ID, Name 
                FROM other_nursery_areas_of_concern
            ");
            $stmt->execute();
            $otherNurseryAreas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Split data into two columns
            $halfCount = ceil(count($otherNurseryAreas) / 2);
            $firstColumn = array_slice($otherNurseryAreas, 0, $halfCount);
            $secondColumn = array_slice($otherNurseryAreas, $halfCount);

            $otherNurseryAreasHtml .= '<div class="other-nursery-areas-container">';
            $otherNurseryAreasHtml .= '<div class="other-nursery-area-column">';
            foreach ($firstColumn as $area) {
                $isChecked = ($area['Name'] !== 'Swimming') ? 'checked' : '';
                $otherNurseryAreasHtml .= '<div class="other-nursery-area">';
                $otherNurseryAreasHtml .= '<input type="checkbox" id="area_' . $area['ID'] . '" name="other_nursery_areas[]" value="' . $area['ID'] . '" ' . $isChecked . '>';
                $otherNurseryAreasHtml .= '<label for="area_' . $area['ID'] . '">' . htmlspecialchars($area['Name']) . '</label>';
                $otherNurseryAreasHtml .= '</div>';
            }
            $otherNurseryAreasHtml .= '</div>'; // Close first column

            $otherNurseryAreasHtml .= '<div class="other-nursery-area-column">';
            foreach ($secondColumn as $area) {
                $isChecked = ($area['Name'] !== 'Swimming') ? 'checked' : '';
                $otherNurseryAreasHtml .= '<div class="other-nursery-area">';
                $otherNurseryAreasHtml .= '<input type="checkbox" id="area_' . $area['ID'] . '" name="other_nursery_areas[]" value="' . $area['ID'] . '" ' . $isChecked . '>';
                $otherNurseryAreasHtml .= '<label for="area_' . $area['ID'] . '">' . htmlspecialchars($area['Name']) . '</label>';
                $otherNurseryAreasHtml .= '</div>';
            }
            $otherNurseryAreasHtml .= '</div>'; // Close second column
            $otherNurseryAreasHtml .= '</div>'; // Close container
        }

        // Prepare response data
        $responseData = [
            'StudentID' => $student['StudentID'] ?? '',
            'StudentName' => $student['StudentName'] ?? '',
            'CurrentClass' => $student['ClassName'] ?? '',
            'CurrentClassID' => $student['CurrentClassID'] ?? '',
                        'SectionName' => $student['SectionName'] ?? '',
            'mandatorySubjects' => '<strong>Mandatory Subjects:</strong><br>' . generateSubjectHtml($mandatorySubjects, 'checkbox', 'subjects[]', $checkedSubjects),
            'electiveSubjects' => ($student['SectionName'] !== 'Nursery Section') ? '<strong>Elective Subjects:</strong><br>' . $electiveSubjectsHtml : '',
            'optionalSubjects' => ($student['SectionName'] !== 'Nursery Section') ? '<strong>Optional Subjects:</strong><br>' . $optionalSubjectsHtml : '',
            'otherNurseryAreas' => ($student['SectionName'] === 'Nursery Section') ? '<strong>Other Areas of Concern:</strong><br>' . $otherNurseryAreasHtml : ''
        ];

        echo json_encode($responseData);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} elseif (isset($_POST['section']) && !empty($_POST['section'])) {
    // Handle section request
    $newSection = $_POST['section'];
    // Fetch relevant form data for the new section
    $formData = fetchFormData($newSection);
    echo json_encode($formData);
    exit;
} else {
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}
?>