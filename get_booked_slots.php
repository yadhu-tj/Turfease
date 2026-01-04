<?php
// Include database connection
include('dbconnection.php');

// Check if the required parameters are set
if (isset($_GET['appointment_date']) && isset($_GET['court'])) {
    $appointment_date = $_GET['appointment_date'];
    $court = $_GET['court'];

    // Prepare the SQL query to fetch booked slots
    $stmt = $conn->prepare("SELECT slot_tym FROM bookings WHERE appointment_date = ? AND court = ?");
    
    // Bind the parameters to the query
    $stmt->bind_param("ss", $appointment_date, $court);
    
    // Execute the query
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    // Initialize an array to hold booked time slots
    $bookedSlots = [];
    
    // Fetch the results and store them in the array
    while ($row = $result->fetch_assoc()) {
        // Assume slot_tym is stored as a string of times separated by commas
        $times = explode(', ', $row['slot_tym']);
        $bookedSlots = array_merge($bookedSlots, $times);
    }

    // Remove duplicates (if any) and re-index the array
    $bookedSlots = array_unique($bookedSlots);
    $bookedSlots = array_values($bookedSlots);

    // Send booked slots as JSON response
    header('Content-Type: application/json');
    echo json_encode($bookedSlots);

} else {
    // Handle missing parameters
    http_response_code(400);
    echo json_encode(["error" => "Missing required parameters."]);
}
?>
