<?php
include 'db.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Get current status and device name
    $query = "SELECT name, status FROM devices WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $device = mysqli_fetch_assoc($result);
    
    if($device) {
        $old_status = $device['status'];
        $device_name = $device['name'];
        
        // Update device status
        $update = "UPDATE devices SET status = '$new_status' WHERE id = '$id'";
        
        if(mysqli_query($conn, $update)) {
            // Log activity
            $log = "INSERT INTO activity_log (device_name, old_status, new_status, created_at) 
                    VALUES ('$device_name', '$old_status', '$new_status', NOW())";
            mysqli_query($conn, $log);
            
            echo json_encode([
                'success' => true,
                'message' => "$device_name turned $new_status successfully"
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database update failed'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Device not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>