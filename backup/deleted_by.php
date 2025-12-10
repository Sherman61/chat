<?php
session_start();

// Database configuration
require_once 'connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the message id from the form data
    $messageId = $_POST['id'];

    // Get the current user's id
    $userId = $_SESSION['user_id'];

    // Check if the current user is the sender or the receiver of the message
    $stmt = $mysqli->prepare("SELECT from_user, to_user FROM chats WHERE id = ?");
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $chat = $result->fetch_assoc();

    if ($chat['from_user'] == $userId) {
        // The current user is the sender of the message
        // Delete the message from the database
        $stmt = $mysqli->prepare("DELETE FROM chats WHERE id = ?");
    } else {
        // The current user is the receiver of the message
        // Mark the message as deleted by the receiver
        $stmt = $mysqli->prepare("UPDATE chats SET deleted_by_receiver = TRUE WHERE id = ?");
    }
 
    $stmt->bind_param("i", $messageId);
    $stmt->execute();

    // Close the database connection
    $mysqli->close();
}
?>
