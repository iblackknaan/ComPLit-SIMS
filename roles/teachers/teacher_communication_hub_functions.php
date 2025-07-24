<?php

// Function to retrieve notifications based on recipient type
function get_notifications_by_type($recipient_type)
{
    global $pdo;

    $notifications = [];

    try {
        // Retrieve notifications based on recipient type
        $stmt = $pdo->prepare("
            SELECT n.notification_id, n.sender_type, n.message, n.created_at, n.status
            FROM notifications n
            WHERE n.recipient_type = ? AND n.sender_type = ? AND n.sender_id = ?
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$recipient_type, $_SESSION['usertype'], $_SESSION['uniqueid']]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    return $notifications;
}

// Function to send a notification
function send_notification($recipient_type, $recipient_ids, $message)
{
    global $pdo;

    $recipient_ids = explode(',', $recipient_ids);
    $sender_type = $_SESSION['usertype'];
    $sender_id = $_SESSION['uniqueid'];
    $sender_name = $_SESSION['username'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO notifications (sender_type, sender_id, sender_name, recipient_type, recipient_id, message, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'unread')
        ");

        foreach ($recipient_ids as $recipient_id) {
            $stmt->execute([$sender_type, $sender_id, $sender_name, $recipient_type, trim($recipient_id), $message]);
        }

        $pdo->commit();
        return "Notification(s) sent successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        return "Error: " . $e->getMessage();
    }
}

// Function to mark notifications as read
function mark_notifications_as_read($notification_ids)
{
    global $pdo;

    try {
        $notification_ids = implode(',', array_map('intval', $notification_ids));

        $stmt = $pdo->prepare("
            UPDATE notifications
            SET status = 'read'
            WHERE notification_id IN ($notification_ids)
        ");
        $stmt->execute();
        return "Notifications marked as read.";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Function to achieve notifications
function achieve_notifications($notification_ids)
{
    global $pdo;

    try {
        $notification_ids = implode(',', array_map('intval', $notification_ids));

        $stmt = $pdo->prepare("
            UPDATE notifications
            SET status = 'achieved'
            WHERE notification_id IN ($notification_ids)
        ");
        $stmt->execute();
        return "Notifications achieved.";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Function to unachieve notifications
function unachieve_notifications($notification_ids)
{
    global $pdo;

    try {
        $notification_ids = implode(',', array_map('intval', $notification_ids));

        $stmt = $pdo->prepare("
            UPDATE notifications
            SET status = 'unachieved'
            WHERE notification_id IN ($notification_ids)
        ");
        $stmt->execute();
        return "Notifications unachieved.";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Function to delete notifications
function delete_notifications($notification_ids)
{
    global $pdo;

    try {
        $notification_ids = implode(',', array_map('intval', $notification_ids));

        $stmt = $pdo->prepare("
            DELETE FROM notifications
            WHERE notification_id IN ($notification_ids)
        ");
        $stmt->execute();
        return "Notifications deleted.";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}
