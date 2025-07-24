<?php
// Include the database configuration file
require_once 'config.php';

// Include the header file
require_once 'header_teacher.php';

// Include the functions file
require_once 'teacher_communication_hub_functions.php';

// Initialize variables
$recipient_type = '';
$recipient_ids = '';
$message = '';
$success_message = '';
$notifications = [];
$unread_notifications = 0;
$achieved_notifications = 0;
$total_notifications = 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['send_notification'])) {
        $recipient_type = $_POST['recipient_type'];
        $recipient_ids = $_POST['recipient_ids'];
        $message = $_POST['message'];
        $success_message = send_notification($recipient_type, $recipient_ids, $message);
    } elseif (isset($_POST['show_notifications'])) {
        $recipient_type = $_POST['recipient_type'];
        $notifications = get_notifications_by_type($recipient_type);

        // Debugging output
        echo '<pre>';
        print_r($notifications);
        echo '</pre>';

        // Count notifications based on status
        $unread_notifications = count(array_filter($notifications, function($n) { return $n['status'] === 'unread'; }));
        $achieved_notifications = count(array_filter($notifications, function($n) { return $n['status'] === 'achieved'; }));
        $total_notifications = count($notifications);
    } elseif (isset($_POST['mark_as_read'])) {
        if (isset($_POST['marked_notifications'])) {
            $notification_ids = $_POST['marked_notifications'];
            mark_notifications_as_read($notification_ids);
        }
    } elseif (isset($_POST['achieve'])) {
        if (isset($_POST['marked_notifications'])) {
            $notification_ids = $_POST['marked_notifications'];
            achieve_notifications($notification_ids);
        }
    } elseif (isset($_POST['unachieve'])) {
        if (isset($_POST['marked_notifications'])) {
            $notification_ids = $_POST['marked_notifications'];
            unachieve_notifications($notification_ids);
        }
    } elseif (isset($_POST['delete_notification'])) {
        if (isset($_POST['marked_notifications'])) {
            $notification_ids = $_POST['marked_notifications'];
            delete_notifications($notification_ids);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send and View Notifications</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .notification-list {
            /* Additional styles here */
        }
        .notification-list-buttons {
            max-height: 350px; /* Adjust this height as needed */
            overflow-y: scroll;
        }
        /* CSS for different notification statuses */
        .read {
            color: green; /* Change text color for read notifications */
        }
        .unread {
            color: red; /* Change text color for unread notifications */
        }
        .achieved {
            color: blue; /* Change text color for achieved notifications */
        }
        /* Adjust button colors */
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-warning {
            color: #212529;
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .btn-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Send Notification</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="recipient_type">Recipient Type:</label>
                            <select name="recipient_type" id="recipient_type" class="form-control">
                                <option value="student" <?php if ($recipient_type == 'student') echo 'selected'; ?>>Student</option>
                                <option value="teacher" <?php if ($recipient_type == 'teacher') echo 'selected'; ?>>Teacher</option>
                                <option value="parent" <?php if ($recipient_type == 'parent') echo 'selected'; ?>>Parent</option>
                                <option value="admin" <?php if ($recipient_type == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="recipient_ids">Recipient IDs (comma-separated):</label>
                            <input type="text" name="recipient_ids" id="recipient_ids" class="form-control" value="<?php echo htmlspecialchars($recipient_ids); ?>">
                        </div>

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea name="message" id="message" rows="12" class="form-control"><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" name="send_notification" class="btn btn-info w-auto">Send Notification</button>
                        </div>
                    </form>

                    <?php if($success_message): ?>
                        <div class="alert alert-success mt-3"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>View Notifications</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="recipient_type">Recipient Type:</label>
                            <select name="recipient_type" id="recipient_type" class="form-control">
                                <option value="student" <?php if ($recipient_type == 'student') echo 'selected'; ?>>Student</option>
                                <option value="teacher" <?php if ($recipient_type == 'teacher') echo 'selected'; ?>>Teacher</option>
                                <option value="parent" <?php if ($recipient_type == 'parent') echo 'selected'; ?>>Parent</option>
                                <option value="admin" <?php if ($recipient_type == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" name="show_notifications" class="btn btn-info w-auto">Show Notifications</button>
                        </div>
                    </form>

                    <h4 class="mt-4 text-center">Notifications List</h4>
                    <div class="notification-list">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="notification-list-buttons">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <input type="checkbox" id="select_all">
                                        <label for="select_all"><strong>Select All</strong></label>
                                    </li>
                                    <?php if (!empty($notifications)): ?>
                                        <?php foreach ($notifications as $notification): ?>
                                            <li class="list-group-item <?php echo htmlspecialchars($notification['status']); ?>">
                                                <input type="checkbox" name="marked_notifications[]" value="<?php echo htmlspecialchars($notification['notification_id']); ?>">
                                                <strong>Sender:</strong> <?php echo isset($notification['sender_type']) ? ucfirst(htmlspecialchars($notification['sender_type'])) : 'Unknown'; ?>,
                                                <strong>Message:</strong> <?php echo htmlspecialchars($notification['message']); ?> -
                                                <small><?php echo htmlspecialchars($notification['created_at']); ?></small>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item">No notifications found.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">
                                <button type="submit" name="mark_as_read" class="btn btn-success">Mark as Read</button>
                                <button type="submit" name="achieve" class="btn btn-primary ml-2">Achieve</button>
                                <button type="submit" name="unachieve" class="btn btn-warning ml-2">Unachieve</button>
                                <button type="submit" name="delete_notification" class="btn btn-danger ml-2">Delete</button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-3">
                        <strong>Total Notifications:</strong> <?php echo $total_notifications; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the footer file -->
<?php require_once 'footer.php'; ?>

<script>
    document.getElementById('select_all').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('input[name="marked_notifications[]"]');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });
</script>
</body>
</html>

