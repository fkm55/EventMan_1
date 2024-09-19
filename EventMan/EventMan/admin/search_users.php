<?php
include('../includes/dbconnection.php');

if (isset($_GET['searchUsers'])) {
    $search = $_GET['searchUsers'];
    $userSql = "SELECT user_id, username, phone_number, email, gender, date_of_birth, status 
                FROM users 
                WHERE CONCAT(first_name, ' ', last_name) LIKE '%$search%' 
                OR phone_number LIKE '%$search%' 
                OR email LIKE '%$search%'";
} else {
    $userSql = "SELECT user_id, username, phone_number, email, gender, date_of_birth, status FROM users";
}

$userResult = $conn->query($userSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
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
        <tbody>
            <?php while($row = $userResult->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo $row['user_id']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['username']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['phone_number']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['email']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['gender']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['date_of_birth']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        function resetSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchForm').submit();
        }
    </script>
</body>
</html>
