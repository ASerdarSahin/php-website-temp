<?php
// Visit once to generate time slots

include('connection.php');

$start_date = '2025-01-01'; // Start date
$end_date = '2025-01-01';   // End date
$opening_time = '18:00';
$closing_time = '22:00';
$slot_duration = 1; // Slot duration in hours

// Fetch all tables
$tables_result = $conn->query("SELECT id FROM tables");
$tables = $tables_result->fetch_all(MYSQLI_ASSOC);

// Generate time slots
$interval = new DateInterval('PT' . ($slot_duration * 60) . 'M');
$period = new DatePeriod(
    new DateTime($start_date),
    new DateInterval('P1D'),
    (new DateTime($end_date))->modify('+1 day')
);

// Loop through each date and table to generate time slots
foreach ($period as $date) {
    foreach ($tables as $table) {
        $current_time = new DateTime($date->format('Y-m-d') . ' ' . $opening_time);
        $end_time = new DateTime($date->format('Y-m-d') . ' ' . $closing_time);
        while ($current_time < $end_time) {
            $slot_datetime = $current_time->format('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO time_slots (table_id, slot_datetime) VALUES (?, ?)");
            $stmt->bind_param('is', $table['id'], $slot_datetime);
            $stmt->execute();
            $current_time->add($interval);
        }
    }
}

// After generating time slots, update table statuses
$updateTableStatusSql = "UPDATE tables t
    SET t.status = 'available'
    WHERE t.id IN (
        SELECT DISTINCT ts.table_id FROM time_slots ts WHERE ts.status = 'available'
    )";
$conn->query($updateTableStatusSql);

echo "Time slots generated successfully.";

$conn->close();
?>