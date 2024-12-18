$(document).ready(function() {
    const reservationForm = $('#reservationForm');

    if (reservationForm.length === 0) {
        return;
    }

    const restaurantSelect = $('#restaurant');
    const tableSelect = $('#table');
    const timeslotSelect = $('#timeslot');
    const reserveButton = reservationForm.find('button[type="submit"]');

    // Fetch available tables when a restaurant is selected
    restaurantSelect.on('change', function() {
        const restaurantId = $(this).val();
        tableSelect.empty().append('<option value="">Select a table</option>').prop('disabled', true);
        timeslotSelect.empty().append('<option value="">Select a table first</option>').prop('disabled', true);
        reserveButton.prop('disabled', true);

        if (restaurantId) {
            $.ajax({
                url: 'php/get_tables.php',
                method: 'GET',
                data: { restaurant_id: restaurantId },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        tableSelect.append('<option value="">No available tables</option>');
                    } else {
                        $.each(data, function(index, table) {
                            tableSelect.append(`<option value="${table.id}">Table ${table.id} (Seats: ${table.capacity})</option>`);
                        });
                        tableSelect.prop('disabled', false);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching tables:', textStatus, errorThrown);
                    showMessage('Failed to load tables. Please try again.', 'error');
                }
            });
        }
    });

    // Fetch available time slots when a table is selected
    tableSelect.on('change', function() {
        const tableId = $(this).val();
        timeslotSelect.empty().append('<option value="">Select a time slot</option>').prop('disabled', true);
        reserveButton.prop('disabled', true);

        if (tableId) {
            $.ajax({
                url: 'php/get_time_slots.php',
                method: 'GET',
                data: { table_id: tableId },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        timeslotSelect.append('<option value="">No available time slots</option>');
                    } else {
                        $.each(data, function(index, slot) {
                            const slotDateTime = new Date(slot.slot_datetime);
                            const formattedDateTime = slotDateTime.toLocaleString();
                            timeslotSelect.append(`<option value="${slot.id}">${formattedDateTime}</option>`);
                        });
                        timeslotSelect.prop('disabled', false);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching time slots:', textStatus, errorThrown);
                    showMessage('Failed to load time slots. Please try again.', 'error');
                }
            });
        }
    });

    // Enable reserve button when a time slot is selected
    timeslotSelect.on('change', function() {
        reserveButton.prop('disabled', !$(this).val());
    });

    // Handle form submission
    reservationForm.on('submit', function(event) {
        event.preventDefault();
        reserveButton.prop('disabled', true).text('Reserving...');
        const formData = $(this).serialize();

        $.ajax({
            url: 'php/book_table.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(data) {
                reserveButton.prop('disabled', false).text('Reserve');
                if (data.success) {
                    showMessage(`Reservation successful! Your confirmation number is: ${data.confirmation_number}`, 'success');
                    // Remove the reserved time slot from the dropdown
                    timeslotSelect.find(`option[value="${data.timeslot_id}"]`).remove();
                    // Reset the form
                    reservationForm[0].reset();
                    tableSelect.prop('disabled', true);
                    timeslotSelect.prop('disabled', true);
                    reserveButton.prop('disabled', true);
                } else {
                    showMessage(data.message, 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                reserveButton.prop('disabled', false).text('Reserve');
                console.error('Error:', textStatus, errorThrown);
                showMessage('An unexpected error occurred. Please try again.', 'error');
            }
        });
    });

    // Function to display messages within the page
    function showMessage(message, type) {
        const main = $('main');
        main.find('.message').remove();

        const messagePara = $('<p></p>').addClass(`message ${type}`).text(message);
        main.prepend(messagePara);

        setTimeout(() => {
            messagePara.fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
});