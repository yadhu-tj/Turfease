<?php
session_start();



// Use the session variable to get the admin's name
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; // Default to 'Admin' if not set
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Home</title>
    <link rel="stylesheet" href="assets/css/admin_home.css">
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" class="logo">
    </div>
    <div class="content">
        <h1>WELCOME ADMIN</h1>
        <P>WHAT WOULD YOU LIKE TO DO TODAY</P>
        <a href="allbookings.php"><button class="btn">BOOKING DETAILS</button></a>
        <a href="usermanagement.php"><button class="btn">USER MANAGEMENT</button></a>
        <a href="paymentDtls.php"><button class="btn">PAYMENT DETAILS</button></a>
    </div>
    <!-- Sidebar Content -->
    <div id="sidebar">
        <div class="sidebar-header">
            <img src="assets/img/profile2.png" alt="User Icon" class="user-icon">
            <h2><?php echo htmlspecialchars($userName); ?></h2>
        </div>
        <div class="sidebar-content">
            <a href="logout.php">LOGOUT</a> <!-- Link to the logout script -->
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select the sidebar and toggle button
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.createElement('button');
            toggleButton.innerHTML = '☰'; // Menu icon
            toggleButton.className = 'sidebar-toggle';
            document.body.appendChild(toggleButton);

            // Function to toggle the sidebar and button position
            function toggleSidebar() {
                sidebar.classList.toggle('show'); // Add or remove the 'show' class
                toggleButton.classList.toggle('move-left'); // Add or remove the 'move-left' class
            }

            // Event listener for the toggle button
            toggleButton.addEventListener('click', toggleSidebar);
        });
    </script>
</body>

</html>