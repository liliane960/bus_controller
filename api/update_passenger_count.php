<?php
// api/update_passenger_count.php

header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../models/PassengerCount.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = $_POST['bus_id'] ?? null;
    $count = $_POST['count'] ?? null;

    if (!$bus_id || !$count) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit;
    }

    $database = new Database();
    $db = $database->connect();

    $passengerCount = new PassengerCount($db);

    if ($passengerCount->updateCount($bus_id, $count)) {
        echo json_encode(['status' => 'success', 'message' => 'Count updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update count']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
