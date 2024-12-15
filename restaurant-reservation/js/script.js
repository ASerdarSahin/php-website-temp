document.addEventListener('DOMContentLoaded', function() {
    const reservationForm = document.getElementById('reservationForm');

    if (!reservationForm) {
        // Reservation form not present; do not execute reservation-related scripts
        return;
    }

    const tableSelect = document.getElementById('table');
    const timeslotSelect = document.getElementById('timeslot');
    const reserveButton = document.querySelector('#reservationForm button[type="submit"]');

    // Fetch available tables
    fetch('php/get_tables.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(table => {
                const option = document.createElement('option');
                option.value = table.id;
                option.textContent = `Table ${table.id} (Seats: ${table.capacity})`;
                tableSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching tables:', error);
            // Display error message within the page instead of an alert
            showMessage('Failed to load tables. Please try again.', 'error');
        });

    // Fetch available time slots when a table is selected
    tableSelect.addEventListener('change', function() {
        const tableId = this.value;
        timeslotSelect.innerHTML = '<option value="">Select a time slot</option>';
        timeslotSelect.disabled = true;
        reserveButton.disabled = true;

        if (tableId) {
            fetch(`php/get_time_slots.php?table_id=${tableId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        const option = document.createElement('option');
                        option.value = "";
                        option.textContent = "No available time slots";
                        timeslotSelect.appendChild(option);
                    } else {
                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.id;
                            const slotDateTime = new Date(slot.slot_datetime);
                            option.textContent = slotDateTime.toLocaleString();
                            timeslotSelect.appendChild(option);
                        });
                        timeslotSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error fetching time slots:', error);
                    // Display error message within the page
                    showMessage('Failed to load time slots. Please try again.', 'error');
                });
        }
    });

    // Enable reserve button when a time slot is selected
    timeslotSelect.addEventListener('change', function() {
        reserveButton.disabled = !this.value;
    });

    // Handle form submission
    reservationForm.addEventListener('submit', function(event) {
        event.preventDefault();
        reserveButton.disabled = true;
        reserveButton.textContent = 'Reserving...';
        const formData = new FormData(this);
        fetch('php/book_table.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            reserveButton.disabled = false;
            reserveButton.textContent = 'Reserve';
            if (data.success) {
                // Display success message within the page
                showMessage(`Reservation successful! Your confirmation number is: ${data.confirmation_number}`, 'success');
                
                // Optionally, remove the reserved time slot from the dropdown
                const selectedOption = timeslotSelect.querySelector(`option[value="${formData.get('timeslot_id')}"]`);
                if (selectedOption) {
                    selectedOption.remove();
                }
                // Reset the form
                this.reset();
                timeslotSelect.disabled = true;
                reserveButton.disabled = true;
            } else {
                // Display error message within the page
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            reserveButton.disabled = false;
            reserveButton.textContent = 'Reserve';
            console.error('Error:', error);
            showMessage('An unexpected error occurred. Please try again.', 'error');
        });
    });

    // Function to display messages within the page
    function showMessage(message, type) {
        const main = document.querySelector('main');
        
        // Remove existing messages
        const existingMessages = main.querySelectorAll('.message');
        existingMessages.forEach(msg => msg.remove());

        const messagePara = document.createElement('p');
        messagePara.classList.add('message', type);
        messagePara.textContent = message;
        main.prepend(messagePara);

        // Automatically remove the message after 5 seconds
        setTimeout(() => {
            messagePara.remove();
        }, 5000);
    }
});