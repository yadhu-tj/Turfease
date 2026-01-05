<?php
session_start();

// Check if booking is confirmed
if (!isset($_SESSION['message'])) {
    header("Location: book.php");
    exit;
}

$total_price = $_SESSION['total_price'];
$message = $_SESSION['message'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - TurfEase</title>
    <link rel="stylesheet" href="assets/css/receipt.css">
</head>

<body>
    <div class="receipt-container">
        <h1>Booking Confirmation</h1>
        <p><?php echo $message; ?></p>

        <div class="receipt-details">
            <h2>Booking Details:</h2>
            <p><strong>Sport:</strong> <?php echo $_SESSION['sport']; ?></p>
            <p><strong>Court:</strong> <?php echo $_SESSION['court']; ?></p>
            <p><strong>Date:</strong> <?php echo $_SESSION['appointment_date']; ?></p>
            <p><strong>Time Slot(s):</strong> <?php echo $_SESSION['slot_tym']; ?></p>
        </div>

        <div class="payment-summary">
            <h3>Total Amount: ₹<?php echo $total_price; ?></h3>
            <p>Thank you for booking with us! Please proceed with the payment.</p>
        </div>

        <button onclick="window.location.href='payment.php'">Proceed to Payment</button>
    </div>
</body>

</html>
<?php
unset($_SESSION['message']);
unset($_SESSION['total_price']);
?>