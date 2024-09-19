<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['ticket_id'])) {
    header('Location: uevents.php');
    exit();
}

$ticket_id = $_GET['ticket_id'];

// Get ticket and event details
$stmt = $conn->prepare("SELECT t.*, e.title, e.entrance_fee, e.date, e.location FROM tickets t JOIN events e ON t.event_id = e.event_id WHERE t.ticket_id = :ticket_id AND t.user_id = :user_id");
$stmt->bindParam(':ticket_id', $ticket_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Invalid ticket.";
    exit();
}

// Prepare payment parameters
$amount = max($ticket['entrance_fee'], 10); // Ensure minimum amount is met (e.g., 10 ETB)
$currency = "ETB";
$email = "test@gmail.com"; // Use a valid email format
$first_name = $ticket['full_name']; // Assuming the full name is stored in ticket's full_name
$last_name = ""; // If you have last name separately, fetch it
$tx_ref = "txn-" . uniqid(); // Unique transaction reference
$callback_url = "https://google.com";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process payment
    $response = processPayment($amount, $currency, $email, $first_name, $last_name, $tx_ref, $callback_url);

    if ($response && isset($response['status']) && $response['status'] == 'success') {
        $payment_url = $response['data']['checkout_url'];
        header("Location: " . $payment_url);
        exit();
    } else {
        echo "Payment initialization failed. Response: ";
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }
}

function processPayment($amount, $currency, $email, $first_name, $last_name, $tx_ref, $callback_url) {
    $url = "https://api.chapa.co/v1/transaction/initialize";
    $data = [
        "amount" => $amount,
        "currency" => $currency,
        "email" => $email,
        "first_name" => $first_name,
        "last_name" => $last_name,
        "tx_ref" => $tx_ref,
        "callback_url" => $callback_url,
        "metadata" => [
            "ticket_id" => $_GET['ticket_id'] // Include ticket_id in metadata
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer CHASECK_TEST-nRqUFxPt9F2RdUBD1D3llOm2RysYmeK3', // Replace with your actual API key
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-semibold text-center mb-4">Checkout</h1>
                <div class="mb-4">
                    <p class="text-gray-700 mb-2"><strong>Event:</strong> <?php echo htmlspecialchars($ticket['title']); ?></p>
                    <p class="text-gray-700 mb-2"><strong>Date:</strong> <?php echo htmlspecialchars($ticket['date']); ?></p>
                    <p class="text-gray-700 mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($ticket['location']); ?></p>
                    <p class="text-gray-700 mb-2"><strong>Full Name:</strong> <?php echo htmlspecialchars($ticket['full_name']); ?></p>
                    <p class="text-gray-700 mb-2"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($ticket['dob']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>Amount:</strong> <?php echo htmlspecialchars($ticket['entrance_fee']); ?> ETB</p>
                </div>
                <form method="POST" action="checkout.php?ticket_id=<?php echo $ticket_id; ?>">
                    <button type="submit" class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-600 transition duration-300">
                        Pay with Chapa
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
