<?php
include('../includes/dbconnection.php');

if (isset($_GET['searchEvents'])) {
    $search = $_GET['searchEvents'];
    $eventSql = "SELECT event_id, title, description, date, time, location, capacity, space_available, event_image, category, entrance_fee 
                FROM events 
                WHERE title LIKE '%$search%' 
                OR time LIKE '%$search%' 
                OR location LIKE '%$search%'";
} else {
    $eventSql = "SELECT event_id, title, description, date, time, location, capacity, space_available, event_image, category, entrance_fee FROM events";
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
            <input type="text" name="searchEvents" id="searchInput" placeholder="Search by title, time, or location" class="border p-2 w-full mb-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
            <?php if(isset($_GET['searchEvents'])): ?>
                <button type="button" onclick="resetSearch()" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Reset Search</button>
            <?php endif; ?>
        </div>
    </form>
    <table id="eventTable" class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Event ID</th>
                <th class="py-2 px-4 border-b">Title</th>
                <th class="py-2 px-4 border-b">Description</th>
                <th class="py-2 px-4 border-b">Date</th>
                <th class="py-2 px-4 border-b">Time</th>
                <th class="py-2 px-4 border-b">Location</th>
                <th class="py-2 px-4 border-b">Capacity</th>
                <th class="py-2 px-4 border-b">Space Available</th>
                <th class="py-2 px-4 border-b">Category</th>
                <th class="py-2 px-4 border-b">Entrance Fee</th>
                <th class="py-2 px-4 border-b">Event Image</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $eventResult->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo $row['event_id']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['title']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['description']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['date']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['time']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['location']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['capacity']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['space_available']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['category']; ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $row['entrance_fee']; ?></td>
                    <td class="py-2 px-4 border-b">
                        <?php if($row['event_image']): ?>
                            <img src="<?php echo $row['event_image']; ?>" alt="Event Image" class="w-16 h-16 object-cover">
                        <?php else: ?>
                            No image
                        <?php endif; ?>
                    </td>
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
