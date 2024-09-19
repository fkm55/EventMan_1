<?php
include('../includes/dbconnection.php');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = !empty($_POST['event_id']) ? $_POST['event_id'] : null;
    $title = !empty($_POST['title']) ? $_POST['title'] : null;
    $description = !empty($_POST['description']) ? $_POST['description'] : null;
    $date = !empty($_POST['date']) ? $_POST['date'] : null;
    $time = !empty($_POST['time']) ? $_POST['time'] : null;
    $location = !empty($_POST['location']) ? $_POST['location'] : null;
    $capacity = isset($_POST['capacity']) && $_POST['capacity'] !== '' ? $_POST['capacity'] : null;
    $space_available = isset($_POST['space_available']) && $_POST['space_available'] !== '' ? $_POST['space_available'] : 0;
    $category = !empty($_POST['category']) ? $_POST['category'] : null;
    $entrance_fee = isset($_POST['entrance_fee']) && $_POST['entrance_fee'] !== '' ? $_POST['entrance_fee'] : null;

    // Handle image upload
    $event_image = '';
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['event_image']['tmp_name'];
        $imageName = $_FILES['event_image']['name'];
        $imageSize = $_FILES['event_image']['size'];
        $imageType = $_FILES['event_image']['type'];
        $imageNameCmps = explode(".", $imageName);
        $imageExtension = strtolower(end($imageNameCmps));
        $newImageName = md5(time() . $imageName) . '.' . $imageExtension;
        
        // Use absolute path for the upload directory
        $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        $dest_path = $uploadFileDir . $newImageName;

        if (move_uploaded_file($imageTmpPath, $dest_path)) {
            $event_image = $newImageName; // Store only the filename in the database
        } else {
            $message = "There was an error uploading the image.";
            $messageType = "error";
        }
    }

    if ($capacity === null) {
        $message = "Capacity is a required field.";
        $messageType = "error";
    } else {
        if (isset($_POST['createEvent'])) {
            // Create event logic
            $insertSql = "INSERT INTO events (title, description, date, time, location, capacity, space_available, event_image, category, entrance_fee) 
                        VALUES (:title, :description, :date, :time, :location, :capacity, :space_available, :event_image, :category, :entrance_fee)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':capacity', $capacity);
            $stmt->bindParam(':space_available', $space_available);
            $stmt->bindParam(':event_image', $event_image);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':entrance_fee', $entrance_fee);

            if ($stmt->execute()) {
                $message = "Event created successfully.";
                $messageType = "success";
            } else {
                $message = "Error creating event.";
                $messageType = "error";
            }
        } elseif (isset($_POST['editEvent'])) {
            // Edit event logic
            if (empty($event_id)) {
                $message = "Event ID cannot be blank.";
                $messageType = "error";
            } else {
                $updateSql = "UPDATE events SET 
                    title = COALESCE(NULLIF(:title, ''), title),
                    description = COALESCE(NULLIF(:description, ''), description),
                    date = COALESCE(NULLIF(:date, ''), date),
                    time = COALESCE(NULLIF(:time, ''), time),
                    location = COALESCE(NULLIF(:location, ''), location),
                    capacity = COALESCE(NULLIF(:capacity, ''), capacity),
                    space_available = COALESCE(NULLIF(:space_available, ''), space_available),
                    event_image = COALESCE(NULLIF(:event_image, ''), event_image),
                    category = COALESCE(NULLIF(:category, ''), category),
                    entrance_fee = COALESCE(NULLIF(:entrance_fee, ''), entrance_fee)
                    WHERE event_id = :event_id";
                $stmt = $conn->prepare($updateSql);
                $stmt->bindParam(':event_id', $event_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':time', $time);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':capacity', $capacity);
                $stmt->bindParam(':space_available', $space_available);
                $stmt->bindParam(':event_image', $event_image);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':entrance_fee', $entrance_fee);

                if ($stmt->execute()) {
                    $message = "Event updated successfully.";
                    $messageType = "success";
                } else {
                    $message = "Error updating event.";
                    $messageType = "error";
                }
            }
        } elseif (isset($_POST['deleteEvent'])) {
            // Delete event logic
            if (empty($event_id)) {
                $message = "Event ID cannot be blank.";
                $messageType = "error";
            } else {
                $deleteSql = "DELETE FROM events WHERE event_id = :event_id";
                $stmt = $conn->prepare($deleteSql);
                $stmt->bindParam(':event_id', $event_id);

                if ($stmt->execute()) {
                    $message = "Event deleted successfully.";
                    $messageType = "success";
                } else {
                    $message = "Error deleting event.";
                    $messageType = "error";
                }
            }
        }
    }
}

if (isset($_GET['searchEvents'])) {
    $search = $_GET['searchEvents'];
    $eventSql = "SELECT event_id, title, description, date, time, location, capacity, space_available, event_image, category, entrance_fee 
                FROM events 
                WHERE title LIKE '%$search%' 
                OR date LIKE '%$search%' 
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
    <title>Manage Events</title>
</head>
<body>
    <h2 class="text-2xl font-bold mb-4">Manage Events</h2>
    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 <?php echo ($messageType == 'success') ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border rounded">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form id="eventForm" method="POST" action="" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="event_id" class="block text-gray-700">Event ID</label>
                <input type="text" name="event_id" id="event_id" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" id="description" class="border p-2 w-full"></textarea>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700">Date</label>
                <input type="date" name="date" id="date" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="time" class="block text-gray-700">Time</label>
                <input type="time" name="time" id="time" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="location" class="block text-gray-700">Location</label>
                <input type="text" name="location" id="location" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="capacity" class="block text-gray-700">Capacity</label>
                <input type="number" name="capacity" id="capacity" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="space_available" class="block text-gray-700">Space Available</label>
                <input type="number" name="space_available" id="space_available" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="category" class="block text-gray-700">Category</label>
                <input type="text" name="category" id="category" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="entrance_fee" class="block text-gray-700">Entrance Fee</label>
                <input type="number" name="entrance_fee" id="entrance_fee" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="event_image" class="block text-gray-700">Event Image</label>
                <input type="file" name="event_image" id="event_image" class="border p-2 w-full">
            </div>
        </div>
        <div class="flex gap-4">
            <button type="submit" name="createEvent" class="bg-blue-500 text-white px-4 py-2 rounded">Create Event</button>
            <button type="submit" name="editEvent" class="bg-yellow-500 text-white px-4 py-2 rounded">Edit Event</button>
            <button type="submit" name="deleteEvent" class="bg-red-500 text-white px-4 py-2 rounded">Delete Event</button>
        </div>
    </form>
</body>
</html>
