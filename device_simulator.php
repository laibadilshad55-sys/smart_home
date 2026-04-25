<?php
/**
 * Device Simulator - Virtual Smart Home Energy Calculator
 * Simulates real device power consumption and energy costs
 */

include 'db.php';
session_start();

class VirtualSmartHome {
    private $conn;
    private $energy_usage = [];
    private $schedules = [];
    private $rate_per_unit = 30; // PKR per kWh (Pakistan average)
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->loadEnergyData();
        $this->loadSchedules();
        $this->ensureSchedulesTable();
    }
    
    // Ensure schedules table exists
    private function ensureSchedulesTable() {
        $this->conn->query("CREATE TABLE IF NOT EXISTS schedules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            device_name VARCHAR(50) NOT NULL,
            schedule_time TIME NOT NULL,
            action_status VARCHAR(10) NOT NULL,
            days VARCHAR(50) DEFAULT 'everyday',
            is_active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    private function loadEnergyData() {
        $result = $this->conn->query("SELECT id, name, status FROM devices");
        if($result) {
            while($row = $result->fetch_assoc()) {
                $this->energy_usage[$row['id']] = [
                    'name' => $row['name'],
                    'watts' => $this->getDeviceWatts($row['name']),
                    'status' => $row['status'],
                    'last_update' => date('Y-m-d H:i:s')
                ];
            }
        }
    }
    
    // Complete wattage database
    private function getDeviceWatts($device_name) {
        $watts = [
            // Lights
            'Living Room Light' => 60,
            'Bedroom Light' => 40,
            'Kitchen Light' => 50,
            'Bathroom Light' => 30,
            'Hall Light' => 45,
            'Garden Light' => 20,
            
            // Fans
            'Bedroom Fan' => 75,
            'Living Room Fan' => 80,
            'Kitchen Exhaust Fan' => 50,
            'Bathroom Exhaust Fan' => 40,
            
            // AC / Cooling
            'Living Room AC' => 1500,
            'Bedroom AC' => 1200,
            
            // Entertainment
            'TV' => 120,
            'Smart TV' => 100,
            'Sound System' => 50,
            'Gaming Console' => 150,
            
            // Kitchen Appliances
            'Microwave' => 1200,
            'Refrigerator' => 150,
            'Oven' => 2000,
            'Kettle' => 2000,
            'Toaster' => 800,
            
            // Others
            'Door' => 10,
            'Smart Plug' => 5,
            'Charger' => 10,
            'Router' => 15,
            'Computer' => 200,
            'Iron' => 1000,
            'Washing Machine' => 500,
            'Vacuum Cleaner' => 800
        ];
        
        // Check for partial matches
        foreach($watts as $key => $watt) {
            if(strpos($device_name, $key) !== false) {
                return $watt;
            }
        }
        
        return 50; // Default wattage
    }
    
    private function loadSchedules() {
        $result = $this->conn->query("SELECT * FROM schedules WHERE is_active = 1");
        if($result) {
            while($row = $result->fetch_assoc()) {
                $this->schedules[] = $row;
            }
        }
    }
    
    // Get current power usage with detailed breakdown
    public function getCurrentPowerUsage() {
        $total_watts = 0;
        $device_breakdown = [];
        
        foreach($this->energy_usage as $device) {
            if($device['status'] == 'ON' || $device['status'] == 'OPEN') {
                $watts = $device['watts'];
                $total_watts += $watts;
                $device_breakdown[] = [
                    'name' => $device['name'],
                    'watts' => $watts,
                    'percentage' => 0 // Will calculate after total
                ];
            }
        }
        
        // Calculate percentages
        foreach($device_breakdown as &$device) {
            $device['percentage'] = $total_watts > 0 ? round(($device['watts'] / $total_watts) * 100, 1) : 0;
        }
        
        $total_kw = round($total_watts / 1000, 2);
        $cost_per_hour = round($total_kw * $this->rate_per_unit, 2);
        $carbon_footprint = round($total_kw * 0.82, 2); // kg CO2 per hour
        
        return [
            'success' => true,
            'total_watts' => $total_watts,
            'total_kw' => $total_kw,
            'cost_per_hour' => $cost_per_hour,
            'cost_per_day' => round($cost_per_hour * 8, 2),
            'cost_per_month' => round($cost_per_hour * 8 * 30, 2),
            'carbon_footprint' => $carbon_footprint,
            'carbon_per_month' => round($carbon_footprint * 8 * 30, 2),
            'active_devices_count' => count($device_breakdown),
            'device_breakdown' => $device_breakdown,
            'rate_per_unit' => $this->rate_per_unit
        ];
    }
    
    // Get highest consuming device
    public function getHighestConsumer() {
        $highest = ['name' => 'None', 'watts' => 0];
        
        foreach($this->energy_usage as $device) {
            if(($device['status'] == 'ON' || $device['status'] == 'OPEN') && $device['watts'] > $highest['watts']) {
                $highest = [
                    'name' => $device['name'],
                    'watts' => $device['watts']
                ];
            }
        }
        
        return $highest;
    }
    
    // Get savings recommendations
    public function getSavingsRecommendations() {
        $recommendations = [];
        $highest = $this->getHighestConsumer();
        
        if($highest['watts'] > 0) {
            $recommendations[] = [
                'type' => 'high_consumer',
                'message' => "💡 Your {$highest['name']} is using {$highest['watts']}W. Turn it off when not needed to save money.",
                'potential_savings' => round(($highest['watts'] / 1000) * $this->rate_per_unit * 8 * 30, 2)
            ];
        }
        
        // Check for lights left on
        $lights_on = 0;
        foreach($this->energy_usage as $device) {
            if(strpos($device['name'], 'Light') !== false && ($device['status'] == 'ON')) {
                $lights_on++;
            }
        }
        
        if($lights_on > 2) {
            $recommendations[] = [
                'type' => 'lights',
                'message' => "💡 $lights_on lights are ON. Turn off unnecessary lights to save energy.",
                'potential_savings' => round(($lights_on * 50 / 1000) * $this->rate_per_unit * 8 * 30, 2)
            ];
        }
        
        return $recommendations;
    }
    
    public function getDailyUsage() {
        $today = date('Y-m-d');
        $result = $this->conn->query("SELECT 
            SUM(CASE WHEN new_status IN ('ON','OPEN') THEN 1 ELSE 0 END) as total_activations,
            COUNT(*) as total_events
            FROM activity_log WHERE DATE(created_at) = '$today'");
        
        if($result) {
            return $result->fetch_assoc();
        }
        return ['total_activations' => 0, 'total_events' => 0];
    }
    
    public function getWeeklyStats() {
        $stats = [];
        for($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $result = $this->conn->query("SELECT COUNT(*) as count FROM activity_log WHERE DATE(created_at) = '$date'");
            $stats[$date] = $result ? $result->fetch_assoc()['count'] : 0;
        }
        return $stats;
    }
    
    public function getMonthlyStats() {
        $monthly = [];
        for($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $result = $this->conn->query("SELECT COUNT(*) as count FROM activity_log WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'");
            $monthly[$month] = $result ? $result->fetch_assoc()['count'] : 0;
        }
        return $monthly;
    }
    
    public function autoSchedule() {
        $hour = date('H');
        $minute = date('i');
        $current_time = "$hour:$minute:00";
        $updates = [];
        
        foreach($this->schedules as $schedule) {
            if($schedule['schedule_time'] == $current_time) {
                $device_name = $this->conn->real_escape_string($schedule['device_name']);
                $action_status = $this->conn->real_escape_string($schedule['action_status']);
                
                // Get old status
                $result = $this->conn->query("SELECT status FROM devices WHERE name = '$device_name'");
                if($row = $result->fetch_assoc()) {
                    $old_status = $row['status'];
                    
                    // Update device
                    $this->conn->query("UPDATE devices SET status = '$action_status' WHERE name = '$device_name'");
                    
                    // Log activity
                    $this->conn->query("INSERT INTO activity_log (device_name, old_status, new_status) 
                                        VALUES ('$device_name', '$old_status', '$action_status')");
                    
                    $updates[] = "{$schedule['device_name']} turned {$schedule['action_status']} (Scheduled)";
                }
            }
        }
        return $updates;
    }
    
    // Get total energy cost for today
    public function getTodayEnergyCost() {
        $active_hours = 8; // Assume 8 hours of active usage
        $usage = $this->getCurrentPowerUsage();
        return [
            'today_cost' => round($usage['cost_per_hour'] * $active_hours, 2),
            'estimated_monthly' => round($usage['cost_per_hour'] * $active_hours * 30, 2),
            'estimated_yearly' => round($usage['cost_per_hour'] * $active_hours * 365, 2)
        ];
    }
}

$virtual_home = new VirtualSmartHome($conn);

// If called directly, return JSON
if(basename($_SERVER['SCRIPT_FILENAME']) == 'device_simulator.php' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch($action) {
        case 'power_usage':
            echo json_encode($virtual_home->getCurrentPowerUsage());
            break;
        case 'recommendations':
            echo json_encode($virtual_home->getSavingsRecommendations());
            break;
        case 'highest_consumer':
            echo json_encode($virtual_home->getHighestConsumer());
            break;
        case 'monthly_stats':
            echo json_encode($virtual_home->getMonthlyStats());
            break;
        default:
            echo json_encode([
                'success' => true,
                'power_usage' => $virtual_home->getCurrentPowerUsage(),
                'daily_usage' => $virtual_home->getDailyUsage(),
                'weekly_stats' => $virtual_home->getWeeklyStats(),
                'highest_consumer' => $virtual_home->getHighestConsumer(),
                'recommendations' => $virtual_home->getSavingsRecommendations()
            ]);
    }
    exit();
}
?>