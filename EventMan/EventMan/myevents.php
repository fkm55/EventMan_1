<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch attendee's events details
$sql = "SELECT t.full_name, e.title as event_name, e.date as event_date, e.time as event_time, t.ticket_id 
        FROM tickets t 
        JOIN events e ON t.event_id = e.event_id 
        WHERE t.user_id = :user_id AND t.paid = 0"; // Assuming 'paid' column indicates ticket is confirmed
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events</title>
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
                <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="logout.php">Logout</a></button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">My Events</h2>
            <?php if (count($events) > 0): ?>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">Full Name</th>
                            <th class="py-2">Event Name</th>
                            <th class="py-2">Event Date</th>
                            <th class="py-2">Event Time</th>
                            <th class="py-2">Download Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($event['full_name']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($event['event_name']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($event['event_date']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($event['event_time']); ?></td>
                                <td class="border px-4 py-2">
                                    <a href="download_ticket.php?ticket_id=<?php echo $event['ticket_id']; ?>" class="text-blue-500 hover:underline">Download</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-700">No events found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
