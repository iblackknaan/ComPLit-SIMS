<?php
require_once 'config.php';

if (isset($_GET['day']) && isset($_GET['duration'])) {
    $day = $_GET['day'];
    $duration = $_GET['duration'];
    $stmt = $pdo->prepare('SELECT TimeSlotID, TimeRange FROM time_slots WHERE Day = ? AND Duration = ?');
    $stmt->execute([$day, $duration]);
    $timeSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($timeSlots);
}
?>
