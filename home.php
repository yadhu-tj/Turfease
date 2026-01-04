<?php
session_start();

// Set the username when the user logs in
// For example, you might set this when the user logs in
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Check if username is set, otherwise default to 'Guest'
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="home.css">  
</head>
<body>
    <div class="header">
        <img src="images/turfease logo.png" class="logo">
        <ul>
            <li><a href="contact.php">Contact us</a></li>
            <li><a href="about.php">About us</a></li>                                                                            
        </ul>
    </div>
    <div class="content">
        <h1>BOOK YOUR SLOTS</h1>
        <h1>WITH EASE</h1>
        <p>ENJOY YOUR PLAYTIME WITH</p>
        <p>TURFEASE</p>
        <a href="book.php"><button type="button">BOOK NOW</button></a>
    </div>

    <div class="slideshow">
        <img id="image1" src="images/messi.png" class="active" alt="Slideshow Image">
        <img id="image2" src="images/ronaldo.png" alt="Slideshow Image">
        <img id="image3" src="images/neymar.png" alt="Slideshow Image">
    </div> 

    <!-- Sidebar Content -->
    <div id="sidebar">
    <div class="sidebar-header">
        <img src="images/profile2.png" alt="User Icon" class="user-icon">
        <h2><?php echo htmlspecialchars($userName); ?></h2>
    </div>
    <div class="sidebar-content">
        <a href="bookingdtl.php">YOUR BOOKING DETAILS</a>
        <a href="logout.php">LOGOUT</a> <!-- Link to the logout script -->
    </div>
</div>

<script src="home.js"></script>
</body>
</html>
