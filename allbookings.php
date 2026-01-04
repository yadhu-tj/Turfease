<?php
session_start();
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "turfdb";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed. Please try again later.");
}

// Default query to fetch all bookings
$sql = "SELECT * FROM bookings";

// Check if a date filter has been applied
$filter_date = '';
if (isset($_POST['filter_date']) && !empty($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];
    $sql = "SELECT * FROM bookings WHERE appointment_date = '$filter_date'";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<head>
    <title>All Bookings</title>
    <link rel="stylesheet" href="allbooking.css">
</head>
<body>
    <div class="header">
        <img src="images/turfease logo.png" class="logo">
        <ul>
            <li><a href="admin_home.php">Home</a></li>                                                                            
        </ul>
    </div>
    <div class="booking-details-page">
        <h2>Your Booking Details</h2>

        <!-- Date Filter Form -->
        <form method="POST" action="allbookings.php" class="filter-form">
            <label for="filter_date">Filter by Date:</label>
            <input type="date" name="filter_date" id="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
            <button type="submit">Filter</button>
            <button type="submit" name="reset_filter">Reset</button>
        </form>

        <table class="booking-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Court</th>
                    <th>Booked At</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['slot_tym']); ?></td>
                            <td><?php echo htmlspecialchars($row['court']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <p><?php echo htmlspecialchars($row['payment_method']); ?></p>
                            </td>
                            <td>
                                <button class="cancel-btn" data-id="<?php echo $row['id']; ?>">Cancel</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
