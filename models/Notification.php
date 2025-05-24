<?php
// models/Notification.php

class Notification {
    private $conn;
    private $table = "notifications";

    // Properties
    public $id;
    public $bus_id;
    public $message;
    public $status; // 'sent' or 'pending'
    public $timestamp;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new notification
    public function create($bus_id, $message, $status = 'pending') {
        $query = "INSERT INTO {$this->table} (bus_id, message, status, timestamp)
                  VALUES (:bus_id, :message, :status, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_id', $bus_id);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Mark a notification as sent
    public function markAsSent($id) {
        $query = "UPDATE {$this->table} SET status = 'sent' WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Get all pending notifications
    public function getPending() {
        $query = "SELECT * FROM {$this->table} WHERE status = 'pending' ORDER BY timestamp ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent notifications
    public function getRecent($limit = 10) {
        $query = "SELECT * FROM {$this->table} ORDER BY timestamp DESC LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
