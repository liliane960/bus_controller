<?php
// models/Bus.php

class Bus {
    private $conn;
    private $table = "buses";

    // Bus properties
    public $id;
    public $bus_number;
    public $capacity;
    public $route;
    public $status;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Register a new bus
    public function registerBus($bus_number, $capacity, $route, $status = 'active') {
        $query = "INSERT INTO {$this->table} (bus_number, capacity, route, status) 
                  VALUES (:bus_number, :capacity, :route, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_number', $bus_number);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':route', $route);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Get all buses
    public function getAllBuses() {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get a bus by ID
    public function getBusById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a bus's info
    public function updateBus($id, $bus_number, $capacity, $route, $status) {
        $query = "UPDATE {$this->table} 
                  SET bus_number = :bus_number, capacity = :capacity, route = :route, status = :status 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_number', $bus_number);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':route', $route);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Delete a bus (optional)
    public function deleteBus($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
