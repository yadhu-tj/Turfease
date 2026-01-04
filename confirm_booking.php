<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"));

if (isset($data->sport, $data->court, $data->date, $data->times, $data->payment)) {
    // Database connection
    $conn = new mysqli("localhost", "username", "password", "database_name");
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (sport, court, date, timeslots, payment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $data->sport, $data->court, $data->date, $data->times, $data->payment);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error saving booking"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>
