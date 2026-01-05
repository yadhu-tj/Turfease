document.addEventListener("DOMContentLoaded", function () {
    const sportSelect = document.getElementById('sport');
    const courtSelect = document.getElementById('court');
    const appointmentInput = document.getElementById('appointment');
    const selectedTimeInput = document.getElementById('selectedTime');
    const timeSlots = document.querySelectorAll('.time-slot');

    // Update available courts based on sport selection
    function updateCourtOptions() {
        const sport = sportSelect.value;
        courtSelect.innerHTML = '<option value=""></option>'; // Clear existing options

        if (sport === 'football' || sport === 'Volleyball' || sport === 'cricket') {
            courtSelect.innerHTML += '<option value="in 5">Indoor 5x5</option>';
            courtSelect.innerHTML += '<option value="out 5">Outdoor 5x5</option>';
        } else if (sport === 'b volley' || sport === 'b football') {
            courtSelect.innerHTML += '<option value="sand">Sand court</option>';
        }
    }

    // Listen for changes in sport selection
    sportSelect.addEventListener('change', updateCourtOptions);
    updateCourtOptions();

    // Set minimum date for appointment input
    const today = new Date();
    appointmentInput.setAttribute('min', today.toISOString().split('T')[0]);

    // Fetch booked slots and update time slots
    function fetchBookedSlots() {
        const selectedDate = appointmentInput.value;
        const selectedCourt = courtSelect.value;

        if (selectedDate && selectedCourt) {
            fetch(`get_booked_slots.php?appointment_date=${selectedDate}&court=${selectedCourt}`)
            .then(response => response.json())
            .then(bookedSlots => {
                console.log(bookedSlots); // Log the fetched booked slots
        
                // Reset slots
                timeSlots.forEach(slot => {
                    slot.classList.remove('disabled', 'booked', 'available', 'selected');
                    slot.classList.add('available');
                    slot.style.pointerEvents = 'auto';
                });
        
                // Mark booked slots
                timeSlots.forEach(slot => {
                    const time = slot.getAttribute('data-time');
                    if (bookedSlots.includes(time)) {
                        slot.classList.remove('available');
                        slot.classList.add('booked');
                        slot.style.pointerEvents = 'none';
                    }
                });
        
                // Disable past time slots
                disablePastTimeSlots();
            })
            .catch(error => console.error("Error fetching booked slots:", error));
           }
    }

    // Disable past time slots
    function disablePastTimeSlots() {
        const selectedDate = appointmentInput.value;
        if (!selectedDate) return;

        const today = new Date();
        const selectedDateObj = new Date(selectedDate);
        const isToday = today.toDateString() === selectedDateObj.toDateString();

        timeSlots.forEach(slot => {
            const timeRange = slot.getAttribute('data-time');
            const [startTime] = timeRange.split(' - ');
            const slotDateTime = new Date(`${selectedDate} ${startTime}`);

            if (isToday && slotDateTime < today) {
                slot.classList.add('disabled');
                slot.style.pointerEvents = 'none';
            }
        });
    }

    // Add event listeners for time slot selection
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function () {
            if (this.classList.contains('available')) {
                this.classList.toggle('selected');
                const selectedTimes = Array.from(document.querySelectorAll('.time-slot.selected'))
                    .map(slot => slot.getAttribute('data-time'));
                selectedTimeInput.value = selectedTimes.join(", ");
            }
        });
    });

    // Event listeners for inputs
    appointmentInput.addEventListener('input', fetchBookedSlots);
    courtSelect.addEventListener('change', fetchBookedSlots);

    // Fetch initial slots if inputs are prefilled
    if (appointmentInput.value && courtSelect.value) {
        fetchBookedSlots();
    }
});
