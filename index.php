<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>SmartHome Pro | Premium Animated Smart Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 20% 50%, #0a0f2a, #0a0a1a);
            min-height: 100vh;
            padding: 20px;
            transition: background 0.3s ease;
        }
        
        /* Premium Dark/Light Mode */
        body.light-mode {
            background: radial-gradient(circle at 20% 50%, #e0e7ff, #c7d2fe);
        }
        
        .container { max-width: 1400px; margin: 0 auto; }
        
        /* Theme Toggle Button */
        .theme-toggle {
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 50px;
            padding: 8px 16px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .theme-toggle:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }
        
        body.light-mode .theme-toggle {
            background: rgba(0,0,0,0.1);
            color: #1e293b;
        }
        
        .header {
            background: rgba(15, 25, 45, 0.8);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 20px 35px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.3s;
        }
        
        body.light-mode .header {
            background: rgba(255,255,255,0.9);
            border-color: rgba(0,0,0,0.05);
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #a5b4fc, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .logo p { color: rgba(255,255,255,0.5); font-size: 13px; }
        body.light-mode .logo p { color: rgba(0,0,0,0.5); }
        
        .datetime { text-align: right; background: rgba(255,255,255,0.05); padding: 10px 20px; border-radius: 20px; }
        .date { font-size: 13px; color: rgba(255,255,255,0.6); }
        .time { font-size: 24px; font-weight: 700; color: white; font-family: monospace; }
        body.light-mode .date, body.light-mode .time { color: #1e293b; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(15, 25, 45, 0.7);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .stat-card:hover { transform: translateY(-5px); background: rgba(15, 25, 45, 0.85); }
        .stat-icon { font-size: 35px; }
        .stat-title { font-size: 11px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
        .stat-value { font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #fff, #a5b4fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        body.light-mode .stat-card {
            background: rgba(255,255,255,0.8);
            border-color: rgba(0,0,0,0.05);
        }
        body.light-mode .stat-title { color: rgba(0,0,0,0.5); }
        
        .room-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .room-tab {
            background: rgba(255,255,255,0.08);
            padding: 12px 25px;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.1);
            font-weight: 500;
        }
        
        .room-tab:hover { background: rgba(139, 92, 246, 0.4); transform: translateY(-2px); }
        .room-tab.active { background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 5px 15px rgba(99,102,241,0.3); }
        
        body.light-mode .room-tab {
            background: rgba(0,0,0,0.05);
            color: #1e293b;
            border-color: rgba(0,0,0,0.1);
        }
        body.light-mode .room-tab.active { color: white; }
        
        .room-container { border-radius: 30px; overflow: hidden; margin-bottom: 30px; transition: all 0.3s; }
        .room-visual {
            position: relative;
            min-height: 420px;
            transition: all 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 30px;
        }
        .room-visual.light-on { background: linear-gradient(135deg, #fff9e6, #fff3cc); box-shadow: 0 0 40px rgba(255, 200, 0, 0.3); }
        .room-visual.light-off { background: linear-gradient(135deg, #1a1a2e, #0f0f1a); }
        .room-content { text-align: center; padding: 40px 30px; }
        .room-name-large { font-size: 32px; font-weight: 800; margin-bottom: 15px; }
        .room-visual.light-on .room-name-large { color: #5c3d00; }
        .room-visual.light-off .room-name-large { color: rgba(255,255,255,0.3); }
        
        .room-icon-group {
            display: flex;
            gap: 40px;
            justify-content: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .device-icon-box {
            text-align: center;
            min-width: 80px;
            transition: all 0.3s;
        }
        
        .device-icon-box span {
            display: block;
            font-size: 12px;
            margin-top: 8px;
            font-weight: 500;
        }
        
        .room-visual.light-on .device-icon-box span { color: #5c3d00; }
        .room-visual.light-off .device-icon-box span { color: rgba(255,255,255,0.4); }
        
        /* Premium Animations */
        .fan-spinning { animation: spinFan 0.4s linear infinite; display: inline-block; }
        @keyframes spinFan { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        .tv-glowing { animation: tvGlowAnim 1s ease-in-out infinite alternate; }
        @keyframes tvGlowAnim { from { text-shadow: 0 0 5px cyan; } to { text-shadow: 0 0 20px cyan; } }
        
        .exhaust-spinning { animation: spinExhaust 0.3s linear infinite; display: inline-block; }
        @keyframes spinExhaust { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        .microwave-on { animation: microwavePulse 0.5s ease-in-out infinite alternate; display: inline-block; }
        @keyframes microwavePulse { from { opacity: 0.6; } to { opacity: 1; text-shadow: 0 0 10px orange; } }
        
        /* AC Premium Animation */
        @keyframes acGlow {
            0% { text-shadow: 0 0 5px rgba(0, 212, 255, 0.5); transform: scale(1); }
            100% { text-shadow: 0 0 20px rgba(0, 212, 255, 0.8); transform: scale(1.05); }
        }
        
        .ac-on {
            color: #00d4ff !important;
            text-shadow: 0 0 15px cyan !important;
            animation: acGlow 0.8s ease-in-out infinite alternate !important;
        }
        
        .room-controls { display: flex; gap: 12px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
        .btn-room { 
            padding: 10px 18px; 
            border: none; 
            border-radius: 50px; 
            font-size: 12px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .btn-room:active { transform: scale(0.95); }
        .btn-on { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .btn-off { background: linear-gradient(135deg, #4b5563, #374151); color: white; }
        .btn-room:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        
        .devices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .device-card {
            background: rgba(15, 25, 45, 0.7);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.3s;
        }
        
        .device-card:hover { transform: translateY(-5px); background: rgba(15, 25, 45, 0.85); }
        .device-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .device-icon { font-size: 40px; }
        .device-status { padding: 5px 15px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-on, .status-open { background: #10b981; color: white; }
        .status-off, .status-closed { background: #ef4444; color: white; }
        .device-name { font-size: 18px; font-weight: 700; color: white; margin-bottom: 15px; }
        .control-buttons { display: flex; gap: 12px; }
        .btn { flex: 1; padding: 10px; border-radius: 12px; font-size: 13px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn:active { transform: scale(0.95); }
        
        .voice-section {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
            backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        
        .voice-container { display: flex; gap: 15px; flex-wrap: wrap; }
        .voice-input { flex: 1; padding: 15px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; color: white; outline: none; transition: all 0.3s; }
        .voice-input:focus { border-color: #8b5cf6; box-shadow: 0 0 15px rgba(139,92,246,0.3); }
        .voice-btn { background: rgba(255,255,255,0.1); padding: 14px 25px; border-radius: 50px; color: white; cursor: pointer; border: none; transition: all 0.3s; }
        .voice-btn:hover { background: rgba(255,255,255,0.2); transform: scale(1.02); }
        .voice-btn.listening { background: #ef4444; animation: pulse 1.5s infinite; }
        
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); } 50% { box-shadow: 0 0 0 15px rgba(239,68,68,0); } }
        
        .voice-commands-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .command-chip { background: rgba(255,255,255,0.08); padding: 8px 18px; border-radius: 40px; font-size: 12px; cursor: pointer; transition: all 0.3s; color: rgba(255,255,255,0.7); }
        .command-chip:hover { background: rgba(139, 92, 246, 0.4); color: white; transform: translateY(-2px); }
        
        .scenes-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        
        .activity-section {
            background: rgba(15, 25, 45, 0.7);
            backdrop-filter: blur(15px);
            border-radius: 28px;
            padding: 25px;
            margin-top: 25px;
            transition: all 0.3s;
        }
        
        .section-title { font-size: 20px; font-weight: 700; color: white; margin-bottom: 15px; }
        .activity-list { max-height: 250px; overflow-y: auto; }
        .activity-list::-webkit-scrollbar { width: 5px; }
        .activity-list::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
        .activity-list::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.5); border-radius: 10px; }
        .activity-item { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,0.7); transition: all 0.3s; }
        .activity-item:hover { background: rgba(255,255,255,0.03); border-radius: 12px; }
        
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(0,0,0,0.95);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 50px;
            display: none;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease;
            font-weight: 500;
        }
        
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }
        
        @media (max-width: 768px) {
            .room-icon-group { gap: 20px; }
            .room-name-large { font-size: 24px; }
            .room-content { padding: 30px 20px; }
            .device-icon-box { min-width: 60px; }
            .device-icon-box div { font-size: 40px !important; }
            .header { flex-direction: column; text-align: center; gap: 15px; }
            .datetime { text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $check_microwave = $conn->query("SELECT * FROM devices WHERE name = 'Microwave'");
        if($check_microwave->num_rows == 0) {
            $conn->query("INSERT INTO devices (name, status) VALUES ('Microwave', 'OFF')");
        }
        $check_exhaust = $conn->query("SELECT * FROM devices WHERE name = 'Kitchen Exhaust Fan'");
        if($check_exhaust->num_rows == 0) {
            $conn->query("INSERT INTO devices (name, status) VALUES ('Kitchen Exhaust Fan', 'OFF')");
        }
        $total = $conn->query("SELECT COUNT(*) as c FROM devices")->fetch_assoc()['c'];
        $active = $conn->query("SELECT COUNT(*) as c FROM devices WHERE status IN ('ON','OPEN')")->fetch_assoc()['c'];
        $power = $active * 50;
        $cost = round(($power / 1000) * 30, 2);
        ?>
        
        <div class="header">
            <div class="logo">
                <h1><i class="fas fa-microchip"></i> SmartHome Pro</h1>
                <p>Premium Smart Home Control</p>
            </div>
            <div class="datetime">
                <div class="date" id="currentDate"></div>
                <div class="time" id="currentTime"></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-home"></i></div>
                <div class="stat-title">Total Devices</div>
                <div class="stat-value"><?php echo $total; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-power-off"></i></div>
                <div class="stat-title">Active Devices</div>
                <div class="stat-value" id="activeCount"><?php echo $active; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bolt"></i></div>
                <div class="stat-title">Power Usage</div>
                <div class="stat-value" id="powerUsage"><?php echo $power; ?>W</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="stat-title">Energy Cost/hr</div>
                <div class="stat-value" id="energyCost">PKR <?php echo $cost; ?></div>
            </div>
        </div>

        <div class="room-selector">
            <div class="room-tab active" data-room="living_room">🛋️ Living Room</div>
            <div class="room-tab" data-room="bedroom">🛏️ Bedroom</div>
            <div class="room-tab" data-room="kitchen">🍳 Kitchen</div>
        </div>

        <!-- LIVING ROOM -->
        <?php
        $living_light = $conn->query("SELECT status FROM devices WHERE name = 'Living Room Light'")->fetch_assoc()['status'];
        $living_tv = $conn->query("SELECT status FROM devices WHERE name = 'TV'")->fetch_assoc()['status'];
        $living_ac = $conn->query("SELECT status FROM devices WHERE name = 'Living Room AC'")->fetch_assoc()['status'];
        $living_status = ($living_light == 'ON') ? 'on' : 'off';
        ?>
        <div id="living_room" class="room-container">
            <div class="room-visual light-<?php echo $living_status; ?>" id="livingRoomVisual">
                <div class="room-content">
                    <div class="room-name-large">🛋️ Living Room</div>
                    <div class="room-icon-group">
                        <div class="device-icon-box">
                            <div id="livingLightIcon" style="font-size: 55px;"><i class="fas fa-lightbulb"></i></div>
                            <span id="livingLightText">Light: <?php echo $living_light; ?></span>
                        </div>
                        <div class="device-icon-box">
                            <div id="livingTvIcon" class="<?php echo ($living_tv == 'ON') ? 'tv-glowing' : ''; ?>" style="font-size: 55px;"><i class="fas fa-tv"></i></div>
                            <span id="livingTvText">TV: <?php echo $living_tv; ?></span>
                        </div>
                        <div class="device-icon-box">
                            <div id="livingAcIcon" class="<?php echo ($living_ac == 'ON') ? 'ac-on' : ''; ?>" style="font-size: 55px;"><i class="fas fa-snowflake"></i></div>
                            <span id="livingAcText">AC: <?php echo $living_ac; ?></span>
                        </div>
                    </div>
                    <div class="room-controls">
                        <button onclick="controlRoomDevice('Living Room Light', 'ON', 'livingLightText', 'livingRoomVisual', 'light')" class="btn-room btn-on">💡 Light ON</button>
                        <button onclick="controlRoomDevice('Living Room Light', 'OFF', 'livingLightText', 'livingRoomVisual', 'light')" class="btn-room btn-off">💡 Light OFF</button>
                        <button onclick="controlRoomDevice('TV', 'ON', 'livingTvText', null, 'tv')" class="btn-room btn-on">📺 TV ON</button>
                        <button onclick="controlRoomDevice('TV', 'OFF', 'livingTvText', null, 'tv')" class="btn-room btn-off">📺 TV OFF</button>
                        <button onclick="controlRoomDevice('Living Room AC', 'ON', 'livingAcText', null, 'ac')" class="btn-room btn-on">❄️ AC ON</button>
                        <button onclick="controlRoomDevice('Living Room AC', 'OFF', 'livingAcText', null, 'ac')" class="btn-room btn-off">❄️ AC OFF</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- BEDROOM -->
        <?php
        $bedroom_light = $conn->query("SELECT status FROM devices WHERE name = 'Bedroom Light'")->fetch_assoc()['status'];
        $bedroom_fan = $conn->query("SELECT status FROM devices WHERE name = 'Bedroom Fan'")->fetch_assoc()['status'];
        $door_status = $conn->query("SELECT status FROM devices WHERE name = 'Door'")->fetch_assoc()['status'];
        $bedroom_status = ($bedroom_light == 'ON') ? 'on' : 'off';
        ?>
        <div id="bedroom" class="room-container" style="display: none;">
            <div class="room-visual light-<?php echo $bedroom_status; ?>" id="bedroomRoomVisual">
                <div class="room-content">
                    <div class="room-name-large">🛏️ Bedroom</div>
                    <div class="room-icon-group">
                        <div class="device-icon-box">
                            <div id="bedroomLightIcon" style="font-size: 55px;"><i class="fas fa-lightbulb"></i></div>
                            <span id="bedroomLightText">Light: <?php echo $bedroom_light; ?></span>
                        </div>
                        <div class="device-icon-box">
                            <div id="bedroomFanIcon" class="<?php echo ($bedroom_fan == 'ON') ? 'fan-spinning' : ''; ?>" style="font-size: 55px;"><i class="fas fa-fan"></i></div>
                            <span id="bedroomFanText">Fan: <?php echo $bedroom_fan; ?></span>
                        </div>
                        <div class="device-icon-box">
                            <div id="doorIcon" style="font-size: 55px;"><i class="fas <?php echo ($door_status == 'OPEN') ? 'fa-door-open' : 'fa-door-closed'; ?>"></i></div>
                            <span id="doorText">Door: <?php echo $door_status; ?></span>
                        </div>
                    </div>
                    <div class="room-controls">
                        <button onclick="controlRoomDevice('Bedroom Light', 'ON', 'bedroomLightText', 'bedroomRoomVisual', 'light')" class="btn-room btn-on">💡 Light ON</button>
                        <button onclick="controlRoomDevice('Bedroom Light', 'OFF', 'bedroomLightText', 'bedroomRoomVisual', 'light')" class="btn-room btn-off">💡 Light OFF</button>
                        <button onclick="controlRoomDevice('Bedroom Fan', 'ON', 'bedroomFanText', null, 'fan')" class="btn-room btn-on">🌀 Fan ON</button>
                        <button onclick="controlRoomDevice('Bedroom Fan', 'OFF', 'bedroomFanText', null, 'fan')" class="btn-room btn-off">🌀 Fan OFF</button>
                        <button onclick="controlRoomDevice('Door', 'OPEN', 'doorText', null, 'door')" class="btn-room btn-on">🚪 Door Open</button>
                        <button onclick="controlRoomDevice('Door', 'CLOSED', 'doorText', null, 'door')" class="btn-room btn-off">🚪 Door Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- KITCHEN -->
        <?php
        $kitchen_light = $conn->query("SELECT status FROM devices WHERE name = 'Kitchen Light'")->fetch_assoc()['status'];
        $exhaust_fan = $conn->query("SELECT status FROM devices WHERE name = 'Kitchen Exhaust Fan'")->fetch_assoc()['status'];
        $microwave = $conn->query("SELECT status FROM devices WHERE name = 'Microwave'")->fetch_assoc()['status'];
        $kitchen_status = ($kitchen_light == 'ON') ? 'on' : 'off';
        ?>
        <div id="kitchen" class="room-container" style="display: none;">
            <div class="room-visual light-<?php echo $kitchen_status; ?>" id="kitchenRoomVisual">
                <div class="room-content">
                    <div class="room-name-large">🍳 Kitchen</div>
                    <div class="room-icon-group">
                        <div class="device-icon-box">
                            <div id="kitchenLightIcon" style="font-size: 55px;"><i class="fas fa-lightbulb"></i></div>
                            <span id="kitchenLightText">Light: <?php echo $kitchen_light; ?></span>
                        </div>
                        <div class="device-icon-box">
                            <div id="exhaustFanIcon" class="<?php echo ($exhaust_fan == 'ON') ? 'exhaust-spinning' : ''; ?>" style="font-size: 55px;"><i class="fas fa-fan"></i></div>
                            <span id="exhaustFanText">Exhaust: <?php echo $exhaust_fan; ?></span>
                        </div>
                        <div class="device-icon-box">
                            <div id="microwaveIcon" class="<?php echo ($microwave == 'ON') ? 'microwave-on' : ''; ?>" style="font-size: 55px;"><i class="fas fa-microchip"></i></div>
                            <span id="microwaveText">Microwave: <?php echo $microwave; ?></span>
                        </div>
                    </div>
                    <div class="room-controls">
                        <button onclick="controlRoomDevice('Kitchen Light', 'ON', 'kitchenLightText', 'kitchenRoomVisual', 'light')" class="btn-room btn-on">💡 Light ON</button>
                        <button onclick="controlRoomDevice('Kitchen Light', 'OFF', 'kitchenLightText', 'kitchenRoomVisual', 'light')" class="btn-room btn-off">💡 Light OFF</button>
                        <button onclick="controlRoomDevice('Kitchen Exhaust Fan', 'ON', 'exhaustFanText', null, 'exhaust')" class="btn-room btn-on">🌀 Exhaust ON</button>
                        <button onclick="controlRoomDevice('Kitchen Exhaust Fan', 'OFF', 'exhaustFanText', null, 'exhaust')" class="btn-room btn-off">🌀 Exhaust OFF</button>
                        <button onclick="controlRoomDevice('Microwave', 'ON', 'microwaveText', null, 'microwave')" class="btn-room btn-on">🔴 Microwave ON</button>
                        <button onclick="controlRoomDevice('Microwave', 'OFF', 'microwaveText', null, 'microwave')" class="btn-room btn-off">🔴 Microwave OFF</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voice Control -->
        <div class="voice-section">
            <div class="section-title" style="color: white;"><i class="fas fa-microphone"></i> Voice Command</div>
            <div class="voice-container">
                <input type="text" id="voiceText" class="voice-input" placeholder="Type or speak a command...">
                <button id="voiceBtn" class="voice-btn" onclick="startVoiceRecognition()"><i class="fas fa-microphone"></i> Speak</button>
                <button onclick="processVoiceCommand()" class="voice-btn" style="background: #10b981;"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
            <div class="voice-commands-list">
                <span class="command-chip" onclick="setCommand('light on')">💡 Light ON</span>
                <span class="command-chip" onclick="setCommand('light off')">💡 Light OFF</span>
                <span class="command-chip" onclick="setCommand('fan on')">🌀 Fan ON</span>
                <span class="command-chip" onclick="setCommand('fan off')">🌀 Fan OFF</span>
                <span class="command-chip" onclick="setCommand('door open')">🚪 Door Open</span>
                <span class="command-chip" onclick="setCommand('door close')">🚪 Door Close</span>
                <span class="command-chip" onclick="setCommand('morning')">🌅 Good Morning</span>
                <span class="command-chip" onclick="setCommand('night')">🌙 Good Night</span>
            </div>
        </div>

        <!-- Other Devices -->
        <div class="devices-grid">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM devices WHERE name NOT IN ('Living Room Light', 'Bedroom Light', 'Kitchen Light', 'Living Room AC', 'Bedroom Fan', 'TV', 'Door', 'Kitchen Exhaust Fan', 'Microwave') ORDER BY id");
            while($row = mysqli_fetch_assoc($result)){
                $status_class = ($row['status'] == 'ON' || $row['status'] == 'OPEN') ? 'status-on' : 'status-off';
                ?>
                <div class="device-card">
                    <div class="device-header">
                        <div class="device-icon"><i class="fas fa-microchip"></i></div>
                        <div class="device-status <?php echo $status_class; ?>"><?php echo $row['status']; ?></div>
                    </div>
                    <div class="device-name"><?php echo $row['name']; ?></div>
                    <div class="control-buttons">
                        <a href="javascript:void(0)" onclick="controlDevice(<?php echo $row['id']; ?>, 'ON')" class="btn btn-primary">ON</a>
                        <a href="javascript:void(0)" onclick="controlDevice(<?php echo $row['id']; ?>, 'OFF')" class="btn btn-danger">OFF</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Smart Scenes -->
        <div class="activity-section">
            <div class="section-title"><i class="fas fa-magic"></i> Smart Scenes</div>
            <div class="scenes-grid">
                <button onclick="applyScene('good_morning')" class="btn btn-primary">🌅 Good Morning</button>
                <button onclick="applyScene('good_night')" class="btn btn-danger">🌙 Good Night</button>
                <button onclick="applyScene('movie_time')" class="btn btn-warning">🎬 Movie Time</button>
                <button onclick="applyScene('away')" class="btn btn-secondary">🚪 Away Mode</button>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="activity-section">
            <div class="section-title"><i class="fas fa-history"></i> Recent Activity</div>
            <div class="activity-list">
                <?php
                $activities = $conn->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 6");
                while($act = $activities->fetch_assoc()):
                ?>
                <div class="activity-item">
                    <i class="fas <?php echo ($act['new_status'] == 'ON' || $act['new_status'] == 'OPEN') ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></i>
                    <span><strong><?php echo $act['device_name']; ?></strong> → <?php echo $act['new_status']; ?></span>
                    <span style="margin-left: auto; font-size: 11px;"><?php echo date('h:i A', strtotime($act['created_at'])); ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle" style="color: #10b981;"></i>
        <span id="toastMsg">Updated!</span>
    </div>

    <script>
        // Date/Time
        function updateDateTime() {
            const now = new Date();
            document.getElementById('currentDate').innerHTML = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('currentTime').innerHTML = now.toLocaleTimeString('en-US');
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Room switching
        function switchRoom(roomId) {
            document.querySelectorAll('.room-container').forEach(c => c.style.display = 'none');
            document.getElementById(roomId).style.display = 'block';
            localStorage.setItem('currentRoom', roomId);
            document.querySelectorAll('.room-tab').forEach(tab => {
                tab.classList.remove('active');
                if(tab.getAttribute('data-room') === roomId) tab.classList.add('active');
            });
        }
        
        document.querySelectorAll('.room-tab').forEach(tab => {
            tab.addEventListener('click', function() { switchRoom(this.dataset.room); });
        });
        
        if(localStorage.getItem('currentRoom') && document.getElementById(localStorage.getItem('currentRoom'))) {
            switchRoom(localStorage.getItem('currentRoom'));
        }

        // Get device ID
        function getDeviceId(name) {
            <?php
            $all = $conn->query("SELECT id, name FROM devices");
            while($d = $all->fetch_assoc()) {
                echo "if(name === '{$d['name']}') return {$d['id']}; ";
            }
            ?>
            return 0;
        }

        // Show loading on button
        function showLoading(btn) {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading-spinner"></span> Loading...';
            btn.disabled = true;
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 800);
        }

        // Control Room Device with Premium Effects
        function controlRoomDevice(deviceName, status, textSpanId, visualId, type) {
            const btns = event.target.closest('.room-controls').querySelectorAll('.btn-room');
            const clickedBtn = event.target;
            showLoading(clickedBtn);
            
            fetch('control_ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + getDeviceId(deviceName) + '&status=' + status
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast(data.message, 'success');
                    
                    // Update text
                    if(textSpanId) {
                        document.getElementById(textSpanId).innerHTML = deviceName.split(' ').pop() + ': ' + status;
                    }
                    
                    // Visual effects
                    if(type === 'light' && visualId) {
                        const visual = document.getElementById(visualId);
                        if(status === 'ON') {
                            visual.classList.remove('light-off');
                            visual.classList.add('light-on');
                        } else {
                            visual.classList.remove('light-on');
                            visual.classList.add('light-off');
                        }
                    }
                    else if(type === 'tv') {
                        const tvIcon = document.getElementById('livingTvIcon');
                        if(status === 'ON') {
                            tvIcon.classList.add('tv-glowing');
                        } else {
                            tvIcon.classList.remove('tv-glowing');
                        }
                    }
                    else if(type === 'ac') {
                        const acIcon = document.getElementById('livingAcIcon');
                        if(status === 'ON') {
                            acIcon.classList.add('ac-on');
                        } else {
                            acIcon.classList.remove('ac-on');
                        }
                    }
                    else if(type === 'fan') {
                        const fanIcon = document.getElementById('bedroomFanIcon');
                        if(status === 'ON') {
                            fanIcon.classList.add('fan-spinning');
                        } else {
                            fanIcon.classList.remove('fan-spinning');
                        }
                    }
                    else if(type === 'door') {
                        const doorIcon = document.getElementById('doorIcon');
                        if(status === 'OPEN') {
                            doorIcon.innerHTML = '<i class="fas fa-door-open"></i>';
                        } else {
                            doorIcon.innerHTML = '<i class="fas fa-door-closed"></i>';
                        }
                    }
                    else if(type === 'exhaust') {
                        const exhaustIcon = document.getElementById('exhaustFanIcon');
                        if(status === 'ON') {
                            exhaustIcon.classList.add('exhaust-spinning');
                        } else {
                            exhaustIcon.classList.remove('exhaust-spinning');
                        }
                    }
                    else if(type === 'microwave') {
                        const microwaveIcon = document.getElementById('microwaveIcon');
                        if(status === 'ON') {
                            microwaveIcon.classList.add('microwave-on');
                        } else {
                            microwaveIcon.classList.remove('microwave-on');
                        }
                    }
                    
                    updateStats();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.log('Error:', error);
                showToast('Network error', 'error');
            });
        }

        // Update statistics without reload
        function updateStats() {
            fetch('get_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('activeCount').innerHTML = data.active;
                    document.getElementById('powerUsage').innerHTML = (data.active * 50) + 'W';
                    document.getElementById('energyCost').innerHTML = 'PKR ' + ((data.active * 50 / 1000) * 30).toFixed(2);
                })
                .catch(err => console.log('Stats error:', err));
        }

        // Control other devices
        function controlDevice(id, status) {
            const btn = event.target;
            showLoading(btn);
            
            fetch('control_ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id + '&status=' + status
            })
            .then(response => response.json())
            .then(data => { 
                if(data.success) { 
                    showToast(data.message, 'success');
                    updateStats();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
        }

        // Premium Toast with colors
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = toast.querySelector('i');
            toastMsg = document.getElementById('toastMsg');
            
            toastMsg.innerText = msg;
            
            if(type === 'error') {
                icon.style.color = '#ef4444';
                icon.className = 'fas fa-exclamation-circle';
                toast.style.background = 'rgba(239,68,68,0.95)';
            } else {
                icon.style.color = '#10b981';
                icon.className = 'fas fa-check-circle';
                toast.style.background = 'rgba(0,0,0,0.95)';
            }
            
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
                toast.style.background = 'rgba(0,0,0,0.95)';
            }, 2500);
        }

        // Voice Recognition
        let recognition, isListening = false;
        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.lang = 'en-US';
            recognition.onresult = function(e) {
                document.getElementById('voiceText').value = e.results[0][0].transcript;
                processVoiceCommand();
                stopListening();
            };
            recognition.onerror = function() { stopListening(); };
        }

        function startVoiceRecognition() {
            if(!recognition) { showToast('Use Chrome for voice', 'error'); return; }
            if(isListening) { stopListening(); return; }
            isListening = true;
            document.getElementById('voiceBtn').classList.add('listening');
            document.getElementById('voiceBtn').innerHTML = '<i class="fas fa-microphone-slash"></i> Listening...';
            recognition.start();
        }
        
        function stopListening() {
            isListening = false;
            document.getElementById('voiceBtn').classList.remove('listening');
            document.getElementById('voiceBtn').innerHTML = '<i class="fas fa-microphone"></i> Speak';
        }
        
        function setCommand(cmd) { 
            document.getElementById('voiceText').value = cmd; 
            processVoiceCommand(); 
        }
        
        function processVoiceCommand() {
            const cmd = document.getElementById('voiceText').value;
            if(!cmd) { showToast('Please say or type a command', 'error'); return; }
            
            showToast('Processing: "' + cmd + '"', 'info');
            
            fetch('voice_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'command=' + encodeURIComponent(cmd)
            })
            .then(r => r.json())
            .then(d => { 
                showToast(d.message, d.success ? 'success' : 'error');
                if(d.success) {
                    setTimeout(() => location.reload(), 1200);
                }
            });
        }
        
        function applyScene(scene) {
            showToast('Applying ' + scene.replace('_', ' ') + ' scene...', 'info');
            
            fetch('scene_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'scene=' + scene
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'error');
                if(data.success) {
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Scene error:', error);
                showToast('Error applying scene', 'error');
            });
        }
    </script>
</body>
</html>