<?php
include('../includes/dbconnection.php');

if (isset($_GET['searchEvents'])) {
    $search = $_GET['searchEvents'];
    $eventSql = "SELECT event_host_name, title, description, phone_number 
                FROM events_request 
                WHERE event_host_name LIKE '%$search%' 
                OR title LIKE '%$search%'";
} else {
    $eventSql = "SELECT event_host_name, title, description, phone_number FROM events_request";
}

$eventResult = $conn->query($eventSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <h2 class="text-2xl font-bold mb-4">Search Events</h2>
    <form id="searchForm" method="GET" action="">
        <div class="flex mb-4">
            <input type="text" name="searchEvents" id="searchInput" placeholder="Search by event host name or title" class="border p-2 w-full mb-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
            <?php if(isset($_GET['searchEvents'])): ?>
                <button type="button" onclick="resetSearch()" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Reset Search</button>
            <?php endif; ?>
        </div>
    </form>
    <table id="eventTable" class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Event Host Name</th>
                <th class="py-2 px-4 border-b">Title</th>
                <th class="py-2 px-4 border-b">Description</th>
                <th class="py-2 px-4 border-b">Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $eventResult->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo $row['event_host_name']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['title']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['description']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['phone_number']; ?></td>
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
