<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details to display in the welcome message
$sql = "SELECT username FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $user['username'] ?? 'User'; // Default to 'User' if username is not found

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

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    // Insert the new user into the database
    $insertSql = "INSERT INTO users (username, email, password, first_name, last_name, status, role, gender, date_of_birth, phone_number) 
                VALUES (:username, :email, :password, :first_name, :last_name, :status, :role, :gender, :date_of_birth, :phone_number)";
    $stmt = $conn->prepare($insertSql);
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
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <h1 class="font-semibold text-xl text-gray-800"><a href="index.php">Eventify</a></h1>
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="admin.php" class="text-gray-600 hover:text-gray-900">Dashboard</a></li>
                        <!-- <li><a href="uevents.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
                        <li><a href="about.php" class="text-gray-600 hover:text-gray-900">About</a></li> -->
                    </ul>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="login.php">Logout</a></button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <!-- Your existing main content here -->
    </main>

    <script>
        function showContent(contentId, description) {
            const sidebar = document.getElementById('sidebar');
            const contentDiv = document.getElementById('content');

            // Hide the sidebar
            sidebar.classList.add('hidden');

            // Adjust content area to take full width
            contentDiv.classList.remove('w-3/4');
            contentDiv.classList.add('w-full');

            // Clear the content area and insert the new content
            contentDiv.innerHTML = `<div class="flex items-center mb-4">
<button onclick="showDashboard()" class="text-xl mr-4">←</button>
<h2 class="text-2xl font-bold">${description}</h2>
</div>
<iframe src="admin/${contentId}.php" frameborder="0" class="w-full h-screen"></iframe>`;
        }

        function showDashboard() {
            const sidebar = document.getElementById('sidebar');
            const contentDiv = document.getElementById('content');

            // Show the sidebar
            sidebar.classList.remove('hidden');

            // Adjust content area to original width
            contentDiv.classList.remove('w-full');
            contentDiv.classList.add('w-3/4');

            // Reset the content area to the default content
            contentDiv.innerHTML = `
            <div class="center">
    <div class="bg-white rounded-lg shadow-lg p-8">
      <div class="flex items-center justify-center mb-6">
          <h2 class="text-3xl font-bold">Welcome to the Admin Dashboard</h2>
        //   <img src="slide_20.jpg" alt="Dashboard Image" class="w-24 h-24 rounded-full object-cover">
      </div>
      <p class="text-gray-600 mb-4">Select an option from the sidebar to manage the system.</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-gray-200 p-4 rounded-lg">
              <h3 class="text-xl font-semibold mb-2">Quick Links</h3>
              <ul class="list-disc list-inside">
                  <li>View User List</li>
                  <li>View Event Details</li>
                  <li>Manage Hosts</li>
              </ul>
          </div>
          <div class="bg-gray-200 p-4 rounded-lg">
              <h3 class="text-xl font-semibold mb-2">Statistics</h3>
              <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt nisl vitae urna ultrices aliquam.</p>
          </div>
      </div>
  </div>
  </div>
            `;
        }
    </script>
</body>

</html>






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
        <div id="sidebar" class="bg-gray-800 text-white w-1/4 p-6">
            <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Manage Accounts</h3>
                <ul>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('search_users', 'Search Users')">Search Users</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('manage_user', 'Manage Users')">Manage Users</button></li>
                </ul>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Events</h3>
                <ul>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('search_events', 'Search Events')">Search Events</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('manage_event', 'Manage Events')">Manage Events</button></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xl font-semibold mb-2">Hosts</h3>
                <ul>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('search_host', 'Search Host Event')">Search Host Event</button></li>
                </ul>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content" class="w-3/4 p-6">
            <!-- Default Content -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold">Welcome to the Admin Dashboard</h2>
                    <!-- <img src="dashboard_image.jpg" alt="Dashboard Image" class="w-24 h-24 rounded-full object-cover"> -->
                </div>
                <p class="text-gray-600 mb-4">Select an option from the sidebar to manage the system.</p>
                <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-200 p-4 rounded-lg">
                        <h3 class="text-xl font-semibold mb-2">Quick Links</h3>
                        <ul class="list-disc list-inside">
                            <li><a href="#" class="text-blue-500 hover:underline">View User List</a></li>
                            <li><a href="#" class="text-blue-500 hover:underline">View Event Details</a></li>
                            <li><a href="#" class="text-blue-500 hover:underline">Manage Hosts</a></li>
                        </ul>
                    </div>
                    <div class="bg-gray-200 p-4 rounded-lg">
                        <h3 class="text-xl font-semibold mb-2">Statistics</h3>
                        <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt nisl vitae urna ultrices aliquam.</p>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <script>
        function showContent(contentId, description) {
            const sidebar = document.getElementById('sidebar');
            const contentDiv = document.getElementById('content');

            // Hide the sidebar
            sidebar.classList.add('hidden');

            // Adjust content area to take full width
            contentDiv.classList.remove('w-3/4');
            contentDiv.classList.add('w-full');

            // Clear the content area and insert the new content
            contentDiv.innerHTML = `<div class="flex items-center mb-4">
<button onclick="showDashboard()" class="text-xl mr-4">←</button>
<h2 class="text-2xl font-bold">${description}</h2>
</div>
<iframe src="admin/${contentId}.php" frameborder="0" class="w-full h-screen"></iframe>
`;
}
function showDashboard() {
        const sidebar = document.getElementById('sidebar');
        const contentDiv = document.getElementById('content');

        // Show the sidebar
        sidebar.classList.remove('hidden');

        // Adjust content area to original width
        contentDiv.classList.remove('w-full');
        contentDiv.classList.add('w-3/4');

        // Reset the content area to the default content
        contentDiv.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold">Welcome to the Admin Dashboard</h2>
                    <img src="dashboard_image.jpg" alt="Dashboard Image" class="w-24 h-24 rounded-full object-cover">
                </div>
                <p class="text-gray-600 mb-4">Select an option from the sidebar to manage the system.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-gray-200 p-4 rounded-lg">
                        <h3 class="text-xl font-semibold mb-2">Quick Links</h3>
                        <ul class="list-disc list-inside">
                            <li><a href="#" class="text-blue-500 hover:underline">View User List</a></li>
                            <li><a href="#" class="text-blue-500 hover:underline">View Event Details</a></li>
                            <li><a href="#" class="text-blue-500 hover:underline">Manage Hosts</a></li>
                        </ul>
                    </div>
                    <div class="bg-gray-200 p-4 rounded-lg">
                        <h3 class="text-xl font-semibold mb-2">Statistics</h3>
                        <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt nisl vitae urna ultrices aliquam.</p>
                    </div>
                </div>
            </div>
        `;
    }
</script>
</body>
</html>
