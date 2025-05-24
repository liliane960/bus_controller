<?php
// models/PassengerCount.php

class PassengerCount {
    private $conn;
    private $table = "passenger_counts";

    // Properties
    public $id;
    public $bus_id;
    public $current_count;
    public $timestamp;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Record a new passenger count
    public function recordCount($bus_id, $current_count) {
        $query = "INSERT INTO {$this->table} (bus_id, current_count, timestamp)
                  VALUES (:bus_id, :current_count, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_id', $bus_id);
        $stmt->bindParam(':current_count', $current_count);

        return $stmt->execute();
    }

    // Get the latest count for a specific bus
    public function getLatestCount($bus_id) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE bus_id = :bus_id 
                  ORDER BY timestamp DESC 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_id', $bus_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get historical counts for a bus (optional)
    public function getHistory($bus_id, $limit = 50) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE bus_id = :bus_id 
                  ORDER BY timestamp DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
