<?php
// Your existing PHP logic
include('includes/dbconnection.php');

// Check if the search form is submitted for users
if (isset($_GET['searchUsers'])) {
    $search = $_GET['searchUsers'];
    // Retrieve users matching the search query
    $userSql = "SELECT user_id, username, phone_number, email, gender, date_of_birth, status 
                FROM users 
                WHERE CONCAT(first_name, ' ', last_name) LIKE '%$search%' 
                OR phone_number LIKE '%$search%' 
                OR email LIKE '%$search%'";
} else {
    // If no search query, retrieve all users
    $userSql = "SELECT user_id, username, phone_number, email, gender, date_of_birth, status FROM users";
}

$userResult = $conn->query($userSql);

// Check if the create user form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['createUser'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $status = $_POST['status'];
    $role = $_POST['role'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];

    // Insert the new user into the database
    $insertSql = "INSERT INTO users (username, email, password, first_name, last_name, status, role, gender, date_of_birth, phone_number) 
                VALUES (:username, :email, :password, :first_name, :last_name, :status, :role, :gender, :date_of_birth, :phone_number)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':phone_number', $phone_number);

    if ($stmt->execute()) {
        $message = "Account created successfully.";
    } else {
        $message = "Error creating account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-1/4 p-6">
            <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Manage Accounts</h3>
                <ul>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('search_users')">Search Users</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('manage_user')">Manage User</button></li>   
                </ul>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Events</h3>
                <ul>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('createEvent')">Create Event</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('editEvent')">Edit Event</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('deleteEvent')">Delete Event</button></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xl font-semibold mb-2">Tickets</h3>
                <ul>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('createTicket')">Create Ticket</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('manageTicket')">Manage Ticket</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('deleteTicket')">Delete Ticket</button></li>
                </ul>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content" class="w-3/4 p-6">
            <!-- Default Content -->
            <h2 class="text-2xl font-bold">Welcome to the Admin Dashboard</h2>
            <p>Select an option from the sidebar to manage the system.</p>
        </div>
    </div>

    <script>
        function showContent(contentId) {
            const contentDiv = document.getElementById('content');

            // Clear the content area
            contentDiv.innerHTML = `<iframe src="admin/${contentId}.php" frameborder="0" class="w-full h-screen"></iframe>`;
        }
    </script>

</body>
</html>
