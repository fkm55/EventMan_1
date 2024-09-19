<?php
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
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('manageAccounts')">Manage Accounts</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('searchUsers')">Search Users</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('createUser')">Create User</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('editAccount')">Edit Account</button></li>
                    <li><button class="w-full text-left py-2 px-4 hover:bg-gray-700" onclick="showContent('terminateAccount')">Terminate Account</button></li>
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
            contentDiv.innerHTML = '';

            // Manage Accounts
            if (contentId === 'manageAccounts') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Manage Accounts</h2>
                    <p>Manage accounts here...</p>
                `;
            }
// Search Users
else if (contentId === 'searchUsers') {
    contentDiv.innerHTML = `
        <h2 class="text-2xl font-bold mb-4">Search Users</h2>
        <form id="searchForm" method="GET" action="">
            <div class="flex mb-4">
                <input type="text" name="searchUsers" id="searchInput" placeholder="Search by name, phone number, or email" class="border p-2 w-full mb-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
                <?php if(isset($_GET['searchUsers'])): ?>
                    <button type="button" onclick="resetSearch()" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Reset Search</button>
                <?php endif; ?>
            </div>
        </form>
        <table id="userTable" class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">User ID</th>
                    <th class="py-2 px-4 border-b">Username</th>
                    <th class="py-2 px-4 border-b">Phone Number</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Gender</th>
                    <th class="py-2 px-4 border-b">Date of Birth</th>
                    <th class="py-2 px-4 border-b">Status</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php
                if ($userResult && $userResult->rowCount() > 0) {
                    while ($row = $userResult->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['phone_number']) . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['gender']) . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td class='py-2 px-4 border-b' colspan='7'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    `;
}




            // Create User
            else if (contentId === 'createUser') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Create User</h2>
                    <form>
                        <div class="mb-4">
                            <label class="block text-gray-700">Name</label>
                            <input type="text" class="border p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Email</label>
                            <input type="email" class="border p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Password</label>
                            <input type="password" class="border p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Role</label>
                            <select class="border p-2 w-full">
                                <option>Admin</option>
                                <option>User</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create User</button>
                    </form>
                `;
            }

            // Edit Account
            else if (contentId === 'editAccount') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Edit Account</h2>
                    <p>Edit account here...</p>
                `;
            }

            // Terminate Account
            else if (contentId === 'terminateAccount') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Terminate Account</h2>
                    <p>Terminate account here...</p>
                `;
            }

            // Create Event
            else if (contentId === 'createEvent') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Create Event</h2>
                    <form>
                        <div class="mb-4">
                            <label class="block text-gray-700">Title</label>
                            <input type="text" class="border p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Description</label>
                            <textarea class="border p-2 w-full"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Date</label>
                            <input type="date" class="border p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Location</label>
                            <input type="text" class="border p-2 w-full">
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create Event</button>
                    </form>
                `;
            }

            // Edit Event
            else if (contentId === 'editEvent') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Edit Event</h2>
                    <p>Edit event here...</p>
                `;
            }

            // Delete Event
            else if (contentId === 'deleteEvent') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Delete Event</h2>
                    <p>Delete event here...</p>
                `;
            }

            // Create Ticket
            else if (contentId === 'createTicket') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Create Ticket</h2>
                    <form>
                        <div class="mb-4">
                            <label class="block text-gray-700">Event</label>
                            <select class="border p-2 w-full">
                                <option>Event 1</option>
                                <option>Event 2</option>
                                <option>Event 3</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Price</label>
                            <input type="text" class="border p-2 w-full">
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create Ticket</button>
                    </form>
                `;
            }

            // Manage Ticket
            else if (contentId === 'manageTicket') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Manage Ticket</h2>
                    <p>Manage ticket here...</p>
                `;
            }

            // Delete Ticket
            else if (contentId === 'deleteTicket') {
                contentDiv.innerHTML = `
                    <h2 class="text-2xl font-bold mb-4">Delete Ticket</h2>
                    <p>Delete ticket here...</p>
                `;
            }
        }

        function fetchUsers(searchValue) {
            fetch(`search_users.php?search=${searchValue}`)
                .then(response => response.json())
                .then(data => {
                    const userTableBody = document.getElementById('userTableBody');
                    userTableBody.innerHTML = '';

                    data.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="py-2 px-4 border-b">${user.user_id}</td>
                            <td class="py-2 px-4 border-b">${user.username}</td>
                            <td class="py-2 px-4 border-b">${user.phone_number}</td>
                            <td class="py-2 px-4 border-b">${user.email}</td>
                            <td class="py-2 px-4 border-b">${user.gender}</td>
                            <td class="py-2 px-4 border-b">${user.date_of_birth}</td>
                            <td class="py-2 px-4 border-b">${user.status}</td>
                        `;
                        userTableBody.appendChild(row);
                    });
                });
        }

        function resetSearch() {
    // Clear search input value
    document.getElementById('searchInput').value = '';

    // Clear the table body
    document.getElementById('userTableBody').innerHTML = '';

    // Submit the search form to reload the table
    document.getElementById('searchForm').submit();
}
    </script>
</body>
</html>
