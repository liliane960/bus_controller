<?php
// api/send_alert.php

require_once '../config/db.php';
require_once '../models/Notification.php';

$database = new Database();
$db = $database->connect();

$notification = new Notification($db);

// Fetch pending notifications
$pendingAlerts = $notification->getPending();

foreach ($pendingAlerts as $alert) {
    // TODO: integrate with SMS/email gateway to send alert here

    // Mark alert as sent
    $notification->markAsSent($alert['id']);
}

echo json_encode(['status' => 'success', 'message' => 'Processed all pending alerts']);
