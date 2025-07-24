<?php
// Include the database configuration file
require_once 'config.php';

// Fetch timetable data from the database
$query = 'SELECT t.day, ts.TimeRange as time, ts.Duration as duration, c.ClassName as class, s.SubjectName as subject
          FROM timetable t
          JOIN time_slots ts ON t.time_slot_id = ts.TimeSlotID
          JOIN classes c ON t.class_id = c.ClassID
          JOIN subjects s ON t.subject_id = s.SubjectID
          ORDER BY FIELD(t.day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"), 
                   STR_TO_DATE(SUBSTRING_INDEX(ts.TimeRange, "-", 1), "%H:%i")';
$stmt = $pdo->query($query);
$timetables = $stmt->fetchAll();

// Group timetables by day
$timetable_by_day = [];
foreach ($timetables as $timetable) {
    $timetable_by_day[$timetable['day']][] = $timetable;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Timetable</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .day-section {
            margin-bottom: 2rem;
        }
        .day-title {
            text-align: center;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">View Timetable</h1>
        <?php if (!empty($timetable_by_day)): ?>
            <?php foreach ($timetable_by_day as $day => $timetables): ?>
                <div class="day-section">
                    <h2 class="day-title"><?php echo htmlspecialchars($day); ?></h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timetables as $timetable): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($timetable['time']); ?></td>
                                    <td><?php echo htmlspecialchars($timetable['class']); ?></td>
                                    <td><?php echo htmlspecialchars($timetable['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($timetable['duration']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No timetable data available.</p>
        <?php endif; ?>

        <!-- Back button to return to TimeTableZone.php with action -->
        <a href="TimeTableZone.php" class="btn btn-primary mt-4">Back to Time Table Zone</a>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
