<?php
// Database configuration
require_once 'connection.php';

$letter = $_GET['letter'] . '%';

$stmt = $mysqli->prepare("SELECT id, username FROM users WHERE username LIKE ? LIMIT 10");
$stmt->bind_param("s", $letter);
$stmt->execute();
$usernamesResult = $stmt->get_result();

$usernames = [];
while ($row = $usernamesResult->fetch_assoc()) {
    $usernames[] = $row;
}

echo json_encode($usernames);
$mysqli->close();
?>
