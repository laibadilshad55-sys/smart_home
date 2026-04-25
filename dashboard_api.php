<?php
/**
 * Dashboard API - For statistics and reports only
 * Voice commands and scenes are handled by separate files
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include 'db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Rate per unit in PKR
$rate_per_unit = 30;

switch($action) {
    
    // ========== MAIN STATISTICS ==========
    case 'stats':
        $total = $conn->query("SELECT COUNT(*) as count FROM devices")->fetch_assoc()['count'];
        $active = $conn->query("SELECT COUNT(*) as count FROM devices WHERE status IN ('ON','OPEN')")->fetch_assoc()['count'];
        $today = $conn->query("SELECT COUNT(*) as count FROM activity_log WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
        
        // Calculate power usage
        $power_usage = $active * 50; // Watts
        $energy_cost = round(($power_usage / 1000) * $rate_per_unit, 2);
        
        // Get weekly activity
        $weekly = [];
        for($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $day_result = $conn->query("SELECT COUNT(*) as c FROM activity_log WHERE DATE(created_at) = '$date'");
            $weekly[$date] = $day_result->fetch_assoc()['c'];
        }
        
        $stats = [
            'success' => true,
            'total_devices' => $total,
            'active_devices' => $active,
            'today_activity' => $today,
            'power_usage' => [
                'watts' => $power_usage,
                'kw' => round($power_usage / 1000, 2),
                'cost_per_hour' => $energy_cost,
                'cost_per_day' => round($energy_cost * 8, 2),
                'cost_per_month' => round($energy_cost * 8 * 30, 2)
            ],
            'weekly_activity' => $weekly
        ];
        echo json_encode($stats);
        break;
    
    // ========== ENERGY REPORT ==========
    case 'energy_report':
        $active = $conn->query("SELECT COUNT(*) as count FROM devices WHERE status IN ('ON','OPEN')")->fetch_assoc()['count'];
        $power_usage = $active * 50;
        $energy_cost = round(($power_usage / 1000) * $rate_per_unit, 2);
        $carbon_footprint = round(($power_usage / 1000) * 0.82, 2); // kg CO2 per hour
        
        // Efficiency rating
        if($power_usage < 500) {
            $efficiency = 'A+ (Excellent)';
            $color = '#10b981';
        } elseif($power_usage < 1000) {
            $efficiency = 'B (Good)';
            $color = '#f59e0b';
        } else {
            $efficiency = 'C (Needs Improvement)';
            $color = '#ef4444';
        }
        
        echo json_encode([
            'success' => true,
            'current_power' => $power_usage . 'W',
            'current_kw' => round($power_usage / 1000, 2) . 'kW',
            'energy_cost' => 'PKR ' . $energy_cost . '/hour',
            'cost_per_day' => 'PKR ' . round($energy_cost * 8, 2),
            'cost_per_month' => 'PKR ' . round($energy_cost * 8 * 30, 2),
            'carbon_footprint' => $carbon_footprint . ' kg CO2/hour',
            'efficiency_rating' => $efficiency,
            'efficiency_color' => $color,
            'savings_tip' => getSavingsTip($active)
        ]);
        break;
    
    // ========== DEVICE SPECIFIC STATS ==========
    case 'device_stats':
        $device_name = isset($_GET['device']) ? mysqli_real_escape_string($conn, $_GET['device']) : '';
        
        if($device_name) {
            $result = $conn->query("SELECT status FROM devices WHERE name = '$device_name'");
            $current = $result->fetch_assoc()['status'];
            
            $history = $conn->query("SELECT new_status, created_at FROM activity_log 
                                     WHERE device_name = '$device_name' 
                                     ORDER BY created_at DESC LIMIT 10");
            $history_data = [];
            while($row = $history->fetch_assoc()) {
                $history_data[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'device' => $device_name,
                'current_status' => $current,
                'recent_activity' => $history_data
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Device name required']);
        }
        break;
    
    // ========== PEAK HOURS ANALYSIS ==========
    case 'peak_hours':
        $result = $conn->query("SELECT 
            HOUR(created_at) as hour,
            COUNT(*) as activity_count
            FROM activity_log 
            WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY HOUR(created_at)
            ORDER BY activity_count DESC
            LIMIT 5");
        
        $peak_hours = [];
        while($row = $result->fetch_assoc()) {
            $peak_hours[] = [
                'hour' => $row['hour'] . ':00 - ' . ($row['hour']+1) . ':00',
                'activity_count' => $row['activity_count']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'peak_hours' => $peak_hours,
            'recommendation' => 'Consider using devices during off-peak hours (12 AM - 6 AM) for better rates'
        ]);
        break;
    
    // ========== MONTHLY COMPARISON ==========
    case 'monthly_comparison':
        $current_month = date('m');
        $last_month = date('m', strtotime('-1 month'));
        
        $current = $conn->query("SELECT COUNT(*) as c FROM activity_log WHERE MONTH(created_at) = $current_month")->fetch_assoc()['c'];
        $last = $conn->query("SELECT COUNT(*) as c FROM activity_log WHERE MONTH(created_at) = $last_month")->fetch_assoc()['c'];
        
        $change = $last > 0 ? round((($current - $last) / $last) * 100, 1) : 0;
        
        echo json_encode([
            'success' => true,
            'current_month_activities' => $current,
            'last_month_activities' => $last,
            'change_percentage' => $change,
            'trend' => $change > 0 ? '📈 Increasing' : ($change < 0 ? '📉 Decreasing' : '➡️ Stable')
        ]);
        break;
    
    // ========== DEFAULT ==========
    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid action. Available: stats, energy_report, device_stats, peak_hours, monthly_comparison',
            'available_actions' => ['stats', 'energy_report', 'device_stats', 'peak_hours', 'monthly_comparison']
        ]);
        break;
}

// Helper function for savings tips
function getSavingsTip($active_devices) {
    if($active_devices == 0) {
        return "All devices are OFF. Great job saving energy! 🌱";
    } elseif($active_devices <= 2) {
        return "You're doing well! Try turning off devices when not in use. 💡";
    } elseif($active_devices <= 4) {
        return "Consider using energy-efficient devices to reduce bills. ⚡";
    } else {
        return "Many devices are ON. Turn off unused devices to save up to 30% on your bill! 🔌";
    }
}
?>