<?php
// Include the database connection
require_once 'config.php';

// Fetch classes from the database
$classes = [];
$stmt = $pdo->query('SELECT ClassID, ClassName FROM classes');
while ($row = $stmt->fetch()) {
    $classes[] = $row;
}

// Fetch subjects from the database
$subjects = [];
$stmt = $pdo->query('SELECT subjectID, subjectName FROM subjects');
while ($row = $stmt->fetch()) {
    $subjects[] = $row;
}

// Prepare data to return as JSON
$data = [
    'classes' => $classes,
    'subjects' => $subjects
];

// Output as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
