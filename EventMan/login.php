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
  <title>Eventify</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Style the sticky navbar */
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
<body class="font-sans bg-gray-100">

<!-- Navbar -->
<header class="bg-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    <!-- Logo and Tabs -->
    <div class="flex items-center space-x-6">
      <h1 class="font-semibold text-2xl text-gray-800"><a href="index.php">Eventify</a></h1>
      <nav class="desktop-menu">
        <ul class="flex space-x-4">
          <li><a href="index.php" class="text-gray-600 hover:text-gray-900">Home</a></li>
          <li><a href="events.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
          <li><a href="about.php" class="text-gray-600 hover:text-gray-900">About us </a></li>
        </ul>
      </nav>
    </div>
    <!-- Sign In Button and sign up -->
    <div class="flex items-center space-x-4">
      <button class="hidden sm:block px-4 py-2 bg-blue-500  text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
        <a href="login.php">Sign In</a>
      </button>
      <button class="hidden sm:block px-4 py-2 bg-blue-500  text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
        <a href="signup.php">Create account</a>
      </button>
      <button class="mobile-menu-button sm:hidden">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6C4 5.44772 4.44772 5 5 5H19C19.5523 5 20 5.44772 20 6C20 6.55228 19.5523 7 19 7H5C4.44772 7 4 6.55228 4 6ZM4 11C4 10.4477 4.44772 10 5 10H19C19.5523 10 20 10.4477 20 11C20 11.5523 19.5523 12 19 12H5C4.44772 12 4 11.5523 4 11ZM5 17C4.44772 17 4 17.4477 4 18C4 18.5523 4.44772 19 5 19H19C19.5523 19 20 18.5523 20 18C20 17.4477 19.5523 17 19 17H5Z" fill="currentColor"/>
        </svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu">
    <ul>
      <li><a href="index.php" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Home</a></li>

      <li><a href="events.php" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Events</a></li>
      <li><a href="about.php" class="block px-4 py-2 text-gray-600 hover:text-gray-900">About</a></li>
    </ul>
  </div>
</header>

<script>
  document.querySelector('.mobile-menu-button').addEventListener('click', function() {
    var menu = document.querySelector('.mobile-menu');
    menu.classList.toggle('active');
  });
</script>

<!-- Main Content -->
<main class="container mx-auto px-6 py-8"><br><br><br><br>

  <!-- Hero Section -->
  <!-- <header class="bg-gray-50 py-10 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900">
          Welcome to <span class="text-indigo-600">Eventify</span>
        </h1>
        <p class="mt-4 text-xl sm:text-2xl md:text-3xl lg:text-4xl text-gray-700">
          Discover and attend exciting events near you.
        </p>
        <div class="mt-8">
          <a href="events.php" class="text-xl sm:text-2xl lg:text-3xl bg-blue-500 hover:bg-indigo-700 px-6 py-3 sm:px-8 sm:py-4 rounded-full inline-block font-semibold text-center text-white shadow-lg transition duration-300 ease-in-out">Explore Events</a>
        </div>
      </div>
    </div>
  </header> -->

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
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="signup.php">
                        Don't have an acount?
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
