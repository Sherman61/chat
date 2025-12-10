<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database configuration
require_once 'connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the message ID from the POST data
    $messageId = $_POST['id'];

    // Retrieve the user ID from the session
    $userId = $_SESSION['user_id'];

    // Delete the message from the chats table
    $stmt = $mysqli->prepare("DELETE FROM chats WHERE id = ? AND (from_user = ? OR to_user = ?)");
    $stmt->bind_param("iii", $messageId, $userId, $userId);
    $stmt->execute();

    // Check if the message was deleted successfully
    if ($stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $mysqli->close();
}
?>