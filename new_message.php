<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
 
// Database configuration
require_once 'connection.php';

// Fetch all chats for the current user
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT users.id, users.username, users.first_name, users.last_name 
                         FROM users 
                         INNER JOIN chats ON users.id = chats.to_user OR users.id = chats.from_user 
                         WHERE (chats.from_user = ? OR chats.to_user = ?) 
                         GROUP BY users.id, users.username, users.first_name, users.last_name");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$chatsResult = $stmt->get_result();

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Message</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>New Message</h1>

    <h2>Your Chats:</h2>
    <ul>
        <?php while ($chat = $chatsResult->fetch_assoc()) : ?>
            <li><a href="chat.php?username=<?php echo $chat['username']; ?>"><?php echo $chat['first_name'] . ' ' . $chat['last_name']; ?></a></li>
        <?php endwhile; ?>
    </ul>

    <h2>Start a New Chat:</h2>
    <form method="GET" action="chat.php">
        <input type="text" name="username" placeholder="Enter username" required>
        <input type="submit" value="Start Chat">
    </form>
</body>
</html>
