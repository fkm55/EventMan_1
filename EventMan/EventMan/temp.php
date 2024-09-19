<?php
session_start();
include('includes/dbconnection.php');
require('fpdf186/fpdf.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['ticket_id'])) {
    header('Location: myevents.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$ticket_id = $_GET['ticket_id'];

// Fetch ticket and event details
$sql = "SELECT t.full_name, e.title as event_name, e.date as event_date, e.time as event_time, t.ticket_id, t.dob
        FROM tickets t 
        JOIN events e ON t.event_id = e.event_id 
        WHERE t.ticket_id = :ticket_id AND t.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':ticket_id', $ticket_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Invalid ticket.";
    exit();
}

class PDF extends FPDF {
    function Header() {
        // Header background color and text color
        $this->SetFillColor(230, 74, 25);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 18);
        
        // Ticket title
        $this->Cell(0, 20, 'Event Ticket', 0, 1, 'C', true);
        $this->Ln(5);
    }

    function Footer() {
        // Footer background color and text color
        $this->SetY(-20);
        $this->SetFillColor(230, 74, 25);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'I', 8);
        
        // Footer content
        $this->Cell(0, 10, 'Thank you for attending!', 0, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AddPage();


$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Ticket ID: ' . $ticket['ticket_id'], 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Event: ' . $ticket['event_name'], 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Date: ' . $ticket['event_date'], 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Time: ' . $ticket['event_time'], 0, 1, 'C');
$pdf->Ln(15);

$pdf->SetFont('Arial', 'U', 14);
$pdf->Cell(0, 10, 'Attendee Information', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Full Name: ' . $ticket['full_name'], 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Date of Birth: ' . $ticket['dob'], 0, 1, 'C');
$pdf->Ln(15);

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'This ticket is non-transferable and must be presented at the event.', 0, 1, 'C');
$pdf->Ln(10);

$pdf->Output('D', 'ticket_' . $ticket['ticket_id'] . '.pdf'); // 'D' forces download
?>
