<?php
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['notification_id'])) {
    $notificationId = intval($_POST['notification_id']);
    
    // Mark the notification as read in the database
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    
    if ($stmt->execute()) {
        echo "Notification marked as read.";
    } else {
        echo "Error marking notification as read.";
    }
    $stmt->close();
}
?>