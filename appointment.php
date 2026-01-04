<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//require 'vendor/autoload.php';

// Database configuration
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$employee_db = "turfdb"; // Employee database name
$appointment_db = "signup"; // Salon Booking database name

// Establish connection to Employee DB
$conn_emp = new mysqli($servername, $username, $password, $employee_db);

// Check Employee DB connection
if ($conn_emp->connect_error) {
    die("Connection failed to Employee DB: " . $conn_emp->connect_error);
}

// Fetch employees from Employee DB
$employees = [];
$sql = "SELECT id,name FROM employees";
$result = $conn_emp->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
} else {
    echo "Error fetching employees: " . $conn_emp->error;
}
$conn_emp->close();

// Establish connection to Salon Booking DB
$conn = new mysqli($servername, $username, $password, $appointment_db);

// Check Salon Booking DB connection
if ($conn->connect_error) {
    die("Connection failed to Salon Booking DB: " . $conn->connect_error);
}

// Initialize booking status variables
$bookingSuccess = false;
$appointmentNumber = null;

// Initialize error message variable
$errorMessage = '';

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $clientName = trim($_POST['client_name']);
    $phoneNumber = trim($_POST['phone_number']);
    $email = strtolower(trim($_POST['email'])); // Convert email to lowercase
    $preferredDate = $_POST['preferred_date'];
    $selectedSlot = $_POST['time_slot'];
    $services = isset($_POST['service']) ? implode(', ', $_POST['service']) : ''; // Convert array to string
    $employeeId = $_POST['employee'];

    // Validate phone number (must be exactly 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phoneNumber)) {
        $errorMessage = "Phone number must be exactly 10 digits";
    }
    // Validate email format (must be lowercase gmail.com)
    else if (!preg_match('/^[a-z0-9._%+-]+@gmail\.com$/', $email)) {
        $errorMessage = "Please enter a valid Gmail address in lowercase (example@gmail.com)";
    }
    // Validate all fields are filled
    else if (empty($clientName) || empty($phoneNumber) || empty($email) || 
             empty($preferredDate) || empty($selectedSlot) || empty($services) || 
             empty($employeeId)) {
        $errorMessage = "All fields are required.";
    }

    // If no validation errors, proceed with booking
    if (empty($errorMessage)) {
        // Reconnect to Employee DB to fetch employee name
        $conn_emp = new mysqli($servername, $username, $password, $employee_db);
        if ($conn_emp->connect_error) {
            die("Connection failed to Employee DB: " . $conn_emp->connect_error);
        }

        // Fetch employee name based on employee ID
        $employeeName = '';
        $emp_stmt = $conn_emp->prepare("SELECT name FROM employees WHERE id = ?");
        if ($emp_stmt === false) {
            die("SQL Error: " . $conn_emp->error);
        }
        $emp_stmt->bind_param("s", $employeeId);
        $emp_stmt->execute();
        $emp_result = $emp_stmt->get_result();

        if ($emp_result->num_rows > 0) {
            $emp_row = $emp_result->fetch_assoc();
            $employeeName = $emp_row['name'];
        } else {
            die("Selected employee does not exist.");
        }
        $emp_stmt->close();
        $conn_emp->close();

        // Function to generate a unique appointment number
        function generateUniqueAppointmentNumber($conn)
        {
            do {
                $count = 0;
                // Generate a random 6-digit number as a string
                $appointmentNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                // Check if it already exists in the booked_slots table
                $checkStmt = $conn->prepare("SELECT COUNT(*) FROM booked_slots WHERE appointment_number = ?");
                $checkStmt->bind_param("s", $appointmentNumber);
                $checkStmt->execute();
                $checkStmt->bind_result($count);
                $checkStmt->fetch();
                $checkStmt->close();
            } while ($count > 0); // Repeat until a unique number is found

            return $appointmentNumber;
        }

        // Prepare SQL statement to insert booking with appointment number
        $stmt = $conn->prepare("INSERT INTO booked_slots (client_name, phone_number, email, preferred_date, slot_time, service, employee_name, appointment_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die("SQL Error: " . $conn->error);
        }

        // Generate a unique appointment number
        $appointmentNumber = generateUniqueAppointmentNumber($conn);

        $stmt->bind_param("ssssssss", $clientName, $phoneNumber, $email, $preferredDate, $selectedSlot, $services, $employeeName, $appointmentNumber);

        if ($stmt->execute() === TRUE) {
            $bookingSuccess = true;
            // Store the generated appointment number for use in the success message
        } else {
            die("Error: " . $stmt->error);
        }

        $stmt->close();
    }
}



// Fetch all booked slots for the selected employee and date
$bookedSlots = [];
if (isset($_GET['preferred_date']) && isset($_GET['employee'])) {
    $date = $_GET['preferred_date'];
    $employeeId = $_GET['employee'];

    // Reconnect to Employee DB to get employee name
    $conn_emp = new mysqli($servername, $username, $password, $employee_db);
    if ($conn_emp->connect_error) {
        die("Connection failed to Employee DB: " . $conn_emp->connect_error);
    }

    // Get employee name
    $employeeName = '';
    $emp_stmt = $conn_emp->prepare("SELECT name FROM employees WHERE id = ?");
    if ($emp_stmt === false) {
        die("SQL Error: " . $conn_emp->error);
    }
    $emp_stmt->bind_param("s", $employeeId);
    $emp_stmt->execute();
    $emp_result = $emp_stmt->get_result();

    if ($emp_result->num_rows > 0) {
        $emp_row = $emp_result->fetch_assoc();
        $employeeName = $emp_row['name'];
    }
    $emp_stmt->close();
    $conn_emp->close();

    if ($employeeName != '') {
        // Prepare SQL statement to fetch booked slots
        $stmt = $conn->prepare("SELECT slot_time FROM booked_slots WHERE preferred_date = ? AND employee_name = ?");

        if ($stmt === false) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("ss", $date, $employeeName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookedSlots[] = $row['slot_time'];
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salon Booking System</title>
    <link rel="stylesheet" href="styles.css">

    <!-- SweetAlert2 for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Basic styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #343a40;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #343a40;
            margin-bottom: 20px;
            font-size: 40px;
            font-weight: bold;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            font-size: 16px;
            color: #495057;
        }

        input,
        select {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            border: none;
            color: #ffffff;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .time-slot {
            display: inline-block;
            margin: 5px;
            padding: 10px;
            background-color: #f1f1f1;
            border: 2px solid #ced4da;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            width: 100px;
            user-select: none;
        }

        .time-slot.booked {
            background-color: red;
            color: #ffffff;
            cursor: not-allowed;
        }

        .time-slot.selected {
            background-color: #4CAF50;
            color: white;
        }

        .time-slots h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #343a40;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Book an Appointment</h1>

        <!-- Form to select employee and date -->
        <form method="get" action="">
            <label for="employee">Select Employee:</label>
            <select id="employee" name="employee" required>
                <option value="">Select Employee</option>
                <?php foreach ($employees as $emp) { ?>
                    <option value="<?php echo htmlspecialchars($emp['id']); ?>" <?php echo (isset($_GET['employee']) && $_GET['employee'] == $emp['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($emp['name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label for="preferred_date">Preferred Date:</label>
            <input type="date" id="preferred_date" name="preferred_date"
                value="<?php echo isset($_GET['preferred_date']) ? htmlspecialchars($_GET['preferred_date']) : ''; ?>"
                required min="<?php echo date('Y-m-d'); ?>">
            <button type="submit">Check Availability</button>
        </form>

        <?php if (isset($_GET['employee']) && isset($_GET['preferred_date'])) { ?>
            <!-- Booking form -->
            <form method="post" action="">
                <label for="client_name">Name:</label>
                <input type="text" id="client_name" name="client_name" required>

                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <!-- Hidden fields to retain selected date and employee -->
                <input type="hidden" name="preferred_date" value="<?php echo htmlspecialchars($_GET['preferred_date']); ?>">
                <input type="hidden" name="employee" value="<?php echo htmlspecialchars($_GET['employee']); ?>">

                <label>Services:</label>
                <div class="services-checkbox" style="margin-bottom: 15px;">
                    <label style="display: inline-block; margin-right: 15px;">
                        <input type="checkbox" name="service[]" value="Haircut"> Haircut
                    </label>
                    <label style="display: inline-block; margin-right: 15px;">
                        <input type="checkbox" name="service[]" value="Manicure"> Manicure
                    </label>
                    <label style="display: inline-block; margin-right: 15px;">
                        <input type="checkbox" name="service[]" value="Pedicure"> Pedicure
                    </label>
                    <label style="display: inline-block; margin-right: 15px;">
                        <input type="checkbox" name="service[]" value="Facial"> Facial
                    </label>
                    <label style="display: inline-block; margin-right: 15px;">
                        <input type="checkbox" name="service[]" value="Massage"> Massage
                    </label>
                </div>

                <div class="time-slots">
                    <h3>Select a Time Slot:</h3>
                    <?php
                    $startTime = strtotime("9:00 AM");
                    $endTime = strtotime("9:00 PM");

                    while ($startTime < $endTime) {
                        $slotStart = date("h:i A", $startTime);
                        $slotEnd = date("h:i A", strtotime('+60 minutes', $startTime));

                        // Skip the 1-2 PM break
                        if ($slotStart >= "01:00 PM" && $slotStart < "02:00 PM") {
                            $startTime = strtotime('+60 minutes', $startTime);
                            continue;
                        }

                        // Format the slot display
                        $slotDisplay = date("g:i", $startTime) . " - " . date("g:i A", strtotime('+60 minutes', $startTime));
                        
                        // Determine if the slot is already booked
                        $slotClass = in_array($slotStart, $bookedSlots) ? 'booked' : '';
                        echo "<div class='time-slot $slotClass' data-slot='" . htmlspecialchars($slotStart) . "'>" . htmlspecialchars($slotDisplay) . "</div>";

                        $startTime = strtotime('+60 minutes', $startTime);
                    }
                    ?>
                </div>

                <input type="hidden" name="time_slot" id="selectedSlot" required>
                <button type="submit" id="confirmButton" disabled>Confirm Booking</button>
            </form>
        <?php } ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const timeSlots = document.querySelectorAll(".time-slot");
            const selectedSlotInput = document.getElementById("selectedSlot");
            const confirmButton = document.getElementById("confirmButton");

            timeSlots.forEach(slot => {
                if (!slot.classList.contains("booked")) {
                    slot.addEventListener("click", function () {
                        // Remove 'selected' class from all slots
                        timeSlots.forEach(s => s.classList.remove("selected"));
                        // Add 'selected' class to the clicked slot
                        slot.classList.add("selected");
                        // Set the hidden input value
                        selectedSlotInput.value = slot.getAttribute("data-slot");
                        // Enable the confirm button
                        confirmButton.disabled = false;
                    });
                }
            });

            <?php if ($bookingSuccess) { ?>
                Swal.fire({
                    title: 'Booking Confirmed!',
                    text: 'Your appointment number is <?php echo htmlspecialchars($appointmentNumber); ?>.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to send email
                        $.ajax({
                            url: 'send_email.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                appointmentNumber: '<?php echo $appointmentNumber; ?>'
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    console.log(response.message); // Email sent successfully
                                } else {
                                    console.log(response.message); // Error message
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("An error occurred: " + error);
                            }
                        });
                    }
                });
            <?php } ?>

            <?php if ($errorMessage) { ?>
                Swal.fire({
                    title: 'Validation Error',
                    text: '<?php echo $errorMessage; ?>',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            <?php } ?>

            // Add client-side validation
            const form = document.querySelector('form[method="post"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const phone = document.getElementById('phone_number').value;
                    const email = document.getElementById('email').value;
                    const services = document.querySelectorAll('input[name="service[]"]:checked');
                    
                    // Phone validation
                    if (!/^[0-9]{10}$/.test(phone)) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Phone number must be exactly 10 digits',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Email validation
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please enter a valid email address',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Services validation
                    if (services.length === 0) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please select at least one service',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }
                });
            }
        });


    </script>
</body>

</html>