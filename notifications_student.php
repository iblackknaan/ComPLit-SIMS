<?php
// Include the header
require_once 'header_student.php'; // Adjust the path if necessary

// Function to fetch notifications with pagination
function getNotificationsPage($limit, $offset) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE recipient_type = 'student' ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch total notifications count
function getTotalNotificationsCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM notifications WHERE recipient_type = 'student'");
    return $stmt->fetchColumn();
}

// Function to update notification status to read
function markNotificationAsRead($notification_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET status = 'read' WHERE notification_id = ?");
    $stmt->execute([$notification_id]);
}

// Check if 'action' parameter is set (to mark notification as read)
if (isset($_GET['action']) && $_GET['action'] === 'mark_as_read' && isset($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    markNotificationAsRead($notification_id);
    // Redirect back to notifications page after marking as read
    header("Location: notifications_student.php");
    exit();
}

// Constants for pagination
$perPage = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Fetch notifications for the current page
$notifications = getNotificationsPage($perPage, $offset);

// Total number of notifications
$totalNotifications = getTotalNotificationsCount();

// Calculate total pages
$totalPages = ceil($totalNotifications / $perPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Student Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <style type="text/css">
        .notifications {
            background-color: #f8f9fa; /* Light gray background for notifications container */
            padding: 20px;
            border-radius: 5px;
            margin-top: 50px; /* Added margin-top to center notifications */
        }

        .list-group-item {
            margin-bottom: 10px;
            border: 1px solid #ddd; /* Border for each notification item */
            padding: 10px;
        }

        .unread-notification {
            background-color: #ffc107; /* Yellow background for unread notifications */
            color: #212529; /* Text color for unread notifications */
        }

        .read-notification {
            background-color: #f0f0f0; /* Light gray background for read notifications */
            color: #6c757d; /* Text color for read notifications */
        }

        .expanded-message {
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 10px;
            position: relative;
        }

        .expanded-message .close-message {
            position: absolute;
            top: 5px;
            right: 5px;
            color: #aaa;
            text-decoration: none;
            font-size: 16px;
        }

        .expanded-message .message-content {
            margin-bottom: 10px;
        }

        .pagination-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center"> <!-- Center notifications horizontally -->
        <div class="col-lg-8"> <!-- Adjust the column width as per your design -->
            <div class="notifications mt-4">
                <h3>Notifications</h3>
                <?php if (count($notifications) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <li class="list-group-item <?php echo $notification['status'] === 'unread' ? 'unread-notification' : 'read-notification'; ?>">
                                <strong><?php echo ucfirst($notification['sender_type']); ?> (<?php echo htmlspecialchars($notification['sender_name']); ?>):</strong>
                                <?php 
                                    $message = htmlspecialchars($notification['message']);
                                    // Split the message into words
                                    $words = explode(' ', $message);
                                    // Take the first three words
                                    $shortMessage = implode(' ', array_slice($words, 0, 3));
                                    // Output the first three words
                                    echo $shortMessage;
                                    // If the message has more than three words, show "Read more"
                                    if (count($words) > 3) {
                                        echo ' <a href="#" class="read-more-link" data-notification-id="' . $notification['notification_id'] . '">Read more</a>';
                                    }
                                ?>
                                <small class="text-muted"><?php echo $notification['created_at']; ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No notifications found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="notifications_student.php?page=<?php echo ($page - 1); ?>" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="notifications_student.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="notifications_student.php?page=<?php echo ($page + 1); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>

        </nav>
    </div>
</div>

<script>
    function attachReadMoreEventListeners() {
        const readMoreLinks = document.querySelectorAll('.read-more-link');
        readMoreLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const notificationId = this.getAttribute('data-notification-id');
                // AJAX request to mark notification as read
                markNotificationAsRead(notificationId);
                // Update UI to mark as read
                const listItem = this.parentNode;
                listItem.classList.remove('unread-notification');
                listItem.classList.add('read-notification');
                // Remove the read more link
                listItem.removeChild(this);
                // Show full message
                const fullMessage = listItem.querySelector('.full-message');
                if (fullMessage) {
                    fullMessage.style.display = 'block';
                }
            });
        });
    }

    // Initial attachment of event listeners
    document.addEventListener('DOMContentLoaded', function() {
        attachReadMoreEventListeners();
    });

    // Function to perform AJAX request to mark notification as read
    function markNotificationAsRead(notificationId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `notifications_student.php?action=mark_as_read&notification_id=${notificationId}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('Notification marked as read');
            } else {
                console.error('Error marking notification as read');
            }
        };
        xhr.send();
    }
</script>

<!-- Include footer if necessary -->
<?php require_once 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
