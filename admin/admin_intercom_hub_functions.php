<?php

// Include the database configuration file
require_once 'config.php';

// Initialize variables
$messages = [];
$recipient_type = isset($_POST['recipient_type']) ? $_POST['recipient_type'] : 'individual'; // Default to 'individual'
$success_message = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle sending messages
    if (isset($_POST['send_message'])) {
        $recipient_type = $_POST['recipient_type'];
        $message = $_POST['message'];

        // Check if session variable is set
        if (!isset($_SESSION['uniqueid'])) {
            error_log("Session uniqueid is not set.");
            $success_message = 'Failed to send message(s).';
            // Optionally, redirect or handle the error
            header("Location: admin_intercom_hub.php");
            exit();
        }

        try {
            if ($recipient_type == 'individual') {
                $recipient_ids = $_POST['recipient_ids'];
                foreach ($recipient_ids as $recipient_id) {
                    $stmt = $pdo->prepare("INSERT INTO messages (recipient_id, recipient_type, sender_type, sender_id, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'unread', NOW())");
                    $stmt->execute([$recipient_id, $recipient_type, $_SESSION['usertype'], $_SESSION['uniqueid'], $message]);
                }
            } elseif ($recipient_type == 'class') {
                $class_id = $_POST['class_id'];
                $stmt = $pdo->prepare("SELECT StudentID FROM students WHERE CurrentClassID = ?");
                $stmt->execute([$class_id]);
                $students = $stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($students as $student_id) {
                    $stmt = $pdo->prepare("INSERT INTO messages (recipient_id, recipient_type, sender_type, sender_id, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'unread', NOW())");
                    $stmt->execute([$student_id, $recipient_type, $_SESSION['usertype'], $_SESSION['uniqueid'], $message]);
                }
            } elseif ($recipient_type == 'school') {
                $stmt = $pdo->query("SELECT StudentID FROM students");
                $students = $stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($students as $student_id) {
                    $stmt = $pdo->prepare("INSERT INTO messages (recipient_id, recipient_type, sender_type, sender_id, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'unread', NOW())");
                    $stmt->execute([$student_id, $recipient_type, $_SESSION['usertype'], $_SESSION['uniqueid'], $message]);
                }
            }

            $success_message = 'Message(s) sent successfully!';
        } catch (Exception $e) {
            // Log the error message
            error_log("Error sending message: " . $e->getMessage());
            $success_message = 'Failed to send message(s).';
        }
    }

    // Handle showing messages
    if (isset($_POST['show_messages'])) {
        $recipient_type = $_POST['recipient_type'];
        $messages = get_messages_by_type($recipient_type);
    }

    // Handle other actions (e.g., mark as read, achieve, unachieve, delete)
    if (isset($_POST['mark_as_read']) || isset($_POST['achieve']) || isset($_POST['unachieve']) || isset($_POST['delete_message'])) {
        if (isset($_POST['marked_messages'])) {
            // Check if session variable is set
            if (!isset($_SESSION['uniqueid'])) {
                error_log("Session uniqueid is not set.");
                // Optionally, redirect or handle the error
                header("Location: admin_intercom_hub.php");
                exit();
            }

            try {
                foreach ($_POST['marked_messages'] as $message_id) {
                    $status = '';
                    if (isset($_POST['mark_as_read'])) $status = 'read';
                    if (isset($_POST['achieve'])) $status = 'achieved';
                    if (isset($_POST['unachieve'])) $status = 'unread';
                    if (isset($_POST['delete_message'])) $status = 'deleted';

                    if ($status !== 'deleted') {
                        $stmt = $pdo->prepare("UPDATE messages SET status = ? WHERE message_id = ? AND recipient_id = ?");
                        $stmt->execute([$status, $message_id, $_SESSION['uniqueid']]);
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM messages WHERE message_id = ? AND recipient_id = ?");
                        $stmt->execute([$message_id, $_SESSION['uniqueid']]);
                    }
                }
                header("Location: admin_intercom_hub.php");
                exit();
            } catch (Exception $e) {
                // Log the error message
                error_log("Error updating message status: " . $e->getMessage());
            }
        }
    }
}

// Fetch messages and counts for displaying
$messages = get_messages_by_type($recipient_type);
$unread_messages = count_messages_by_status('unread');
$achieved_messages = count_messages_by_status('achieved');
$total_messages = count_total_messages();

// Function to get messages by recipient type
function get_messages_by_type($recipient_type)
{
    global $pdo;

    // Check if session variable is set
    if (!isset($_SESSION['uniqueid'])) {
        error_log("Session uniqueid is not set.");
        return [];
    }

    $messages = [];

    try {
        $stmt = $pdo->prepare("
            SELECT message_id, sender_type, message, created_at, status
            FROM messages
            WHERE recipient_type = ? AND recipient_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$recipient_type, $_SESSION['uniqueid']]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Log the error message
        error_log("Error fetching messages: " . $e->getMessage());
    }

    return $messages;
}

// Function to count messages by status
function count_messages_by_status($status)
{
    global $pdo;

    // Check if session variable is set
    if (!isset($_SESSION['uniqueid'])) {
        error_log("Session uniqueid is not set.");
        return 0;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS count
            FROM messages
            WHERE recipient_id = ? AND status = ?
        ");
        $stmt->execute([$_SESSION['uniqueid'], $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (Exception $e) {
        // Log the error message
        error_log("Error counting messages by status: " . $e->getMessage());
        return 0;
    }
}

// Function to count total messages
function count_total_messages()
{
    global $pdo;

    // Check if session variable is set
    if (!isset($_SESSION['uniqueid'])) {
        error_log("Session uniqueid is not set.");
        return 0;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS count
            FROM messages
            WHERE recipient_id = ?
        ");
        $stmt->execute([$_SESSION['uniqueid']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (Exception $e) {
        // Log the error message
        error_log("Error counting total messages: " . $e->getMessage());
        return 0;
    }
}

// Function to get all classes
function get_all_classes()
{
    global $pdo;

    try {
        $stmt = $pdo->query("SELECT ClassID, ClassName FROM classes ORDER BY ClassID ASC, ClassName ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Log the error message
        error_log("Error fetching classes: " . $e->getMessage());
        return [];
    }
}

// Function to get students by class ID
function get_students_by_class($class_id) {
    global $pdo;

    // Check if session variable is set
    if (!isset($_SESSION['uniqueid'])) {
        error_log("Session uniqueid is not set.");
        return [];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT StudentID, FirstName, LastName
            FROM students
            WHERE CurrentClassID = ?
        ");
        $stmt->execute([$class_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Log the error message
        error_log("Error fetching students: " . $e->getMessage());
        return [];
    }
}
?>
