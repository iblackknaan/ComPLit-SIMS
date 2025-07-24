<?php
// Start the session
session_start();

// Include the database configuration file
require_once 'config.php';

// Retrieve the selected stakeholder's ID and message from the form submission
$stakeholder_id = $_POST['stakeholder_id'] ?? null;
$message = $_POST['message'] ?? null;

// Get the sender ID from the session
$sender_id = $_SESSION['userID'] ?? null;

// Validate inputs
if (!$stakeholder_id || !$message || !$sender_id) {
    $_SESSION['message'] = 'Required parameters are missing.';
    header('Location: admin_intercom_hub.php');
    exit;
}

// Initialize default values for table and id_column
$table = 'unknown';
$id_column = 'unknown_id';
$recipient_type = 'unknown';

// Determine the stakeholder type (Student, Teacher, or Parent) based on the ID
if ($stakeholder_id >= 1 && $stakeholder_id <= 100) {
    $recipient_type = 'student';
    $table = 'students';
    $id_column = 'StudentID';
} elseif ($stakeholder_id >= 101 && $stakeholder_id <= 200) {
    $recipient_type = 'teacher';
    $table = 'teachers';
    $id_column = 'TeacherID';
} elseif ($stakeholder_id >= 201 && $stakeholder_id <= 300) {
    $recipient_type = 'parent';
    $table = 'parents';
    $id_column = 'ParentID';
} else {
    $_SESSION['message'] = 'Invalid stakeholder ID';
    header('Location: admin_intercom_hub.php');
    exit;
}

try {
    // Prepare and execute query to fetch recipient's name
    $stmt = $pdo->prepare("SELECT CONCAT(FirstName, ' ', LastName) AS FullName FROM $table WHERE $id_column = :stakeholder_id");
    $stmt->execute(['stakeholder_id' => $stakeholder_id]);
    $recipient_name = $stmt->fetchColumn();

    if (!$recipient_name) {
        $_SESSION['message'] = 'Recipient not found.';
        header('Location: admin_intercom_hub.php');
        exit;
    }

    // Prepare and execute query to insert message
    $stmt = $pdo->prepare("INSERT INTO messages (sender_type, sender_id, sender_name, recipient_type, recipient_id, message) 
        VALUES (:sender_type, :sender_id, 'Admin', :recipient_type, :recipient_id, :message)");
    $stmt->execute([
        'sender_type' => 'admin',
        'sender_id' => $sender_id,
        'recipient_type' => $recipient_type,
        'recipient_id' => $stakeholder_id,
        'message' => $message
    ]);

    $_SESSION['message'] = 'Message sent successfully.';
    header('Location: admin_intercom_hub.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['message'] = 'Database error: ' . $e->getMessage();
    header('Location: admin_intercom_hub.php');
    exit;
}
?>
