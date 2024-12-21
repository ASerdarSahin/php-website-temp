<?php

session_start();
header('Content-Type: application/json');

// check user login with json response
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include('connection.php');

try {
    // Fetch available tables
    $sql = "SELECT id, capacity FROM tables WHERE status = 'available'";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception('Failed to fetch tables.');
    }

    $tables = [];
    // Fetch the results into an array
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }

    echo json_encode($tables); // Return the tables as JSON
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally { // Close the connection
    $conn->close();
}
?>