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
<html lang="en">
<head>
    <title>Messages - Chat App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 32px;
            font-weight: 600;
        }
        
        .header p {
            margin: 0;
            color: #666;
            font-size: 16px;
        }
        
        .logout-btn {
            float: right;
            background: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #d32f2f;
        }
        
        .new-chat-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .new-chat-section h2 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 22px;
            font-weight: 600;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
        }
        
        .search-form input[type="text"] {
            flex: 1;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .search-form input[type="submit"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .search-form input[type="submit"]:hover {
            transform: translateY(-2px);
        }
        
        .chats-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .chats-section h2 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 22px;
            font-weight: 600;
        }
        
        .chat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .chat-list li {
            margin-bottom: 10px;
        }
        
        .chat-list a {
            display: block;
            padding: 18px 20px;
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-size: 18px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .chat-list a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            transform: translateX(5px);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state p {
            font-size: 18px;
            margin: 0;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-form input[type="submit"] {
                width: 100%;
            }
            
            .logout-btn {
                float: none;
                display: block;
                width: 100%;
                margin-top: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="logout.php" class="logout-btn">Logout</a>
            <h1>💬 Messages</h1>
            <p>Start a conversation or continue chatting</p>
        </div>
        
        <div class="new-chat-section">
            <h2>🔍 Start a New Chat</h2>
            <form method="GET" action="chat.php" class="search-form">
                <input type="text" name="username" placeholder="Enter username to chat with..." required>
                <input type="submit" value="Start Chat">
            </form>
        </div>
        
        <div class="chats-section">
            <h2>💬 Your Conversations</h2>
            <?php if ($chatsResult->num_rows > 0): ?>
                <ul class="chat-list">
                    <?php while ($chat = $chatsResult->fetch_assoc()): ?>
                        <li>
                            <a href="chat.php?username=<?php echo htmlspecialchars($chat['username']); ?>">
                                <?php echo htmlspecialchars($chat['first_name'] . ' ' . $chat['last_name']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p>No conversations yet. Start a new chat above!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
