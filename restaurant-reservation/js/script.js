document.addEventListener('DOMContentLoaded', function() {
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
                    data.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.id;
                        const slotDateTime = new Date(slot.slot_datetime);
                        option.textContent = slotDateTime.toLocaleString();
                        timeslotSelect.appendChild(option);
                    });
                    timeslotSelect.disabled = false;
                });
        }
    });

    // Enable reserve button when a time slot is selected
    timeslotSelect.addEventListener('change', function() {
        reserveButton.disabled = !this.value;
    });

    // Handle form submission
    document.getElementById('reservationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('php/book_table.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    });
});