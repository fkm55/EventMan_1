<?php
// DB credentials.
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','event_management');

try {
    // Establish database connection.
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Display error message if connection fails.
    echo "Connection failed: " . $e->getMessage();
    die(); // Terminate script execution.
}
// Start the session
session_start();

// Fetch user data from the database and populate the form fields
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']); // Assuming you have a user session with user_id
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Update user profile when the form is submitted
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($first_name) || empty($email) || empty($username) || empty($password)) {
        $message = "Error: All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Error: Invalid email format.";
    } elseif (strlen($password) < 4) {
        $message = "Error: Password must be at least 6 characters.";
    } else {
        // Check if the new username or email already exists in the database
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email) AND user_id != :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $message = "Error: Username or Email already exists.";
            } else {
                // Hash the password before storing it in the database for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Proceed with updating the user profile
                $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, email = :email, username = :username, password = :password WHERE user_id = :user_id");
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();

                // Fetch updated user data
                $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $message = "Profile updated successfully.";
            }
        } catch(PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #563d7c;
            color: white;
            padding: 20px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }

        nav ul li {
            margin-right: 20px;
        }

        nav ul li.logout {
            margin-left: auto;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            padding: 20px;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #563d7c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #6f52a2;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
        }

        .success {
            background-color: #4caf50;
        }

        .error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="user.php">Home</a></li>
                <li><a href="event.php">Events</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li class="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <input id="text" type="text" name="username" placeholder="Username" value="<?php echo isset($user['username']) ? $user['username'] : ''; ?>"><br><br>
            <input id="text" type="password" name="password" placeholder="Password" value=""><br><br>
            <input id="text" type="email" name="email" placeholder="Email" value=" "><br><br>
            <input id="text" type="text" name="first_name" placeholder="First Name" value="<?php echo isset($user['first_name']) ? $user['first_name'] : ''; ?>"><br><br>

            <input type="submit" value="Save Changes">
        </form>
    </main>
</body>
</html>
