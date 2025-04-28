<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distance Matrix - Theretowhere</title>
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
        
        /* Distance matrix styles */
        .matrix-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .matrix-intro {
            margin-bottom: 30px;
        }
        
        .matrix-intro h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .matrix-intro p {
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
        
        .locations-container {
            margin-bottom: 20px;
        }
        
        .location-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .location-item input {
            flex: 1;
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
        
        .btn-sm {
            font-size: 14px;
            padding: 8px 15px;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #9c27b0;
            color: #9c27b0;
        }
        
        .btn-outline:hover {
            background-color: #f3e5f5;
        }
        
        .btn-remove {
            background-color: #f44336;
            color: white;
        }
        
        .btn-remove:hover {
            background-color: #d32f2f;
        }
        
        .matrix-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .matrix-results {
            margin-top: 40px;
            display: none;
        }
        
        .matrix-results.active {
            display: block;
        }
        
        .matrix-results h3 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .results-table th, .results-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .results-table th {
            background-color: #f5f5f5;
            font-weight: 500;
        }
        
        .results-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .map-container {
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 30px;
        }
        
        /* How it works section */
        .how-it-works {
            margin-bottom: 60px;
        }
        
        .how-it-works h2 {
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .step-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            text-align: center;
        }
        
        .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: #9c27b0;
            color: white;
            border-radius: 50%;
            line-height: 40px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .step-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .step-card p {
            color: #666;
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
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">My Dashboard</a>
                    <a href="logout.php">Sign out</a>
                <?php else: ?>
                    <a href="login.php">Sign in</a> / <a href="signup.php">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Distance Matrix</h1>
            <p>Calculate travel times between multiple locations at once</p>
        </div>
    </section>
    
    <!-- Distance Matrix Tool -->
    <section class="container">
        <div class="matrix-container">
            <div class="matrix-intro">
                <h2>Calculate Travel Times</h2>
                <p>Enter your starting location and multiple destinations to see travel times and distances between them.</p>
                <p>Perfect for comparing commute times to work, friends, favorite spots, and more.</p>
            </div>
            
            <form id="matrixForm">
                <div class="form-group">
                    <label for="startLocation">Starting Location</label>
                    <input type="text" id="startLocation" class="form-control" placeholder="Enter your home or starting point">
                </div>
                
                <div class="form-group">
                    <label>Destinations</label>
                    <div class="locations-container" id="locationsContainer">
                        <div class="location-item">
                            <input type="text" class="form-control location-input" placeholder="Enter a destination (e.g., work, gym, etc.)">
                            <button type="button" class="btn btn-sm btn-remove location-remove">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" id="addLocationBtn">+ Add Another Location</button>
                </div>
                
                <div class="form-group">
                    <label for="transportMode">Transportation Mode</label>
                    <select id="transportMode" class="form-control">
                        <option value="driving">Driving</option>
                        <option value="walking">Walking</option>
                        <option value="bicycling">Bicycling</option>
                        <option value="transit">Public Transit</option>
                    </select>
                </div>
                
                <div class="matrix-actions">
                    <button type="button" class="btn" id="calculateBtn">Calculate Distances</button>
                    <button type="button" class="btn btn-outline" id="resetBtn">Reset Form</button>
                </div>
            </form>
            
            <div class="matrix-results" id="matrixResults">
                <h3>Travel Times & Distances</h3>
                <div class="table-responsive">
                    <table class="results-table" id="resultsTable">
                        <thead>
                            <tr>
                                <th>Destination</th>
                                <th>Travel Time</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Results will be populated here -->
                        </tbody>
                    </table>
                </div>
                
                <div class="map-container" id="map"></div>
                
                <?php if ($isLoggedIn): ?>
                    <div style="margin-top: 20px;">
                        <button type="button" class="btn btn-outline" id="saveResultsBtn">Save These Results</button>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 20px; text-align: center;">
                        <p>Want to save your results? <a href="login.php">Sign in</a> or <a href="signup.php">create an account</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section class="container how-it-works">
        <h2>How It Works</h2>
        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Enter Your Starting Point</h3>
                <p>Add your home address or any location you want to use as your starting point.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Add Multiple Destinations</h3>
                <p>Enter all the places you frequently visit: work, gym, friends' homes, favorite restaurants, etc.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Choose Transportation Mode</h3>
                <p>Select how you typically travel: driving, walking, biking, or public transit.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Get Instant Results</h3>
                <p>See travel times and distances to all your destinations at once, helping you make informed decisions.</p>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM elements
            const locationsContainer = document.getElementById('locationsContainer');
            const addLocationBtn = document.getElementById('addLocationBtn');
            const calculateBtn = document.getElementById('calculateBtn');
            const resetBtn = document.getElementById('resetBtn');
            const matrixResults = document.getElementById('matrixResults');
            const resultsTable = document.getElementById('resultsTable').getElementsByTagName('tbody')[0];
            const saveResultsBtn = document.getElementById('saveResultsBtn');
            
            // Add location event
            addLocationBtn.addEventListener('click', function() {
                const locationItem = document.createElement('div');
                locationItem.className = 'location-item';
                locationItem.innerHTML = `
                    <input type="text" class="form-control location-input" placeholder="Enter a destination (e.g., work, gym, etc.)">
                    <button type="button" class="btn btn-sm btn-remove location-remove">Remove</button>
                `;
                locationsContainer.appendChild(locationItem);
                
                // Add event listener to the new remove button
                const removeBtn = locationItem.querySelector('.location-remove');
                removeBtn.addEventListener('click', function() {
                    locationsContainer.removeChild(locationItem);
                });
            });
            
            // Remove location event (for initial location)
            document.querySelector('.location-remove').addEventListener('click', function() {
                if (locationsContainer.children.length > 1) {
                    this.parentElement.remove();
                }
            });
            
            // Calculate distances
            calculateBtn.addEventListener('click', function() {
                // Simulate calculation (in a real app, this would use the Google Maps Distance Matrix API)
                simulateDistanceCalculation();
            });
            
            // Reset form
            resetBtn.addEventListener('click', function() {
                document.getElementById('startLocation').value = '';
                
                // Remove all location items except the first one
                while (locationsContainer.children.length > 1) {
                    locationsContainer.removeChild(locationsContainer.lastChild);
                }
                
                // Clear the first location input
                locationsContainer.querySelector('.location-input').value = '';
                
                // Reset transport mode
                document.getElementById('transportMode').selectedIndex = 0;
                
                // Hide results
                matrixResults.classList.remove('active');
            });
            
            // Save results (only works when logged in)
            if (saveResultsBtn) {
                saveResultsBtn.addEventListener('click', function() {
                    alert('Your results have been saved!');
                    // In a real app, this would send the data to the server via AJAX
                });
            }
            
            // Initialize map (would be populated with real data in production)
            let map;
            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: 40.7128, lng: -74.0060}, // New York by default
                    zoom: 12
                });
            }
            
            // Simulate distance calculation (in production, use actual Google Maps Distance Matrix API)
            function simulateDistanceCalculation() {
                const startLocation = document.getElementById('startLocation').value;
                const locationInputs = document.querySelectorAll('.location-input');
                const transportMode = document.getElementById('transportMode').value;
                
                // Validate inputs
                if (!startLocation) {
                    alert('Please enter a starting location');
                    return;
                }
                
                let hasDestinations = false;
                locationInputs.forEach(input => {
                    if (input.value) hasDestinations = true;
                });
                
                if (!hasDestinations) {
                    alert('Please enter at least one destination');
                    return;
                }
                
                // Clear previous results
                resultsTable.innerHTML = '';
                
                // Generate random results for demonstration
                locationInputs.forEach(input => {
                    if (input.value) {
                        const row = resultsTable.insertRow();
                        
                        const destinationCell = row.insertCell(0);
                        const timeCell = row.insertCell(1);
                        const distanceCell = row.insertCell(2);
                        
                        // Generate random time (5-60 minutes)
                        const minutes = Math.floor(Math.random() * 56) + 5;
                        // Generate random distance (1-20 miles)
                        const miles = (Math.random() * 19 + 1).toFixed(1);
                        
                        destinationCell.textContent = input.value;
                        timeCell.textContent = `${minutes} mins`;
                        distanceCell.textContent = `${miles} miles`;
                    }
                });
                
                // Show results
                matrixResults.classList.add('active');
                
                // Initialize map
                initMap();
                
                // In a real app, markers would be added to the map for each location
            }
        });
    </script>
</body>
</html>
