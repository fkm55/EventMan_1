<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['tx_ref'])) {
    header('Location: uevents.php');
    exit();
}

$tx_ref = $_GET['tx_ref'];

// Function to verify payment
function verifyPayment($tx_ref) {
    $url = "https://api.chapa.co/v1/transaction/verify/$tx_ref";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer CHASECK_TEST-nRqUFxPt9F2RdUBD1D3llOm2RysYmeK3',
        'Content-Type: application/json'
    ]);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        return false;
    }
    curl_close($ch);

    return json_decode($result, true);
}

$response = verifyPayment($tx_ref);

if ($response && isset($response['status']) && $response['status'] == 'success') {
    $ticket_id = $response['data']['metadata']['ticket_id']; // Assuming you stored ticket_id in metadata
    
    // Start a transaction
    $conn->beginTransaction();

    try {
        // Update the ticket as paid
        $stmt = $conn->prepare("UPDATE tickets SET paid = 1 WHERE ticket_id = :ticket_id");
        $stmt->bindParam(':ticket_id', $ticket_id);
        $stmt->execute();

        // Decrease the space available in the events table
        $stmt = $conn->prepare("UPDATE events SET space_available = space_available - 1 WHERE event_id = (SELECT event_id FROM tickets WHERE ticket_id = :ticket_id)");
        $stmt->bindParam(':ticket_id', $ticket_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        header('Location: user.php?status=success&ticket_id=' . $ticket_id);
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollBack();
        echo "Payment verification failed. Error: " . $e->getMessage();
        header('Location: user.php?status=failed');
        exit();
    }
} else {
    echo "Payment verification failed.";
    header('Location: user.php?status=failed');
    exit();
}
?>
