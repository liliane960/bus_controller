-- Smart Bus Monitoring System Database Setup
-- Database: smart_bus_monitoring

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS smart_bus_monitoring;
USE smart_bus_monitoring;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS passenger_counts;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS buses;
DROP TABLE IF EXISTS users;

-- 1. Create users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'driver', 'police') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create buses table
CREATE TABLE buses (
    bus_id INT PRIMARY KEY AUTO_INCREMENT,
    plate_number VARCHAR(50) UNIQUE NOT NULL,
    capacity INT NOT NULL,
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    driver_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- 3. Create passenger_counts table
CREATE TABLE passenger_counts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bus_id INT NOT NULL,
    current_count INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id) ON DELETE CASCADE
);

-- 4. Create notifications table
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    bus_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comment TEXT,
    status ENUM('pending', 'sent') DEFAULT 'pending',
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_bus_driver ON buses(driver_id);
CREATE INDEX idx_bus_status ON buses(status);
CREATE INDEX idx_passenger_bus_time ON passenger_counts(bus_id, timestamp);
CREATE INDEX idx_notification_bus_time ON notifications(bus_id, sent_at);
CREATE INDEX idx_notification_status ON notifications(status);
CREATE INDEX idx_user_role ON users(role);

-- Insert sample data

-- Sample users (password: 'password123' hashed)
INSERT INTO users (username, password, role) VALUES
('admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('driver1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'driver'),
('driver2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'driver'),
('police1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'police'),
('police2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'police');

-- Sample buses
INSERT INTO buses (plate_number, capacity, status, driver_id) VALUES
('ABC-123', 50, 'active', 2),
('XYZ-456', 45, 'active', 3),
('DEF-789', 40, 'maintenance', NULL),
('GHI-012', 55, 'active', NULL);

-- Sample passenger counts
INSERT INTO passenger_counts (bus_id, current_count, timestamp) VALUES
(1, 25, NOW() - INTERVAL 10 MINUTE),
(1, 30, NOW() - INTERVAL 5 MINUTE),
(1, 35, NOW()),
(2, 20, NOW() - INTERVAL 15 MINUTE),
(2, 25, NOW() - INTERVAL 8 MINUTE),
(2, 30, NOW()),
(4, 15, NOW() - INTERVAL 20 MINUTE),
(4, 20, NOW());

-- Sample notifications
INSERT INTO notifications (bus_id, message, sent_at, comment, status) VALUES
(1, 'Bus ABC-123 is approaching capacity (35/50 passengers)', NOW() - INTERVAL 30 MINUTE, 'Monitor closely', 'sent'),
(2, 'Bus XYZ-456 passenger count increased to 30', NOW() - INTERVAL 25 MINUTE, 'Normal operation', 'sent'),
(1, 'Bus ABC-123 reached 80% capacity', NOW() - INTERVAL 10 MINUTE, 'Consider additional bus', 'pending'),
(4, 'Bus GHI-012 started route with 20 passengers', NOW() - INTERVAL 5 MINUTE, 'Route 1', 'sent');

-- Create views for common queries

-- View for bus summary with latest passenger count
CREATE VIEW bus_summary AS
SELECT 
    b.bus_id,
    b.plate_number,
    b.capacity,
    b.status,
    u.username as driver_name,
    COALESCE(pc.current_count, 0) as current_passengers,
    (b.capacity - COALESCE(pc.current_count, 0)) as available_seats,
    CASE 
        WHEN COALESCE(pc.current_count, 0) >= b.capacity * 0.8 THEN 'High'
        WHEN COALESCE(pc.current_count, 0) >= b.capacity * 0.6 THEN 'Medium'
        ELSE 'Low'
    END as occupancy_level
FROM buses b
LEFT JOIN users u ON b.driver_id = u.user_id
LEFT JOIN (
    SELECT bus_id, current_count 
    FROM passenger_counts pc1
    WHERE timestamp = (
        SELECT MAX(timestamp) 
        FROM passenger_counts pc2 
        WHERE pc2.bus_id = pc1.bus_id
    )
) pc ON b.bus_id = pc.bus_id;

-- View for notification summary
CREATE VIEW notification_summary AS
SELECT 
    n.notification_id,
    n.bus_id,
    b.plate_number,
    n.message,
    n.sent_at,
    n.comment,
    n.status,
    u.username as driver_name
FROM notifications n
JOIN buses b ON n.bus_id = b.bus_id
LEFT JOIN users u ON b.driver_id = u.user_id
ORDER BY n.sent_at DESC;

-- Stored procedure for getting dashboard statistics
DELIMITER //
CREATE PROCEDURE GetDashboardStats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'admin') as admin_count,
        (SELECT COUNT(*) FROM users WHERE role = 'driver') as driver_count,
        (SELECT COUNT(*) FROM users WHERE role = 'police') as police_count,
        (SELECT COUNT(*) FROM buses WHERE status = 'active') as active_buses,
        (SELECT COUNT(*) FROM notifications WHERE status = 'pending') as pending_notifications,
        (SELECT COUNT(*) FROM notifications WHERE DATE(sent_at) = CURDATE()) as today_notifications;
END //
DELIMITER ;

-- Stored procedure for recording passenger count with notification
DELIMITER //
CREATE PROCEDURE RecordPassengerCount(
    IN p_bus_id INT,
    IN p_count INT
)
BEGIN
    DECLARE bus_capacity INT;
    DECLARE occupancy_percentage DECIMAL(5,2);
    
    -- Get bus capacity
    SELECT capacity INTO bus_capacity FROM buses WHERE bus_id = p_bus_id;
    
    -- Calculate occupancy percentage
    SET occupancy_percentage = (p_count / bus_capacity) * 100;
    
    -- Record passenger count
    INSERT INTO passenger_counts (bus_id, current_count) VALUES (p_bus_id, p_count);
    
    -- Create notification if occupancy is high
    IF occupancy_percentage >= 80 THEN
        INSERT INTO notifications (bus_id, message, status) 
        VALUES (p_bus_id, CONCAT('Bus occupancy at ', ROUND(occupancy_percentage, 1), '% (', p_count, '/', bus_capacity, ' passengers)'), 'pending');
    END IF;
END //
DELIMITER ;

-- Grant permissions (adjust as needed)
-- GRANT ALL PRIVILEGES ON smart_bus_monitoring.* TO 'your_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Show created tables
SHOW TABLES;

-- Show sample data
SELECT 'Users:' as table_name;
SELECT user_id, username, role FROM users;

SELECT 'Buses:' as table_name;
SELECT bus_id, plate_number, capacity, status, driver_id FROM buses;

SELECT 'Latest Passenger Counts:' as table_name;
SELECT bus_id, current_count, timestamp FROM passenger_counts 
WHERE timestamp = (SELECT MAX(timestamp) FROM passenger_counts pc2 WHERE pc2.bus_id = passenger_counts.bus_id);

SELECT 'Recent Notifications:' as table_name;
SELECT notification_id, bus_id, message, sent_at, status FROM notifications ORDER BY sent_at DESC LIMIT 5; 