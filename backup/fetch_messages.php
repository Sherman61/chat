<?php
session_start();

// Database configuration
require_once 'connection.php';

// Retrieve the username from the URL parameter
if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // Fetch user information of the user you are chatting with
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $chatUserResult = $stmt->get_result();
    $chatUser = $chatUserResult->fetch_assoc();

    // Fetch chat history between the two users
    $stmt = $mysqli->prepare("SELECT * FROM chats WHERE ((from_user = ? AND to_user = ? AND deleted_by_sender = FALSE) OR (from_user = ? AND to_user = ? AND deleted_by_receiver = FALSE)) AND id > ? ORDER BY time_sent ASC");
$stmt->bind_param("iiiii", $user_id, $chatUser['id'], $chatUser['id'], $user_id, $lastMessageId);
$stmt->execute();
$chatsResult = $stmt->get_result();
 

    // Fetch only the new messages since the last fetch
    $lastMessageId = $_GET['lastMessageId'];
    $newChatsResult = [];
    while ($chat = $chatsResult->fetch_assoc()) {
        if ($chat['id'] > $lastMessageId) {
            $newChatsResult[] = $chat;
        }
    }

    echo json_encode($newChatsResult);
} else {
    header("HTTP/1.1 400 Bad Request");
}
$mysqli->close();
?>
