<?php
// cancel_booking.php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
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

    $bookingId = $_POST['id'];

    // Fetch the booking details first
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();

        // Move the booking details to the canceled_bookings table
        $insertStmt = $conn->prepare("INSERT INTO canceled_bookings (id, appointment_date, slot_tym, court, created_at, payment_method)
                                     VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("isssss", $booking['id'], $booking['appointment_date'], $booking['slot_tym'], 
                                        $booking['court'], $booking['created_at'], $booking['payment_method']);
        
        if ($insertStmt->execute()) {
            // If insertion is successful, delete the booking from the original table
            $deleteStmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
            $deleteStmt->bind_param("i", $bookingId);

            if ($deleteStmt->execute()) {
                // Send success response
                echo json_encode(['success' => true, 'message' => 'Booking successfully canceled and details saved.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error deleting the booking.']);
            }

            $deleteStmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error moving booking to canceled bookings.']);
        }

        $insertStmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Booking not found.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}
?>
