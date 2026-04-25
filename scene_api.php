<?php
/**
 * Scene API - Handles smart scenes like Good Morning, Good Night, etc.
 * Called by index.php when user clicks scene buttons
 */

include 'db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get scene name from request
$scene = isset($_POST['scene']) ? trim($_POST['scene']) : '';
$response = ['success' => false, 'message' => '❌ Scene not found'];

// Define all scenes with their actions
$scenes = [
    'good_morning' => [
        'name' => 'Good Morning',
        'icon' => '🌅',
        'devices' => [
            'Living Room Light' => 'ON',
            'TV' => 'ON',
            'Kitchen Light' => 'ON',
            'Bedroom Light' => 'OFF'
        ]
    ],
    'good_night' => [
        'name' => 'Good Night',
        'icon' => '🌙',
        'devices' => [
            'Living Room Light' => 'OFF',
            'TV' => 'OFF',
            'Living Room AC' => 'OFF',
            'Bedroom Light' => 'OFF',
            'Bedroom Fan' => 'OFF',
            'Kitchen Light' => 'OFF',
            'Kitchen Exhaust Fan' => 'OFF',
            'Microwave' => 'OFF'
        ]
    ],
    'movie_time' => [
        'name' => 'Movie Time',
        'icon' => '🎬',
        'devices' => [
            'Living Room Light' => 'OFF',
            'TV' => 'ON',
            'Living Room AC' => 'ON'
        ]
    ],
    'away' => [
        'name' => 'Away Mode',
        'icon' => '🚪',
        'devices' => [
            'Living Room Light' => 'OFF',
            'Bedroom Light' => 'OFF',
            'Kitchen Light' => 'OFF',
            'TV' => 'OFF',
            'Living Room AC' => 'OFF',
            'Bedroom Fan' => 'OFF',
            'Kitchen Exhaust Fan' => 'OFF',
            'Microwave' => 'OFF'
        ]
    ],
    'study_time' => [
        'name' => 'Study Time',
        'icon' => '📚',
        'devices' => [
            'Living Room Light' => 'OFF',
            'Bedroom Light' => 'ON',
            'TV' => 'OFF',
            'Bedroom Fan' => 'ON'
        ]
    ],
    'dinner_time' => [
        'name' => 'Dinner Time',
        'icon' => '🍽️',
        'devices' => [
            'Kitchen Light' => 'ON',
            'Living Room Light' => 'ON',
            'TV' => 'OFF'
        ]
    ]
];

// Check if scene exists
if(isset($scenes[$scene])) {
    $scene_data = $scenes[$scene];
    $updated_devices = [];
    $success_count = 0;
    $error_count = 0;
    
    foreach($scene_data['devices'] as $device_name => $new_status) {
        // Escape device name to prevent SQL injection
        $device_name = mysqli_real_escape_string($conn, $device_name);
        $new_status = mysqli_real_escape_string($conn, $new_status);
        
        // Get old status
        $result = $conn->query("SELECT status FROM devices WHERE name = '$device_name'");
        
        if($result && $row = $result->fetch_assoc()) {
            $old_status = $row['status'];
            
            // Only update if status is different
            if($old_status != $new_status) {
                // Update device
                if($conn->query("UPDATE devices SET status = '$new_status' WHERE name = '$device_name'")) {
                    // Log activity
                    $conn->query("INSERT INTO activity_log (device_name, old_status, new_status) 
                                  VALUES ('$device_name', '$old_status', '$new_status')");
                    $updated_devices[] = "$device_name: $old_status → $new_status";
                    $success_count++;
                } else {
                    $error_count++;
                }
            } else {
                $updated_devices[] = "$device_name: already $new_status (no change)";
            }
        } else {
            $error_count++;
        }
    }
    
    $scene_name = $scene_data['name'];
    $icon = $scene_data['icon'];
    
    $response = [
        'success' => true,
        'message' => "{$icon} {$scene_name} scene activated! ({$success_count} devices updated)",
        'scene' => $scene,
        'scene_name' => $scene_name,
        'devices_updated' => $success_count,
        'devices' => $updated_devices
    ];
    
    // If there were errors, add warning
    if($error_count > 0) {
        $response['warning'] = "⚠️ $error_count device(s) could not be updated";
    }
    
} else {
    // Scene not found - return available scenes
    $available_scenes = array_keys($scenes);
    $response = [
        'success' => false,
        'message' => "❌ Scene '$scene' not found",
        'available_scenes' => $available_scenes,
        'hint' => "Try: " . implode(', ', $available_scenes)
    ];
}

echo json_encode($response);
?>