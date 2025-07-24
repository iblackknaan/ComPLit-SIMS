<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Timetable Data</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Input Timetable Data</h1>
        
        <?php
        // Display alert message if set
        $alertMessage = isset($_GET['alertMessage']) ? $_GET['alertMessage'] : '';
        if (!empty($alertMessage)) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($alertMessage) . '</div>';
        }
        ?>

        <form method="post" action="GenerateTimeTableLogic.php">
            <input type="hidden" name="form_submitted" value="1">
            
            <div class="form-group">
                <label for="day">Day:</label>
                <select class="form-control" name="day" id="day" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                </select>
            </div>

            <div class="form-group">
                <label for="duration">Duration:</label>
                <select class="form-control" name="duration" id="duration" required>
                    <option value="70 minutes">70 minutes</option>
                    <option value="80 minutes">80 minutes</option>
                    <option value="50 minutes">50 minutes</option>
                </select>
            </div>

            <div class="form-group">
                <label for="time_slot_id">Time:</label>
                <select class="form-control" id="time_slot_id" name="time_slot_id" required>
                    <!-- Options will be populated dynamically using JavaScript -->
                </select>
            </div>

            <div class="form-group">
                <label for="class_id">Class:</label>
                <select class="form-control" name="class_id" id="class_id" required>
                    <?php
                    require_once 'config.php';
                    $stmt = $pdo->query('SELECT ClassID, ClassName, ClassRoomNumber FROM classes');
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"{$row['ClassID']}\">" . htmlspecialchars($row['ClassName']) . " </option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subject_id">Subject:</label>
                <select class="form-control" name="subject_id" id="subject_id" required>
                    <?php
                    $stmt = $pdo->query('SELECT SubjectID, SubjectName FROM subjects');
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"{$row['SubjectID']}\">" . htmlspecialchars($row['SubjectName']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const daySelect = document.getElementById('day');
            const durationSelect = document.getElementById('duration');
            const timeSlotSelect = document.getElementById('time_slot_id');

            function fetchTimeSlots(day, duration) {
                fetch(`fetch_time_slots.php?day=${day}&duration=${duration}`)
                    .then(response => response.json())
                    .then(data => {
                        timeSlotSelect.innerHTML = '';
                        if (data.length === 0) {
                            const option = document.createElement('option');
                            option.textContent = 'No available time slots';
                            timeSlotSelect.appendChild(option);
                        } else {
                            data.forEach(slot => {
                                const option = document.createElement('option');
                                option.value = slot.TimeSlotID;
                                option.textContent = slot.TimeRange;
                                timeSlotSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching time slots:', error);
                    });
            }

            daySelect.addEventListener('change', function () {
                fetchTimeSlots(daySelect.value, durationSelect.value);
            });

            durationSelect.addEventListener('change', function () {
                fetchTimeSlots(daySelect.value, durationSelect.value);
            });

            // Fetch time slots for the initial selected day and duration
            fetchTimeSlots(daySelect.value, durationSelect.value);
        });
    </script>
</body>
</html>
