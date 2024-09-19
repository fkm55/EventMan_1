<?php
session_start();
include('includes/dbconnection.php');

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];
    $role = 0; // Default role
    $status = 1; // Default status

    // Validate first name and last name (only letters, no spaces)
    if (!preg_match("/^[a-zA-Z]+$/", $first_name) || !preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $error_message = "First name and last name must contain only letters without spaces.";
    }
    // Validate phone number length and starting digits
    elseif (!preg_match("/^09\d{8}$/", $phone_number)) {
        $error_message = "Phone number must be exactly 10 digits and start with '09'.";
    }
    // Validate required fields
    elseif (empty($username) || empty($email) || empty($password) || empty($gender) || empty($date_of_birth)) {
        $error_message = "All fields are required.";
    } else {
        // Check if the username already exists
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error_message = "The username is already taken. Please choose a different username.";
        } else {
            // Check if the user is at least 18 years old and born before 2006
            $dob_timestamp = strtotime($date_of_birth);
            $current_year = date('Y');
            $min_birth_year = $current_year - 18;
            $max_birth_year = 2006;

            if ($dob_timestamp >= strtotime("$min_birth_year-01-01") || $dob_timestamp <= strtotime("$max_birth_year-12-31")) {
              $error_message = "<span style='color: red;'>You must be at least 18 years old.</span>";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert user data into the database
                try {
                    $sql = "INSERT INTO users (username, email, password, first_name, last_name, status, role, gender, date_of_birth, phone_number) 
                            VALUES (:username, :email, :password, :first_name, :last_name, :status, :role, :gender, :date_of_birth, :phone_number)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    $stmt->bindParam(':status', $status);
                    $stmt->bindParam(':role', $role);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':date_of_birth', $date_of_birth);
                    $stmt->bindParam(':phone_number', $phone_number);
                    
                    if ($stmt->execute()) {
                        $success_message = "Account created successfully. Please <a href='login.php'>sign in</a>.";
                    } else {
                        $error_message = "Failed to create an account. Please try again.";
                    }
                } catch (PDOException $e) {
                    $error_message = "Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link href="./output.css" rel="stylesheet">

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
<!-- Navbar -->
<header class="bg-white shadow-md">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-6">
        <h1 class="font-semibold text-xl text-gray-800"><a href="index.php">Eventify</a></h1>
        <nav>
          <ul class="flex space-x-4">
            <li><a href="index.php" class="text-gray-600 hover:text-gray-900">Home</a></li>
            <li><a href="events.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
            <li><a href="about.php" class="text-gray-600 hover:text-gray-900">About</a></li>
          </ul>
        </nav>
      </div>
      <div class="flex items-center space-x-4">
        <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="login.php">Sign In</a></button>
        <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="signup.php">Create account</a></button>
      </div>
    </div>
  </header>
<body class="font-sans bg-gray-100">
  <div class="container mx-auto px-6 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-md">
      <div class="py-4 px-6">
        <h2 class="text-2xl font-semibold mb-4">Sign Up</h2>
        <?php if ($error_message) : ?>
          <p class="text-red-500 text-xs italic mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message) : ?>
          <p class="text-green-500 text-xs italic mb-4"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="signup.php" method="POST">
          <div class="mb-4">
            <label for="first_name" class="block text-gray-700 font-semibold mb-2">First Name</label>
            <input type="text" id="first_name" name="first_name" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <div class="mb-4">
            <label for="last_name" class="block text-gray-700 font-semibold mb-2">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <div class="mb-4">
            <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
            <input type="text" id="username" name="username" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" id="email" name="email" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <div class="mb-4">
            <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
            <input type="password" id="password" name="password" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <div class="mb-4">
            <label for="gender" class="block text-gray-700 font-semibold mb-2">Gender</label>
            <select id="gender" name="gender" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
              <option value="">Select</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="date_of_birth" class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <div class="mb-4">
            <label for="phone_number" class="block text-gray-700 font-semibold mb-2">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" class="border border-gray-300 rounded-md px-4 py-2 w-full" required>
          </div>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-600 transition duration-300">Sign Up</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

