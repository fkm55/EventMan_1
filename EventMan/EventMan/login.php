<?php
session_start();
include('includes/dbconnection.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        // Query to check user credentials
        $sql = "SELECT * FROM users WHERE username = :username AND status = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] == 1) {
                    header('Location: admin.php');
                } else {
                    header('Location: user.php');
                }
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "Invalid username or your account is inactive.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventify - Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .sticky {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
        }

        @media (max-width: 640px) {
            .mobile-menu-button {
                display: block;
            }

            .desktop-menu {
                display: none;
            }

            .mobile-menu {
                display: none;
                position: absolute;
                top: 100%;
                right: 0;
                background-color: #fff;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 4px;
                z-index: 1000;
            }

            .mobile-menu.active {
                display: block;
            }
        }

        @media (min-width: 641px) {
            .mobile-menu-button {
                display: none;
            }

            .desktop-menu {
                display: flex;
            }

            .mobile-menu {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo and Tabs -->
            <div class="flex items-center space-x-6">
                <h1 class="font-semibold text-xl text-gray-800"><a href="index.html">Eventify</a></h1>
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="index.html" class="text-gray-600 hover:text-gray-900">Home</a></li>
                        <li><a href="uevents.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
                        <li><a href="about.html" class="text-gray-600 hover:text-gray-900">About</a></li>
                        <!-- Add more tabs as needed -->
                    </ul>
                </nav>
            </div>
            <!-- Sign In Button and Language Selector -->
            <div class="flex items-center space-x-4">
                <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="login.html">Sign In</a></button>
                <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="signup.html">Create account</a></button>
            </div>
        </div>
    </header>

    <!-- Sign In Form -->
    <div class="flex justify-center mt-10">
        <div class="w-full max-w-md">
            <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST" action="login.php">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input id="username" name="username" type="text" placeholder="Enter your username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input id="password" name="password" type="password" placeholder="Enter your password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <?php if ($error_message) : ?>
                    <p class="text-red-500 text-xs italic"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Sign In
                    </button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
                        Forgot Password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
