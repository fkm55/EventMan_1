<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];
$full_name = $_POST['full_name'];
$dob = $_POST['dob']; // Ensure the form input name matches this variable

// Retrieve the entrance fee for the event
$stmt = $conn->prepare("SELECT entrance_fee FROM events WHERE event_id = :event_id");
$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if ($event) {
    $entrance_fee = $event['entrance_fee'];

    // Insert the ticket information into the tickets table with price set to entrance_fee
    $stmt = $conn->prepare("INSERT INTO tickets (event_id, full_name, dob, paid, user_id, price) VALUES (:event_id, :full_name, :dob, 0, :user_id, :entrance_fee)");
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR); // Correctly bind the dob parameter
    $stmt->bindParam(':entrance_fee', $entrance_fee, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Get the last inserted ticket ID
    $ticket_id = $conn->lastInsertId();

    // Redirect to checkout page with ticket_id
    header('Location: checkout.php?ticket_id=' . $ticket_id);
    exit();
} else {
    echo "Event not found.";
}
?>
