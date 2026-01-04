<?php
session_start(); // Start the session to manage user sessions
$errors = [];

// Database connection
include('includes/dbconnection.php');

// Handle signup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if there are no errors
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to determine if email is an admin
        $role = ($email == 'admin@example.com') ? 'admin' : 'user'; // Assign admin role based on email

        // Insert new user with role
        $stmt = $conn->prepare("INSERT INTO signup (username, email, password, role) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            error_log("SQL Error: " . $conn->error); // Log error for developers
            $errors[] = "An unexpected error occurred. Please try again later.";
        } else {
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            // Execute the statement
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
                exit;
            } else {
                error_log("Execution Error: " . $stmt->error); // Log error for developers
                $errors[] = "An unexpected error occurred. Please try again later.";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Check if there are no errors
    if (empty($errors)) {
        // Prepare SQL statement to retrieve the user
        $stmt = $conn->prepare("SELECT username, password, role FROM signup WHERE email = ?");
        if (!$stmt) {
            error_log("SQL Error: " . $conn->error); // Log error for developers
            $errors[] = "An unexpected error occurred. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Check if the user exists
            if ($stmt->num_rows == 1) {
                // Bind the result to variables
                $stmt->bind_result($username, $hashed_password, $role);
                $stmt->fetch();

                // Verify the password
                if (password_verify($password, $hashed_password)) {
                    // Set session variables
                    $_SESSION['email'] = $email;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role; // Store the role in session

                    // Redirect based on role (Admin or User)
                    if ($role == 'admin') {
                        header("Location: admin_home.php"); // Admin dashboard
                    } else {
                        header("Location: home.php?username=" . urlencode($username)); // Regular user dashboard
                    }
                    exit;
                } else {
                    $errors[] = "Invalid password.";
                }
            } else {
                $errors[] = "No account found with that email.";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TurfEase - Welcome</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <script>
        function validateForm() {
            const username = document.forms["signupForm"]["username"].value;
            const email = document.forms["signupForm"]["email"].value;
            const password = document.forms["signupForm"]["password"].value;
            const confirmPassword = document.forms["signupForm"]["confirm_password"].value;
            const errorList = document.getElementById("error-list");
            errorList.innerHTML = ""; // Clear previous errors

            let errors = [];

            if (!username) {
                errors.push("Username is required.");
            }
            if (!email) {
                errors.push("Email is required.");
            } else if (!validateEmail(email)) {
                errors.push("Invalid email format.");
            }
            if (!password) {
                errors.push("Password is required.");
            } else if (password.length < 8) { // Minimum length check
                errors.push("Password must be at least 8 characters long.");
            }
            if (password !== confirmPassword) {
                errors.push("Passwords do not match.");
            }

            if (errors.length > 0) {
                errors.forEach(error => {
                    const li = document.createElement("li");
                    li.textContent = error;
                    li.style.color = "red"; // Ensure the error message is red
                    li.style.display = "block"; // Ensure the error message is displayed
                    errorList.appendChild(li);
                });
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }

        let currentPopup = null;
        function showpopup(popupId) {
            if (currentPopup) {
                currentPopup.classList.remove('show');
                document.getElementById('background-blur').classList.remove('show');
                setTimeout(() => {
                    document.getElementById(popupId).classList.add('show');
                    document.getElementById('background-blur').classList.add('show');
                    currentPopup = document.getElementById(popupId);
                }, 300); // transition duration
            } else {
                if (popupId) {
                    document.getElementById(popupId).classList.add('show');
                    document.getElementById('background-blur').classList.add('show');
                    currentPopup = document.getElementById(popupId);
                } else {
                    // Close popup
                    currentPopup.classList.remove('show');
                    document.getElementById('background-blur').classList.remove('show');
                    currentPopup = null;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Change the height of the second form
            const popup2 = document.getElementById('popup-2');
            if (popup2) {
                popup2.style.height = '470px';
            }
        });
    </script>
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" class="logo" alt="TurfEase Logo">
    </div>
    <div class="content">
        <h1>Welcome to the ultimate playground for sports enthusiasts!</h1>
        <p>Dive into a world where every game is played on the best turf, and your passion for sports can thrive. Let
            the games begin!</p>
        <button type="button"><a onclick="showpopup('popup-1')">login/signup</a></button>
    </div>
    <div id="background-blur" class="background-blur"></div>
    <div class="popup" id="popup-1">
        <div class="close-btn" onclick="showpopup()">&times;</div>
        <img src="assets/img/profile2.png" alt="Profile">
        <h1>LOGIN</h1>
        <ul id="login-error-list" class="error-list"></ul>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="text" name="email" class="input-box" placeholder="your email" required>
            <input type="password" name="password" class="input-box" placeholder="your password" required><br>
            <button type="submit" name="login" class="loginbtn">LOGIN</button>
            <p>Don't have an account?</p>
            <a onclick="showpopup('popup-2')">sign up</a><br>
        </form>
    </div>
    <div class="popup" id="popup-2">
        <div class="overlay"></div>
        <div class="close-btn" onclick="showpopup()">&times;</div>
        <img src="assets/img/profile2.png" alt="Profile">
        <h1>SIGNUP</h1>
        <ul id="error-list" class="error-list"></ul> <!-- Error messages will be displayed here -->
        <form name="signupForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post"
            onsubmit="return validateForm()">
            <input type="text" name="username" class="input-box" placeholder="your name" required>
            <input type="text" name="email" class="input-box" placeholder="your email" required>
            <input type="password" name="password" class="input-box" placeholder="your password" required>
            <input type="password" name="confirm_password" class="input-box" placeholder="confirm your password"
                required>
            <button type="submit" name="signup" class="loginbtn">Signup</button>
        </form>
    </div>
    <script src="assets/js/index.js"></script>
</body>

</html>