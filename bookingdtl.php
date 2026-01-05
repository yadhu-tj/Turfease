<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

// Database connection
include('includes/dbconnection.php');

// Fetch the user's booking details
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, appointment_date, slot_tym, court, created_at, payment_method, amount FROM bookings WHERE email = ?");
if (!$stmt) {
    die("Error fetching bookings. Please try again later.");
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Booking Receipt</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bookingdtl.css">
    <style>

    </style>
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" alt="TurfEase Logo">
        <ul class="nav-links">
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="home.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="receipt-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="receipt">
                    <div class="receipt-title">Booking Receipt</div>
                    <div class="booking-details">
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($row['appointment_date']); ?></p>
                        <p><strong>Time Slot:</strong> <?php echo htmlspecialchars($row['slot_tym']); ?></p>
                        <p><strong>Court:</strong> <?php echo htmlspecialchars($row['court']); ?></p>
                        <p><strong>Amount: </strong> ₹<?php echo htmlspecialchars($row['amount']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($row['payment_method']); ?></p>
                    </div>
                    <div class="actions">
                        <button class="cancel-btn" data-id="<?php echo $row['id']; ?>">Cancel Booking</button>
                    </div>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-bookings">No bookings found.</div>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function () {
                const bookingId = this.getAttribute('data-id');
                if (confirm("Are you sure you want to cancel this booking?")) {
                    fetch('cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + bookingId
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message || "Booking successfully canceled.");
                                location.reload();
                            } else {
                                alert(data.error || "An error occurred.");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("An unexpected error occurred.");
                        });
                }
            });
        });
    </script>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>