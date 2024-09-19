<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Fetch current password from the database
    $sql = "SELECT password FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($current_password, $user['password'])) {
        // Update user details and password if provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, password = :password WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $hashed_password);
        } else {
            $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
        }

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            $success_message = "Your account has been successfully updated.";
        } else {
            $error_message = "Error updating record: " . implode(" ", $stmt->errorInfo());
        }
    } else {
        $error_message = "Current password is incorrect.";
    }
}

$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="font-semibold text-xl text-gray-800"><a href="index.php">Eventify</a></h1>
        </div>
    </header>

    <!-- Edit Account Form -->
    <div class="flex justify-center mt-10">
        <div class="w-full max-w-md">
            <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST" action="">
                <?php if ($success_message): ?>
                <p class="text-green-500 text-xs italic mb-4"><?php echo $success_message; ?></p>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                <p class="text-red-500 text-xs italic mb-4"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">
                        First Name
                    </label>
                    <input id="first_name" name="first_name" type="text" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">
                        Last Name
                    </label>
                    <input id="last_name" name="last_name" type="text" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone_number">
                        Phone Number
                    </label>
                    <input id="phone_number" name="phone_number" type="text" value="<?php echo htmlspecialchars($user['phone_number']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="current_password">
                        Current Password
                    </label>
                    <input id="current_password" name="current_password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">
                        New Password
                    </label>
                    <input id="new_password" name="new_password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
