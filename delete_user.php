<?php
session_start();

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "turfdb";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get user ID from request
$userId = $_POST['id'] ?? null;

if ($userId) {
    // Delete user
    $stmt = $conn->prepare("DELETE FROM signup WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to remove user']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
}

$conn->close();
?>
