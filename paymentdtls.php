<?php
session_start();
// Database connection
include('includes/dbconnection.php');

// Initialize filter date variable
$filter_date = '';

// Check if the filter date is set
if (isset($_POST['filter_date']) && !empty($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];
}

// Prepare SQL query to fetch data, including the filter if set
$sql = "SELECT email, payment_method, appointment_date, amount FROM bookings";
if ($filter_date) {
    $sql .= " WHERE appointment_date = '$filter_date'"; // Apply filter for specific date
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - TurfEase</title>
    <link rel="stylesheet" href="assets/css/paymentdtls.css">
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" class="logo" alt="TurfEase Logo">
        <ul>
            <li><a href="admin_home.php">Home</a></li>
        </ul>
    </div>

    <!-- Filter form -->


    <div class="booking-details-page">
        <h2>Your Payment Details</h2>
        <div class="filter-form">
            <form method="POST" action="paymentdtls.php">
                <label for="filter_date">Filter by Booking Date:</label>
                <input type="date" name="filter_date" id="filter_date"
                    value="<?php echo htmlspecialchars($filter_date); ?>">
                <button type="submit">Filter</button>
                <!-- Reset Filter Button -->
                <button type="submit" name="reset_filter" value="true">Reset </button>
            </form>
        </div>
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td>Paid</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No bookings found for this date.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>