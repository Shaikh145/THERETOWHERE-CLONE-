<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $city = $_POST['city'] ?? '';
    $preferences = $_POST['preferences'] ?? [];
    
    // Validate input
    if (empty($name) || empty($city) || empty($preferences)) {
        $message = '<div class="error-message">Please fill in all required fields</div>';
    } else {
        // Convert preferences array to JSON
        $preferencesJson = json_encode($preferences);
        
        // Insert heatmap
        $heatmapId = insertData(
            "INSERT INTO heatmaps (user_id, name, city, preferences) VALUES (?, ?, ?, ?)",
            [$userId, $name, $city, $preferencesJson]
        );
        
        if ($heatmapId) {
            $message = '<div class="success-message">Heatmap created successfully!</div>';
            // Redirect to view the new heatmap
            header("Location: view-heatmap.php?id=" . $heatmapId);
            exit;
        } else {
            $message = '<div class="error-message">An error occurred. Please try again.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Heatmap - Theretowhere</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        a {
            text-decoration: none;
            color: #0066cc;
        }
        
        /* Header styles */
        header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .auth-links a {
            margin-left: 15px;
            color: #0066cc;
        }
        
        /* Page header */
        .page-header {
            background-color: #9c27b0;
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        /* Form styles */
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .form-intro {
            margin-bottom: 30px;
        }
        
        .form-intro h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .form-intro p {
            color: #666;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #9c27b0;
            outline: none;
        }
        
        .preference-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .preference-info {
            flex: 1;
        }
        
        .preference-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .preference-info p {
            font-size: 14px;
            color: #666;
        }
        
        .preference-slider {
            width: 200px;
            margin: 0 15px;
        }
        
        .preference-value {
            width: 40px;
            text-align: center;
            font-weight: bold;
        }
        
        .btn {
            display: inline-block;
            background-color: #9c27b0;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #7b1fa2;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #9c27b0;
            color: #9c27b0;
        }
        
        .btn-outline:hover {
            background-color: #f3e5f5;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .error-message {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: #388e3c;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .map-preview {
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 30px;
            background-color: #eee;
        }
        
        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 30px 0;
            margin-top: 60px;
        }
        
        .footer-bottom {
            text-align: center;
            color: #bbb;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="index.php" class="logo">Theretowhere</a>
            <div class="auth-links">
                <a href="dashboard.php">My Dashboard</a>
                <a href="logout.php">Sign out</a>
            </div>
        </div>
    </header>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Create a Heatmap</h1>
            <p>Customize a heatmap based on your preferences</p>
        </div>
    </section>
    
    <!-- Create Heatmap Form -->
    <section class="container">
        <div class="form-container">
            <div class="form-intro">
                <h2>Customize Your Heatmap</h2>
                <p>Tell us what matters most to you, and we'll generate a heatmap showing the best neighborhoods based on your preferences.</p>
            </div>
            
            <?php echo $message; ?>
            
            <form action="create-heatmap.php" method="POST">
                <div class="form-group">
                    <label for="name">Heatmap Name*</label>
                    <input type="text" id="name" name="name" class="form-control" required placeholder="e.g., My NYC Commuter Map">
                </div>
                
                <div class="form-group">
                    <label for="city">City*</label>
                    <input type="text" id="city" name="city" class="form-control" required placeholder="e.g., New York, NY">
                </div>
                
                <div class="form-group">
                    <label>Set Your Preferences</label>
                    <p style="color: #666; margin-bottom: 15px;">Drag the sliders to indicate how important each factor is to you (0 = not important, 10 = very important)</p>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Commute Time</h3>
                            <p>Proximity to your workplace or frequent destinations</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[commute]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Public Transportation</h3>
                            <p>Access to buses, trains, and other public transit</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[transit]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Restaurants & Cafes</h3>
                            <p>Proximity to dining options</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[dining]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Parks & Green Spaces</h3>
                            <p>Access to parks, trails, and outdoor recreation</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[parks]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Schools</h3>
                            <p>Quality of nearby schools</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[schools]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Shopping</h3>
                            <p>Access to grocery stores, malls, and retail</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[shopping]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                    
                    <div class="preference-item">
                        <div class="preference-info">
                            <h3>Nightlife</h3>
                            <p>Proximity to bars, clubs, and entertainment</p>
                        </div>
                        <input type="range" class="preference-slider" name="preferences[nightlife]" min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">  min="0" max="10" value="5" oninput="this.nextElementSibling.value = this.value">
                        <output class="preference-value">5</output>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Generate Heatmap</button>
                    <button type="reset" class="btn btn-outline">Reset</button>
                </div>
            </form>
            
            <div class="map-preview" id="mapPreview">
                <!-- Map preview will be shown here after form submission -->
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Theretowhere. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Google Maps API (Replace YOUR_API_KEY with an actual key in production) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=visualization"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map preview
            let map;
            function initMap() {
                map = new google.maps.Map(document.getElementById('mapPreview'), {
                    center: {lat: 40.7128, lng: -74.0060}, // New York by default
                    zoom: 12
                });
            }
            
            // Initialize map on page load
            initMap();
            
            // Form submission handling
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                // In a real app, this would be handled by the server
                // This is just for preview purposes
                e.preventDefault();
                
                // Get form values
                const city = document.getElementById('city').value;
                
                // Update map center based on city (in a real app, use geocoding)
                let center = {lat: 40.7128, lng: -74.0060}; // Default to NYC
                if (city.toLowerCase().includes('san francisco')) {
                    center = {lat: 37.7749, lng: -122.4194};
                } else if (city.toLowerCase().includes('chicago')) {
                    center = {lat: 41.8781, lng: -87.6298};
                } else if (city.toLowerCase().includes('los angeles')) {
                    center = {lat: 34.0522, lng: -118.2437};
                }
                
                // Update map
                map.setCenter(center);
                
                // Create heatmap layer (simulated data)
                const heatmapData = [];
                for (let i = 0; i < 100; i++) {
                    const lat = center.lat + (Math.random() - 0.5) * 0.1;
                    const lng = center.lng + (Math.random() - 0.5) * 0.1;
                    const weight = Math.random() * 10;
                    heatmapData.push({
                        location: new google.maps.LatLng(lat, lng),
                        weight: weight
                    });
                }
                
                const heatmap = new google.maps.visualization.HeatmapLayer({
                    data: heatmapData,
                    map: map,
                    radius: 20
                });
                
                // Show success message
                const message = document.createElement('div');
                message.className = 'success-message';
                message.textContent = 'Heatmap preview generated! Submit to save.';
                form.insertBefore(message, form.querySelector('.form-actions'));
                
                // Scroll to map
                document.getElementById('mapPreview').scrollIntoView({behavior: 'smooth'});
            });
        });
    </script>
</body>
</html>
