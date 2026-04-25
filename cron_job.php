<?php
/**
 * Cron Job File - Run every minute
 * Setup: Windows Task Scheduler or Linux Cron
 * Command: php C:/xampp/htdocs/smart_home/cron_job.php
 */

include 'db.php';

// Set timezone
date_default_timezone_set('Asia/Karachi');

// Create schedules table if not exists
$create_table = "
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(50) NOT NULL,
    schedule_time TIME NOT NULL,
    action_status VARCHAR(10) NOT NULL,
    days VARCHAR(50) DEFAULT 'everyday',
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($create_table);

// Insert sample schedules if table is empty
$check = $conn->query("SELECT COUNT(*) as cnt FROM schedules");
$count = $check->fetch_assoc()['cnt'];

if($count == 0) {
    $conn->query("INSERT INTO schedules (device_name, schedule_time, action_status) VALUES 
        ('Living Room Light', '06:00:00', 'ON'),
        ('Living Room Light', '22:00:00', 'OFF'),
        ('Bedroom Light', '07:00:00', 'ON'),
        ('Bedroom Light', '23:00:00', 'OFF')
    ");
    echo "Sample schedules added.\n";
}

// ========== 1. AUTO SCHEDULE CHECK ==========
$current_time = date('H:i:00');
$result = $conn->query("SELECT * FROM schedules WHERE schedule_time = '$current_time' AND is_active = 1");

$schedule_count = 0;
while($schedule = $result->fetch_assoc()) {
    $device_name = $conn->real_escape_string($schedule['device_name']);
    $action_status = $conn->real_escape_string($schedule['action_status']);
    
    // Get old status
    $old_result = $conn->query("SELECT status FROM devices WHERE name = '$device_name'");
    if($old_row = $old_result->fetch_assoc()) {
        $old_status = $old_row['status'];
        
        // Update device
        $conn->query("UPDATE devices SET status = '$action_status' WHERE name = '$device_name'");
        
        // Log activity
        $conn->query("INSERT INTO activity_log (device_name, old_status, new_status) 
                      VALUES ('$device_name', '$old_status', '$action_status')");
        
        $schedule_count++;
    }
}

if($schedule_count > 0) {
    echo "✅ $schedule_count schedule(s) executed at " . date('H:i:s') . "\n";
}

// ========== 2. ENERGY SAVING (Auto OFF after 2 hours) ==========
$energy_saving_enabled = true; // Set to false to disable

if($energy_saving_enabled) {
    $energy_count = 0;
    $result = $conn->query("SELECT id, name FROM devices WHERE status IN ('ON','OPEN')");
    
    while($device = $result->fetch_assoc()) {
        $device_name = $conn->real_escape_string($device['name']);
        $device_id = $device['id'];
        
        // Check last activity
        $last_activity = $conn->query("SELECT created_at FROM activity_log 
                                       WHERE device_name = '$device_name' 
                                       ORDER BY created_at DESC LIMIT 1");
        
        if($last_activity && $last_activity->num_rows > 0) {
            $last = strtotime($last_activity->fetch_assoc()['created_at']);
            $inactive_hours = round((time() - $last) / 3600, 1);
            
            if(time() - $last > 7200) { // 2 hours = 7200 seconds
                $conn->query("UPDATE devices SET status = 'OFF' WHERE id = $device_id");
                $conn->query("INSERT INTO activity_log (device_name, old_status, new_status) 
                              VALUES ('$device_name', 'ON', 'AUTO_OFF_ENERGY_SAVING')");
                $energy_count++;
            }
        }
    }
    
    if($energy_count > 0) {
        echo "⚡ $energy_count device(s) turned off for energy saving\n";
    }
}

// ========== 3. GENERATE DAILY REPORT (At midnight) ==========
$current_hour = date('H');
if($current_hour == '00') { // Runs at midnight
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    $stats = $conn->query("SELECT 
        COUNT(*) as total_activities,
        COUNT(CASE WHEN new_status IN ('ON','OPEN') THEN 1 END) as turn_ons,
        COUNT(CASE WHEN new_status IN ('OFF','CLOSED') THEN 1 END) as turn_offs
        FROM activity_log WHERE DATE(created_at) = '$yesterday'");
    
    $report = $stats->fetch_assoc();
    
    // Save report to file
    $report_text = "[" . date('Y-m-d H:i:s') . "] Daily Report for $yesterday\n";
    $report_text .= "Total Activities: " . $report['total_activities'] . "\n";
    $report_text .= "Turn Ons: " . $report['turn_ons'] . "\n";
    $report_text .= "Turn Offs: " . $report['turn_offs'] . "\n";
    $report_text .= "-----------------------------------\n";
    
    file_put_contents('cron_log.txt', $report_text, FILE_APPEND);
    echo "📊 Daily report generated\n";
}

// ========== 4. CLEANUP OLD ACTIVITY LOGS (Keep last 30 days) ==========
$cleanup_enabled = true;
if($cleanup_enabled && date('H') == '02') { // Runs at 2 AM only
    $conn->query("DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    echo "🗑️ Old activity logs cleaned up\n";
}

echo "✅ Cron job completed at " . date('Y-m-d H:i:s') . "\n";
?>