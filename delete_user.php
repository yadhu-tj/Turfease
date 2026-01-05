<?php
session_start();

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Database connection
include('includes/dbconnection.php');

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