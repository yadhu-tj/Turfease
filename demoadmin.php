<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php"); // Redirect to login if not admin
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .content {
            padding: 20px;
        }

        a {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #4e73df;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #3b5bbf;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, Admin <?php echo htmlspecialchars($username); ?></h1>
    </div>
    <div class="content">
        <h2>Admin Actions</h2>
        <a href="allbookings.php">View All Bookings</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="reports.php">Generate Reports</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
