<?php
include 'db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');

$method = $_SERVER['REQUEST_METHOD'];

// Allowed status values
$allowed_status = ['ON', 'OFF', 'OPEN', 'CLOSED'];

// ========== GET METHOD - Get all devices ==========
if($method == 'GET') {
    $result = $conn->query("SELECT id, name, status FROM devices ORDER BY id");
    $devices = [];
    while($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }
    echo json_encode(['success' => true, 'devices' => $devices]);
    exit();
}

// ========== POST METHOD - Control device ==========
elseif($method == 'POST') {
    
    // Get input (supports both form data and JSON)
    if(isset($_POST['name']) && isset($_POST['status'])) {
        $device_name = trim($_POST['name']);
        $new_status = trim($_POST['status']);
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        $device_name = isset($data['name']) ? trim($data['name']) : '';
        $new_status = isset($data['status']) ? trim($data['status']) : '';
    }
    
    // Validate inputs
    if(empty($device_name)) {
        echo json_encode(['success' => false, 'message' => '❌ Device name is required']);
        exit();
    }
    
    if(!in_array($new_status, $allowed_status)) {
        echo json_encode(['success' => false, 'message' => '❌ Invalid status. Allowed: ON, OFF, OPEN, CLOSED']);
        exit();
    }
    
    // Escape device name to prevent SQL injection
    $device_name = mysqli_real_escape_string($conn, $device_name);
    
    // Check if device exists
    $check = $conn->query("SELECT id, status FROM devices WHERE name = '$device_name'");
    
    if($check && $check->num_rows > 0) {
        $device = $check->fetch_assoc();
        $old_status = $device['status'];
        $device_id = $device['id'];
        
        // Update device status
        $update = $conn->query("UPDATE devices SET status = '$new_status' WHERE name = '$device_name'");
        
        if($update) {
            // Log activity
            $conn->query("INSERT INTO activity_log (device_name, old_status, new_status) 
                          VALUES ('$device_name', '$old_status', '$new_status')");
            
            echo json_encode([
                'success' => true, 
                'message' => "✅ $device_name turned $new_status successfully",
                'device_id' => $device_id,
                'old_status' => $old_status,
                'new_status' => $new_status
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Database update failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Device not found: ' . $device_name]);
    }
    exit();
}

// ========== PUT METHOD - Bulk update ==========
elseif($method == 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(isset($data['devices']) && is_array($data['devices'])) {
        $success_count = 0;
        foreach($data['devices'] as $device) {
            $name = mysqli_real_escape_string($conn, $device['name']);
            $status = $device['status'];
            
            if(in_array($status, $allowed_status)) {
                $result = $conn->query("SELECT status FROM devices WHERE name = '$name'");
                if($row = $result->fetch_assoc()) {
                    $old = $row['status'];
                    $conn->query("UPDATE devices SET status = '$status' WHERE name = '$name'");
                    $conn->query("INSERT INTO activity_log (device_name, old_status, new_status) 
                                  VALUES ('$name', '$old', '$status')");
                    $success_count++;
                }
            }
        }
        echo json_encode(['success' => true, 'updated' => $success_count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid bulk data']);
    }
    exit();
}

// ========== METHOD NOT ALLOWED ==========
else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use GET, POST, or PUT']);
    exit();
}
?>