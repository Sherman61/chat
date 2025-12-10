<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: new_message.php");
    exit;
}
 
 require_once 'connection.php';

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: chat.php");
            exit;
        }
    }

    $error = "Invalid username or password.";
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
	<style type="text/css">

#button{   
    //color: white;
    background-color: Lightblue;
}
</style>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Login</h1>

    <?php if (isset($error)) : ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" class="labell" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" class="labell" name="password" id="password" required>
        <br>
        <input type="submit" value="Login">
        <a href="index.php" class="btn btn-light ">Click to Signup</a><br><br>
    </form>
</body>
</html>
