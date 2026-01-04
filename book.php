<?php
// Start the session
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = []; // Initialize errors
    // Check if the user is logged in and email is set in the session
    if (!isset($_SESSION['email'])) {
        // Redirect to login page if email is not set in session
        header("Location: index.php");
        exit;
    }

    // Collect form data
    $sport = isset($_POST['sport']) ? trim($_POST['sport']) : '';
    $court = isset($_POST['court']) ? trim($_POST['court']) : '';
    $appointment_date = isset($_POST['appointment']) ? $_POST['appointment'] : '';
    $slot_tym = isset($_POST['time']) ? $_POST['time'] : ''; // Selected time slots
    $email = $_SESSION['email']; // Get the logged-in user's email from session

    // Validate inputs
    if (empty($sport))
        $errors[] = "Sport is required.";
    if (empty($court))
        $errors[] = "Court is required.";
    if (empty($appointment_date))
        $errors[] = "Appointment date is required.";
    if (empty($slot_tym))
        $errors[] = "At least one time slot is required.";

    // Date and time validation
    date_default_timezone_set('Asia/Kolkata');
    $currentDateTime = new DateTime();
    $selectedDate = new DateTime($appointment_date);

    if ($selectedDate < $currentDateTime->setTime(0, 0)) {
        $errors[] = "You cannot book for past dates.";
    } else {
        $timeSlots = explode(", ", $slot_tym);
        foreach ($timeSlots as $slot) {
            preg_match('/(\d{1,2}:\d{2} [APM]+)/', $slot, $matches);
            if ($matches) {
                $slotStartTime = $matches[1];
                $slotDateTime = new DateTime($appointment_date . ' ' . $slotStartTime);
                if ($slotDateTime <= $currentDateTime) {
                    $errors[] = "The time slot $slot cannot be booked as the current time has passed.";
                }
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: book.php");
        exit;
    }

    // Calculate the amount
    $timeSlots = explode(", ", $slot_tym);
    $amount = 0;

    foreach ($timeSlots as $slot) {
        preg_match('/(\d{1,2}:\d{2}) ([APM]+)/', $slot, $matches);
        if ($matches) {
            $time = $matches[1];
            $period = $matches[2];
            $hourMinute = DateTime::createFromFormat('h:i A', $time . ' ' . $period);

            // Check the court type and calculate the amount
            if ($court == 'sand') {
                $amount += 600; // Fixed rate for sand court
            } else if ($hourMinute < DateTime::createFromFormat('h:i A', '6:30 PM')) {
                if ($court == 'in 5') {
                    $amount += 700;
                } elseif ($court == 'out 5') {
                    $amount += 600;
                }
            } else {
                if ($court == 'in 5') {
                    $amount += 1100;
                } elseif ($court == 'out 5') {
                    $amount += 900;
                }
            }
        }
    }

    // Store booking details in session along with the email and amount
    $_SESSION['booking_info'] = [
        'sport' => $sport,
        'court' => $court,
        'appointment_date' => $appointment_date,
        'slot_tym' => $slot_tym,
        'email' => $email,
        'amount' => $amount, // Add the calculated amount
    ];

    header("Location: payment.php");
    exit;
}
?>


<!-- HTML Form (Booking Page) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <link rel="stylesheet" href="assets/css/book.css">
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" class="logo">
    </div>

    <div class="bookform">
        <form action="book.php" method="POST">
            <label for="sport">Choose a sport</label>
            <select id="sport" class="input-box" name="sport">
                <option value=""></option>
                <option value="football">Football</option>
                <option value="Volleyball">Volleyball</option>
                <option value="cricket">Cricket</option>
                <option value="b volley">Beach Volleyball</option>
                <option value="b football">Beach Football</option>
            </select><br>

            <label for="court">Choose a court</label>
            <select id="court" class="input-box" name="court">
                <option value=""></option>
                <option value="in 5">Indoor 5x5</option>
                <option value="out 5">Outdoor 5x5</option>
                <option value="sand">Sand Court</option>
            </select><br>

            <label for="appointment">Select a date</label><br>
            <input class="input-box" type="date" id="appointment" name="appointment" min=""><br><br>

            <label>Available Time Slots</label><br><br>
            <div class="time-slot-container" id="timeSlotContainer">
                <div class="time-slot" data-time="6:30 AM - 7:30 AM">6:30 AM - 7:30 AM</div>
                <div class="time-slot" data-time="7:30 AM - 8:30 AM">7:30 AM - 8:30 AM</div>
                <div class="time-slot" data-time="8:30 AM - 9:30 AM">8:30 AM - 9:30 AM</div>
                <div class="time-slot" data-time="9:30 AM - 10:30 AM">9:30 AM - 10:30 AM</div>
                <div class="time-slot" data-time="10:30 AM - 11:30 AM">10:30 AM - 11:30 AM</div>
                <div class="time-slot" data-time="11:30 AM - 12:30 PM">11:30 AM - 12:30 PM</div>
                <div class="time-slot" data-time="12:30 PM - 1:30 PM">12:30 PM - 1:30 PM</div>
                <div class="time-slot" data-time="2:30 PM - 3:30 PM">2:30 PM - 3:30 PM</div>
                <div class="time-slot" data-time="3:30 PM - 4:30 PM">3:30 PM - 4:30 PM</div>
                <div class="time-slot" data-time="4:30 PM - 5:30 PM">4:30 PM - 5:30 PM</div>
                <div class="time-slot" data-time="5:30 PM - 6:30 PM">5:30 PM - 6:30 PM</div>
                <div class="time-slot" data-time="6:30 PM - 7:30 PM">6:30 PM - 7:30 PM</div>
                <div class="time-slot" data-time="7:30 PM - 8:30 PM">7:30 PM - 8:30 PM</div>
                <div class="time-slot" data-time="8:30 PM - 9:30 PM">8:30 PM - 9:30 PM</div>
                <div class="time-slot" data-time="9:30 PM - 10:30 PM">9:30 PM - 10:30 PM</div>
                <div class="time-slot" data-time="10:30 PM - 11:30 PM">10:30 PM - 11:30 PM</div>
            </div>

            <input type="hidden" id="selectedTime" name="time" required><br>

            <button type="submit" class="confirm">PROCEED TO PAYMENT</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sportSelect = document.getElementById('sport');
            const courtSelect = document.getElementById('court');
            const appointmentInput = document.getElementById('appointment');
            const selectedTimeInput = document.getElementById('selectedTime');
            const timeSlots = document.querySelectorAll('.time-slot');
            function updateCourtOptions() {
                const sport = sportSelect.value;

                // Clear current court options
                courtSelect.innerHTML = '<option value=""></option>';

                // Show court options based on selected sport
                if (sport === 'football' || sport === 'Volleyball' || sport === 'cricket') {
                    courtSelect.innerHTML += '<option value="in 5">Indoor 5x5</option>';
                    courtSelect.innerHTML += '<option value="out 5">Outdoor 5x5</option>';
                } else if (sport === 'b volley' || sport === 'b football') {
                    courtSelect.innerHTML += '<option value="sand">Sand court</option>';
                }
            }

            // Listen for changes in sport selection
            sportSelect.addEventListener('change', updateCourtOptions);

            // Initial court options update on page load (in case the form is prefilled)
            updateCourtOptions();

            // Set the minimum date for the appointment field
            const today = new Date();
            const day = ("0" + today.getDate()).slice(-2);  // Add leading zero if necessary
            const month = ("0" + (today.getMonth() + 1)).slice(-2);  // Add leading zero if necessary
            const year = today.getFullYear();
            const currentDate = year + "-" + month + "-" + day;
            appointmentInput.setAttribute('min', currentDate);
            // Fetch booked slots for the given court and appointment date
            const fetchBookedSlots = () => {
                const selectedDate = appointmentInput.value;
                const selectedCourt = courtSelect.value;

                if (selectedDate && selectedCourt) {
                    fetch(`get_booked_slots.php?appointment_date=${selectedDate}&court=${selectedCourt}`)
                        .then(response => response.json())
                        .then(bookedSlots => {
                            // Reset all slots to 'available'
                            timeSlots.forEach(slot => {
                                slot.classList.remove('disabled', 'booked', 'available', 'selected');
                                slot.classList.add('available'); // Default to available
                                slot.style.pointerEvents = 'auto';
                            });

                            // Mark booked slots
                            timeSlots.forEach(slot => {
                                const time = slot.getAttribute('data-time');
                                if (bookedSlots.includes(time)) {
                                    slot.classList.remove('available');
                                    slot.classList.add('booked');
                                    slot.style.pointerEvents = 'none'; // Disable interaction
                                }
                            });

                            // Disable past time slots
                            disablePastTimeSlots();
                        })
                        .catch(error => console.error("Error fetching booked slots:", error));
                }
            };

            // Disable past time slots
            const disablePastTimeSlots = () => {
                const selectedDate = appointmentInput.value;
                if (!selectedDate) return;

                const today = new Date();
                const selectedDateObj = new Date(selectedDate);
                const isToday = today.toDateString() === selectedDateObj.toDateString();

                timeSlots.forEach(slot => {
                    const timeRange = slot.getAttribute('data-time');
                    const [startTime] = timeRange.split(' - '); // Get the start time (e.g., "6:30 AM")
                    const slotDateTime = new Date(`${selectedDate} ${startTime}`);

                    if (isToday && slotDateTime < today) {
                        slot.classList.add('disabled');
                        slot.style.pointerEvents = 'none'; // Disable interaction
                    }
                });
            };

            // Select time slots
            timeSlots.forEach(slot => {
                slot.addEventListener('click', function () {
                    if (this.classList.contains('available')) {
                        this.classList.toggle('selected');

                        const selectedTimes = [...document.querySelectorAll('.time-slot.selected')]
                            .map(slot => slot.getAttribute('data-time'));

                        selectedTimeInput.value = selectedTimes.join(", ");
                    }
                });
            });

            appointmentInput.addEventListener('input', fetchBookedSlots);
            courtSelect.addEventListener('change', fetchBookedSlots);
        });
    </script>
</body>

</html>