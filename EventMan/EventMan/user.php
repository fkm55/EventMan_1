<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user details
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
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <h1 class="font-semibold text-xl text-gray-800"><a href="index.html">Eventify</a></h1>
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="user.php" class="text-gray-600 hover:text-gray-900">Dashboard</a></li>
                        <li><a href="uevents.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
                        <li><a href="about.html" class="text-gray-600 hover:text-gray-900">About</a></li>
                    </ul>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="logout.php">Logout</a></button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">User Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">My Events</h3>
                    <ul class="list-disc pl-5">
                        <li><a href="myevents.php" class="text-blue-500">View Events</a></li>
                    </ul>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Edit Account</h3>
                    <ul class="list-disc pl-5">
                        <li><a href="edit_user.php" class="text-blue-500">Edit Account Details</a></li>
                    </ul>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Dashboard</h3>
                    <ul class="list-disc pl-5">
                        <li><a href="dashboard.php" class="text-blue-500">View Dashboard</a></li>
                    </ul>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Checkout</h3>
                    <ul class="list-disc pl-5">
                        <li><a href="checkout.php" class="text-blue-500">Proceed to Checkout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
