<?php
// api/fetch_data.php

header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../models/PassengerCount.php';
require_once '../models/Notification.php';

$database = new Database();
$db = $database->connect();

$passengerCount = new PassengerCount($db);
$notification = new Notification($db);

// Example: get current counts and recent alerts
$currentCounts = $passengerCount->getAllCounts();
$recentAlerts = $notification->getRecent(5);

echo json_encode([
    'passenger_counts' => $currentCounts,
    'recent_alerts' => $recentAlerts
]);
