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
<!-- HTML Form (Booking Page) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #151515;
            color: whitesmoke;
            font-family: Segoe UI;
            font-weight: 700;
        }

        .logo {
            width: 200px;
            height: 150px;
            margin-left: 20px;
            margin-top: 5px;

        }

        .header {
            position: fixed;
            background: #151515;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            top: 0;
            height: 15%;
            width: 100%;
            display: flex;
            align-items: center;

        }

        .header .user-icon {
            width: 70px;
            height: 70px;
            margin-left: 900px;
            margin-top: -10px;
        }

        .header .user {
            margin-left: 0px;
            margin-top: -10px;
        }

        .bookform {
            margin-top: 10%;
            margin-left: 30%;
            border-radius: 5px;
            width: 450px;
            height: 850px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            background: #151515;
            padding: 20px;
            text-align: center;

        }

        .input-box {
            border-radius: 5px;
            padding: 10px;
            margin: 10px 10px;
            width: 70%;
            border: 1px solid #999;
            font-family: 'Segoe UI';
        }

        label {
            margin: 10px 10px;
            width: 200px;
        }

        .input {
            border-style: none;
            color: antiquewhite;
            text-align: center;
            background-color: black;
            padding: 10px;
            margin: 10px 10px;
            width: 37%;
            font-family: Segoe UI;
            font-weight: 500;

        }

        .time-slot-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .time-slot {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            width: 180px;
            height: 20px;
            background-color: darkcyan;
            /* Green for default */
            border-style: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            user-select: none;
            transition: background-color 0.3s ease;
        }

        .button {
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            padding: 10px;
            background: darkcyan;
            color: antiquewhite;
            cursor: pointer;
        }

        .bookform .confirm {
            border-radius: 5px;
            width: 200px;
            margin-top: 10px;
            height: 30px;
            background: darkcyan;
            color: white;
            font-weight: 1000;
        }

        /* Disable booked time slots visually */
        .time-slot.disabled {
            background-color: #f0f0f0;
            color: #ccc;
            cursor: not-allowed;
        }

        .time-slot {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .time-slot.available {
            background-color: darkcyan;
            /* Light green */
            color: white;
            /* Dark green text */
            border-color: #c3e6cb;
        }

        .time-slot.booked {
            background-color: #f8d7da;
            /* Light red */
            color: #721c24;
            /* Dark red text */
            border-color: #f5c6cb;
            pointer-events: none;
            /* Disable clicking */
        }

        .time-slot.selected {
            background-color: #cce5ff;
            /* Light blue for selected slots */
            color: darkcyan;
            border-color: #b8daff;
        }

        .time-slot.hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/img/turfease logo.png" class="logo" alt="TurfEase Logo">
        <img src="assets/img/profile2.png" alt="User Icon" class="user-icon">
        <div class="user">
            <h3><?php echo htmlspecialchars($userName); ?></h3>
        </div>
    </div>

    <div class="bookform">
        <form action="book.php" method="POST">
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

            <button type="submit" class="confirm">CONFIRM</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const timeSlots = document.querySelectorAll(".time-slot");
            const selectedTimeInput = document.getElementById("selectedTime");
            const appointmentDateInput = document.getElementById("appointment");

            // Get today's date and current time
            const now = new Date();
            const todayDate = now.toISOString().split("T")[0]; // Format as YYYY-MM-DD
            const currentHour = now.getHours();
            const currentMinutes = now.getMinutes();

            // Helper function to parse time string in "6:30 AM - 7:30 AM" format
            function parseTime(timeText) {
                const [time, period] = timeText.split(" ");
                let [hours, minutes] = time.split(":").map(Number);

                // Convert to 24-hour format
                if (period === "PM" && hours < 12) {
                    hours += 12;
                }
                if (period === "AM" && hours === 12) {
                    hours = 0;
                }

                return { hours, minutes };
            }

            // Filter time slots based on the selected date
            function filterTimeSlots() {
                const selectedDate = appointmentDateInput.value;

                timeSlots.forEach(slot => {
                    const { hours, minutes } = parseTime(slot.dataset.time.split(" - ")[0]);

                    if (selectedDate === todayDate) {
                        // Hide past slots for today
                        if (hours < currentHour || (hours === currentHour && minutes <= currentMinutes)) {
                            slot.style.display = "none";
                        } else {
                            slot.style.display = "inline-block";
                        }
                    } else {
                        // Show all slots for other dates
                        slot.style.display = "inline-block";
                    }
                });
            }

            // Add event listener to update time slots when the date changes
            appointmentDateInput.addEventListener("change", filterTimeSlots);

            // Initialize the date input with today's date and filter slots
            appointmentDateInput.min = todayDate; // Prevent selection of past dates
            appointmentDateInput.value = todayDate; // Default to today
            filterTimeSlots(); // Initial filter for today's date

            // Add click event for selecting a time slot
            timeSlots.forEach(slot => {
                slot.addEventListener("click", function () {
                    if (this.style.display === "none") return; // Skip hidden slots

                    // Toggle the 'selected' class for the clicked slot
                    this.classList.toggle("selected");

                    // Update the hidden input with the selected times
                    const selectedTimes = Array.from(document.querySelectorAll(".time-slot.selected"))
                        .map(slot => slot.getAttribute("data-time"));

                    selectedTimeInput.value = selectedTimes.join(", ");
                });
            });
        });
    </script>

</body>

</html>