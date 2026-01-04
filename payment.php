<?php
// Start the session
session_start();
include('includes/dbconnection.php');

// Check if the user is logged in (email should be in session)
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Fetch the logged-in user's email from session
$email = $_SESSION['email'];

if (!isset($_SESSION['booking_info'])) {
    header("Location: book.php");
    exit;
}

$booking_info = $_SESSION['booking_info'];
$errors = [];
$booking_successful = false; // Track success for the modal popup

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    if (empty($payment_method)) {
        $errors[] = "Please select a payment method.";
    }

    if (empty($errors)) {
        // Extract booking info
        $sport = $booking_info['sport'];
        $court = $booking_info['court'];
        $appointment_date = $booking_info['appointment_date'];
        $slot_tym = $booking_info['slot_tym'];
        $amount = $booking_info['amount'];

        $timeSlots = explode(", ", $slot_tym);

        // Insert each booking into the database
        foreach ($timeSlots as $slot) {
            $stmt = $conn->prepare("INSERT INTO bookings (email, sport, court, appointment_date, slot_tym, payment_method,amount) VALUES (?, ?, ?, ?, ?, ?,?)");
            if ($stmt === false) {
                $errors[] = "Error preparing SQL statement.";
                break;
            }
            $stmt->bind_param("sssssss", $email, $sport, $court, $appointment_date, $slot, $payment_method, $amount);
            if (!$stmt->execute()) {
                $errors[] = "Error executing SQL statement.";
                break;
            }
            $stmt->close();
        }

        // Clear session and mark success if no errors
        if (empty($errors)) {
            unset($_SESSION['booking_info']);
            $booking_successful = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="assets/css/payment.css">
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" class="logo">
    </div>
    <h2>Booking Details</h2>
    <div class="confirm_page">
        <!-- Display Booking Details -->
        <ul>
            <li>Sport: <?php echo htmlspecialchars($booking_info['sport']); ?></li>
            <li>Court: <?php echo htmlspecialchars($booking_info['court']); ?></li>
            <li>Appointment Date: <?php echo htmlspecialchars($booking_info['appointment_date']); ?></li>
            <li>Time Slots: <?php echo htmlspecialchars($booking_info['slot_tym']); ?></li>
            <li>Total Amount: ₹<?php echo htmlspecialchars($booking_info['amount']); ?></li>
        </ul>

        <!-- Payment Form -->
        <div class="pay">
            <form method="POST" action="payment.php">
                <label for="payment_method">Choose Payment Method:</label><br>
                <select name="payment_method" id="payment_method">
                    <option value="">Select</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="UPI">UPI</option>
                    <option value="Net Banking">Net Banking</option>
                </select>
                <br><br>
                <button type="submit">CONFIRM</button>
            </form>
        </div>
    </div>

    <!-- Display Errors -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal for Success -->
    <div class="modal-overlay"></div>
    <div class="modal">
        <h2>Booking Successful!</h2>
        <p>Your booking has been confirmed. Thank you for choosing TurfEase!</p>
        <button onclick="closeModal()">OK</button>
    </div>

    <script>
        function closeModal() {
            document.querySelector('.modal').style.display = 'none';
            document.querySelector('.modal-overlay').style.display = 'none';
            window.location.href = "home.php";
        }

        // Show modal if booking is successful
        <?php if ($booking_successful): ?>
            document.querySelector('.modal').style.display = 'block';
            document.querySelector('.modal-overlay').style.display = 'block';
        <?php endif; ?>
    </script>
</body>

</html>