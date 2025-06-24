# Smart Bus Monitoring System - Database Structure

## Database Configuration
- **Database Name**: `smart_bus_monitoring`
- **Host**: `localhost`
- **User**: `root`
- **Password**: `""` (empty)

## Table Structures

### 1. `users` Table
**Purpose**: Store user accounts for admin, driver, and police roles

```sql
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'driver', 'police') NOT NULL
);
```

**Key Queries**:
```sql
-- Count users by role (Dashboard.php)
SELECT COUNT(*) as total FROM users WHERE role = 'admin'
SELECT COUNT(*) as total FROM users WHERE role = 'driver'
SELECT COUNT(*) as total FROM users WHERE role = 'police'

-- Get all users (view_users.php)
SELECT user_id, username, role FROM users

-- User authentication (login.php)
SELECT username, password, role FROM users WHERE username = ?
```

### 2. `buses` Table
**Purpose**: Store bus information and driver assignments

```sql
CREATE TABLE buses (
    bus_id INT PRIMARY KEY AUTO_INCREMENT,
    plate_number VARCHAR(50) UNIQUE NOT NULL,
    capacity INT NOT NULL,
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    driver_id INT,
    FOREIGN KEY (driver_id) REFERENCES users(user_id)
);
```

**Key Queries**:
```sql
-- Get all buses with driver info (view_buses.php)
SELECT b.bus_id, b.plate_number, b.capacity, b.status, u.username AS driver_name 
FROM buses b 
LEFT JOIN users u ON b.driver_id = u.user_id 
ORDER BY b.bus_id ASC

-- Get buses for specific driver (view_notifications.php)
SELECT bus_id FROM buses WHERE driver_id = ? AND bus_id = ?
```

### 3. `passenger_counts` Table
**Purpose**: Track passenger counts for each bus over time

```sql
CREATE TABLE passenger_counts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bus_id INT NOT NULL,
    current_count INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id)
);
```

**Key Queries**:
```sql
-- Record new passenger count (PassengerCount.php)
INSERT INTO passenger_counts (bus_id, current_count, timestamp) 
VALUES (?, ?, NOW())

-- Get latest count for specific bus (PassengerCount.php)
SELECT * FROM passenger_counts 
WHERE bus_id = ? 
ORDER BY timestamp DESC 
LIMIT 1

-- Get historical counts (PassengerCount.php)
SELECT * FROM passenger_counts 
WHERE bus_id = ? 
ORDER BY timestamp DESC 
LIMIT ?
```

### 4. `notifications` Table
**Purpose**: Store alerts and notifications for bus events

```sql
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    bus_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comment TEXT,
    status ENUM('pending', 'sent') DEFAULT 'pending',
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id)
);
```

**Key Queries**:
```sql
-- Create new notification (Notification.php)
INSERT INTO notifications (bus_id, message, status, timestamp) 
VALUES (?, ?, ?, NOW())

-- Get all notifications for admin/police (view_notifications.php)
SELECT notification_id, bus_id, message, sent_at, comment 
FROM notifications 
ORDER BY sent_at DESC

-- Get notifications for specific driver (view_notifications.php)
SELECT n.notification_id, n.bus_id, n.message, n.sent_at, n.comment
FROM notifications n
INNER JOIN buses b ON n.bus_id = b.bus_id
WHERE b.driver_id = ?
ORDER BY n.sent_at DESC

-- Get pending notifications (Notification.php)
SELECT * FROM notifications WHERE status = 'pending' ORDER BY timestamp ASC

-- Get recent notifications (Notification.php)
SELECT * FROM notifications ORDER BY timestamp DESC LIMIT ?

-- Count total notifications (Dashboard.php)
SELECT COUNT(*) as total FROM notifications
```

## Database Relationships

```
users (1) ←→ (many) buses
buses (1) ←→ (many) passenger_counts
buses (1) ←→ (many) notifications
```

## Key Business Logic Queries

### Dashboard Summary Queries
```sql
-- Admin Dashboard Summary
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'admin') as admin_count,
    (SELECT COUNT(*) FROM users WHERE role = 'driver') as driver_count,
    (SELECT COUNT(*) FROM users WHERE role = 'police') as police_count,
    (SELECT COUNT(*) FROM notifications) as notification_count;
```

### Role-Based Access Control
```sql
-- Check if driver owns a bus
SELECT bus_id FROM buses WHERE driver_id = ? AND bus_id = ?

-- Get buses assigned to driver
SELECT * FROM buses WHERE driver_id = ?
```

### Passenger Count Monitoring
```sql
-- Get current passenger count vs capacity
SELECT 
    b.bus_id,
    b.plate_number,
    b.capacity,
    pc.current_count,
    (b.capacity - pc.current_count) as available_seats
FROM buses b
LEFT JOIN (
    SELECT bus_id, current_count 
    FROM passenger_counts 
    WHERE timestamp = (
        SELECT MAX(timestamp) 
        FROM passenger_counts pc2 
        WHERE pc2.bus_id = passenger_counts.bus_id
    )
) pc ON b.bus_id = pc.bus_id
WHERE b.status = 'active';
```

### Notification System
```sql
-- Get notifications with bus details
SELECT 
    n.notification_id,
    n.bus_id,
    b.plate_number,
    n.message,
    n.sent_at,
    n.comment,
    u.username as driver_name
FROM notifications n
JOIN buses b ON n.bus_id = b.bus_id
LEFT JOIN users u ON b.driver_id = u.user_id
ORDER BY n.sent_at DESC;
```

## API Endpoints and Queries

### 1. Fetch Data API (`api/fetch_data.php`)
```php
// Get current passenger counts and recent alerts
$currentCounts = $passengerCount->getAllCounts();
$recentAlerts = $notification->getRecent(5);
```

### 2. Update Passenger Count API (`api/update_passenger_count.php`)
```php
// Update passenger count for specific bus
$passengerCount->updateCount($bus_id, $count);
```

### 3. Send Alert API (`api/send_alert.php`)
```php
// Create new notification
$notification->create($bus_id, $message, $status);
```

## Security Considerations

1. **Password Hashing**: All passwords are hashed using `password_hash()` and verified with `password_verify()`
2. **Prepared Statements**: All queries use prepared statements to prevent SQL injection
3. **Session Management**: User authentication is managed through PHP sessions
4. **Role-Based Access**: Different queries are used based on user roles (admin, driver, police)

## Performance Optimization

1. **Indexes**: Consider adding indexes on frequently queried columns:
   ```sql
   CREATE INDEX idx_bus_driver ON buses(driver_id);
   CREATE INDEX idx_passenger_bus_time ON passenger_counts(bus_id, timestamp);
   CREATE INDEX idx_notification_bus_time ON notifications(bus_id, sent_at);
   ```

2. **Query Optimization**: Use LIMIT clauses for large datasets and proper JOIN conditions

3. **Connection Management**: Database connections are properly closed after use

## Export Functionality

The system includes CSV export capabilities for:
- Bus reports (`export_car_counts.php`)
- Passenger counts (`export_passenger_counts.php`)
- Notifications (`export_notification.php`)

Each export file generates CSV data from the respective tables for reporting purposes. 