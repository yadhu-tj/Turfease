<?php
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "Booking successful!";
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful</title>
    <link rel="stylesheet" href="successMessage.css">
</head>

<body>
    <div class="successMessage">
    <h1><?php echo htmlspecialchars($message); ?></h1>
    <p>Thank you for your booking.</p>
    <a href="home.php">OK</a>
    </div>
</body>

</html>
