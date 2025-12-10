
<?PHP
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: new_message.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($first_name) && !empty($last_name) && !empty($username) && !empty($password) && !is_numeric($username) && isset($_POST['agree'])) {
        // Check if the username already exists
        $stmt = $mysqli->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();  // Store the result for getting the number of rows
        $num_rows = $stmt->num_rows;
        $stmt->close();

        if ($num_rows > 0) {
            // Username already exists
            $error = "Error: This username is already registered. Please try again with a different username.";
        } else {
            // Save to the database
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $first_name, $last_name, $username, $password);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: Failed to register. Please try again.";
            }

            $stmt->close();
        }
    } else {
        $error = "Please enter valid information in all fields and agree to the terms and conditions.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <style type="text/css">
        #button {
            padding: 10px;
            color: white;
            background-color: Lightblue;
            border: none;
        }

        ::placeholder {
            color: #333;
            opacity: 1;
        }

        .agree-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .agree-label {
            font-size: 16px;
            color: #333;
            display: inline-block;
            margin-top: 5px;
        }

        .agree-link {
            color: blue;
            text-decoration: underline;
        }
    </style>
        
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<body>
<div class="full-screen-container">
    <div class="login-container">
        <h1 class="login-title">Sign Up</h1>

        <form class="form" method="post">
            <div class="input-group">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required>
            </div>
            <div class="input-group">
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required>
            </div>

            <div class="input-group success">
            <label for="username">user Name:</label>
                <input id="username" name="username" require placeholder="Enter Username">
            </div>
            <div class="input-group error">
            <label for="first_name">Password:</label>
                <input id="password" type="password" name="password" pattern=".{8,}" title="8 characters minimum"
                       required placeholder="Enter Password">
            </div>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="input-group">
                <label for="agree" class="agree-label">I agree to the <a href="#" class="agree-link">terms and
                        conditions</a>.</label>
                <input type="checkbox" name="agree" id="agree" class="agree-checkbox" required>
            </div>
            <button id="button" type="submit" value="Signup" name="submit" class="login-button">Submit</button>
            <a href="login.php" class="btn btn-light">Click to Login</a><br><br>
        </form>
    </div>
</div>
</body>
</html>
