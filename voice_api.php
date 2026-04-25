<?php
include 'db.php';
header('Content-Type: application/json');

$command = isset($_POST['command']) ? strtolower(trim($_POST['command'])) : '';
$response = ['success' => false, 'message' => 'Say: light on, fan off, door open, good morning, good night'];

// Simple keyword matching
if(!empty($command)) {
    
    // LIGHT ON
    if(strpos($command, 'light') !== false && strpos($command, 'on') !== false) {
        // Check which room
        if(strpos($command, 'living') !== false || strpos($command, 'room') !== false) {
            $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Living Room Light'");
            $response = ['success' => true, 'message' => "Living room light ON"];
        }
        elseif(strpos($command, 'bed') !== false || strpos($command, 'bedroom') !== false) {
            $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Bedroom Light'");
            $response = ['success' => true, 'message' => "Bedroom light ON"];
        }
        elseif(strpos($command, 'kitchen') !== false) {
            $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Kitchen Light'");
            $response = ['success' => true, 'message' => "Kitchen light ON"];
        }
        else {
            $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Living Room Light'");
            $response = ['success' => true, 'message' => "Light turned ON"];
        }
    }
    
    // LIGHT OFF
    elseif(strpos($command, 'light') !== false && strpos($command, 'off') !== false) {
        if(strpos($command, 'living') !== false || strpos($command, 'room') !== false) {
            $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Living Room Light'");
            $response = ['success' => true, 'message' => "Living room light OFF"];
        }
        elseif(strpos($command, 'bed') !== false || strpos($command, 'bedroom') !== false) {
            $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Bedroom Light'");
            $response = ['success' => true, 'message' => "Bedroom light OFF"];
        }
        elseif(strpos($command, 'kitchen') !== false) {
            $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Kitchen Light'");
            $response = ['success' => true, 'message' => "Kitchen light OFF"];
        }
        else {
            $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Living Room Light'");
            $response = ['success' => true, 'message' => "Light turned OFF"];
        }
    }
    
    // FAN ON
    elseif(strpos($command, 'fan') !== false && strpos($command, 'on') !== false) {
        $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Bedroom Fan'");
        $response = ['success' => true, 'message' => "Fan turned ON"];
    }
    
    // FAN OFF
    elseif(strpos($command, 'fan') !== false && strpos($command, 'off') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Bedroom Fan'");
        $response = ['success' => true, 'message' => "Fan turned OFF"];
    }
    
    // TV ON
    elseif(strpos($command, 'tv') !== false && strpos($command, 'on') !== false) {
        $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'TV'");
        $response = ['success' => true, 'message' => "TV turned ON"];
    }
    
    // TV OFF
    elseif(strpos($command, 'tv') !== false && strpos($command, 'off') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'TV'");
        $response = ['success' => true, 'message' => "TV turned OFF"];
    }
    
    // AC ON
    elseif((strpos($command, 'ac') !== false || strpos($command, 'cooler') !== false) && strpos($command, 'on') !== false) {
        $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Living Room AC'");
        $response = ['success' => true, 'message' => "AC turned ON"];
    }
    
    // AC OFF
    elseif((strpos($command, 'ac') !== false || strpos($command, 'cooler') !== false) && strpos($command, 'off') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Living Room AC'");
        $response = ['success' => true, 'message' => "AC turned OFF"];
    }
    
    // DOOR OPEN
    elseif((strpos($command, 'door') !== false || strpos($command, 'gate') !== false) && strpos($command, 'open') !== false) {
        $conn->query("UPDATE devices SET status = 'OPEN' WHERE name = 'Door'");
        $response = ['success' => true, 'message' => "Door opened"];
    }
    
    // DOOR CLOSE
    elseif((strpos($command, 'door') !== false || strpos($command, 'gate') !== false) && (strpos($command, 'close') !== false || strpos($command, 'shut') !== false)) {
        $conn->query("UPDATE devices SET status = 'CLOSED' WHERE name = 'Door'");
        $response = ['success' => true, 'message' => "Door closed"];
    }
    
    // EXHAUST FAN ON
    elseif((strpos($command, 'exhaust') !== false || strpos($command, 'chimney') !== false) && strpos($command, 'on') !== false) {
        $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Kitchen Exhaust Fan'");
        $response = ['success' => true, 'message' => "Exhaust fan ON"];
    }
    
    // EXHAUST FAN OFF
    elseif((strpos($command, 'exhaust') !== false || strpos($command, 'chimney') !== false) && strpos($command, 'off') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Kitchen Exhaust Fan'");
        $response = ['success' => true, 'message' => "Exhaust fan OFF"];
    }
    
    // MICROWAVE ON
    elseif((strpos($command, 'micro') !== false || strpos($command, 'oven') !== false) && strpos($command, 'on') !== false) {
        $conn->query("UPDATE devices SET status = 'ON' WHERE name = 'Microwave'");
        $response = ['success' => true, 'message' => "Microwave ON"];
    }
    
    // MICROWAVE OFF
    elseif((strpos($command, 'micro') !== false || strpos($command, 'oven') !== false) && strpos($command, 'off') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Microwave'");
        $response = ['success' => true, 'message' => "Microwave OFF"];
    }
    
    // GOOD MORNING
    elseif(strpos($command, 'morning') !== false || strpos($command, 'good morning') !== false) {
        $conn->query("UPDATE devices SET status = 'ON' WHERE name IN ('Living Room Light', 'TV', 'Kitchen Light')");
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name = 'Bedroom Light'");
        $response = ['success' => true, 'message' => "Good morning! Lights ON"];
    }
    
    // GOOD NIGHT
    elseif(strpos($command, 'night') !== false || strpos($command, 'good night') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name != 'Door'");
        $conn->query("UPDATE devices SET status = 'CLOSED' WHERE name = 'Door'");
        $response = ['success' => true, 'message' => "Good night! All OFF"];
    }
    
    // ALL OFF
    elseif(strpos($command, 'all off') !== false || strpos($command, 'everything off') !== false) {
        $conn->query("UPDATE devices SET status = 'OFF' WHERE name != 'Door'");
        $conn->query("UPDATE devices SET status = 'CLOSED' WHERE name = 'Door'");
        $response = ['success' => true, 'message' => "All devices OFF"];
    }
    
    // Log activity
    if($response['success']) {
        $conn->query("INSERT INTO activity_log (device_name, old_status, new_status) VALUES ('Voice', 'Command', '{$response['message']}')");
    }
}

echo json_encode($response);
?>