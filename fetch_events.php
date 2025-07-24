<?php
// Include the database connection
require_once 'config.php'; // Assuming this file contains the database connection setup

// Fetch events from database
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format events for FullCalendar (assuming 'start' and 'end' are datetime fields)
$formattedEvents = [];
foreach ($events as $event) {
    $formattedEvents[] = [
        'title' => $event['event_title'],
        'start' => $event['start_date'], // Format date as needed (e.g., '2024-06-30')
        'end' => $event['end_date'],     // Optional: if event spans multiple days
        'description' => $event['description'],
        'location' => $event['location']
    ];
}

// Output events as JSON
header('Content-Type: application/json');
echo json_encode($formattedEvents);
?>
