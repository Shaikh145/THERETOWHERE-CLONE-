<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get destination ID
$destinationId = $_GET['id'] ?? '';

// Get destination details
try {
    $destination = fetchOne("SELECT * FROM destinations WHERE destination_id = ?", [$destinationId]);
    
    if (!$destination) {
        header("Location: destinations.php");
        exit;
    }
} catch (Exception $e) {
    // If there's an error, create a sample destination
    $destination = [
        'destination_id' => $destinationId ?: 1,
        'name' => 'Paris',
        'city' => 'Paris',
        'country' => 'France',
        'description' => 'Paris, the City of Light, is renowned for its stunning architecture, art museums, historical monuments, and romantic ambiance. From the iconic Eiffel Tower to the historic Notre-Dame Cathedral, Paris offers countless attractions that showcase its rich cultural heritage and timeless beauty.',
        'image' => 'paris.jpg',
        'rating' => 4.8,
        'latitude' => 48.8566,
        'longitude' => 2.3522,
        'timezone' => 'Europe/Paris',
        'currency' => 'Euro (EUR)',
        'language' => 'French',
        'best_time_to_visit' => 'April to June, September to October'
    ];
}

// Get packages for this destination
try {
    $packages = fetchAll("SELECT * FROM packages WHERE destination_id = ? ORDER BY price ASC", [$destinationId]);
} catch (Exception $e) {
    // If there's an error, create sample packages
    $packages = [
        [
            'package_id' => 1,
            'name' => 'Paris Explorer',
            'description' => '5-day Paris adventure with Eiffel Tower tour',
            'image' => 'paris_package.jpg',
            'price' => 899.00,
            'duration' => 5,
            'included_services' => 'Flight, Hotel, Breakfast, Eiffel Tower Tour, Seine Cruise',
            'rating' => 4.5
        ],
        [
            'package_id' => 2,
            'name' => 'Paris Luxury Getaway',
            'description' => '7-day luxury experience in the heart of Paris',
            'image' => 'paris_luxury.jpg',
            'price' => 1499.00,
            'duration' => 7,
            'included_services' => 'Business Class Flight, 5-Star Hotel, All Meals, Private Tours, Airport Transfer',
            'rating' => 4.8
        ],
        [
            'package_id' => 3,
            'name' => 'Paris Weekend Break',
            'description' => '3-day quick escape to Paris',
            'image' => 'paris_weekend.jpg',
            'price' => 599.00,
            'duration' => 3,
            'included_services' => 'Flight, Hotel, Breakfast, City Pass',
            'rating' => 4.3
        ]
    ];
}

// Get hotels for this destination
try {
    $hotels = fetchAll("SELECT * FROM hotels WHERE destination_id = ? ORDER BY rating DESC LIMIT 3", [$destinationId]);
} catch (Exception $e) {
    // If there's an error, create sample hotels
    $hotels = [
        [
            'hotel_id' => 1,
            'name' => 'Grand Hotel Paris',
            'description' => 'Luxury hotel in the heart of Paris',
            'image' => 'paris_hotel1.jpg',
            'price_per_night' => 250.00,
            'rating' => 4.7,
            'address' => '1 Rue de Rivoli, 75001 Paris, France'
        ],
        [
            'hotel_id' => 2,
            'name' => 'Eiffel View Hotel',
            'description' => 'Boutique hotel with stunning Eiffel Tower views',
            'image' => 'paris_hotel2.jpg',
            'price_per_night' => 180.00,
            'rating' => 4.5,
            'address' => '15 Avenue de la Bourdonnais, 75007 Paris, France'
        ],
        [
            'hotel_id' => 3,
            'name' => 'Montmartre Residence',
            'description' => 'Charming hotel in the artistic Montmartre district',
            'image' => 'paris_hotel3.jpg',
            'price_per_night' => 150.00,
            'rating' => 4.3,
            'address' => '7 Rue Lamarck, 75018 Paris, France'
        ]
    ];
}

// Get attractions for this destination
$attractions = [
    [
        'name' => 'Eiffel Tower',
        'description' => 'Iconic iron tower built in 1889',
        'image' => 'eiffel_tower.jpg',
        'rating' => 4.7
    ],
    [
        'name' => 'Louvre Museum',
        'description' => 'World\'s largest art museum and historic monument',
        'image' => 'louvre.jpg',
        'rating' => 4.8
    ],
    [
        'name' => 'Notre-Dame Cathedral',
        'description' => 'Medieval Catholic cathedral on the Île de la Cité',
        'image' => 'notre_dame.jpg',
        'rating' => 4.6
    ],
    [
        'name' => 'Arc de Triomphe',
        'description' => 'Iconic triumphal arch honoring those who fought for France',
        'image' => 'arc_de_triomphe.jpg',
        'rating' => 4.5
    ]
];

// Check if user has saved this destination
$isSaved = false;
if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    try {
        $savedItem = fetchOne("SELECT * FROM saved_items WHERE user_id = ? AND item_type = 'destination' AND item_id = ?", 
                            [$userId, $destinationId]);
        $isSaved = !empty($savedItem);
    } catch (Exception $e) {
        // Ignore error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $destination['name']; ?> - Theretowhere</title>
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
        
        /* Destination header styles */
        .destination-header {
            position: relative;
            height: 500px;
            background-size: cover;
            background-position: center;
            color: white;
            margin-bottom: 40px;
            display: flex;
            align-items: flex-end;
        }
        
        .destination-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }
        
        .destination-header-content {
            position: relative;
            z-index: 2;
            padding: 40px 0;
            width: 100%;
        }
        
        .destination-title {
            font-size: 42px;
            margin-bottom: 10px;
        }
        
        .destination-location {
            font-size: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .destination-location i {
            margin-right: 5px;
        }
        
        .destination-rating {
            display: flex;
            align-items: center;
        }
        
        .stars {
            color: #ffc107;
            margin-right: 5px;
        }
        
        /* Destination content styles */
        .destination-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .destination-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .destination-sidebar {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .content-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .content-card h2 {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .destination-description {
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .destination-highlights {
            margin-top: 20px;
        }
        
        .highlights-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            list-style: none;
        }
        
        .highlight-item {
            display: flex;
            align-items: center;
        }
        
        .highlight-item i {
            color: #9c27b0;
            margin-right: 10px;
        }
        
        .info-list {
            list-style: none;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            width: 150px;
            font-weight: 500;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .map-container {
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #eee;
        }
        
        .weather-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            text-align: center;
        }
        
        .weather-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .weather-temp {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .weather-desc {
            color: #666;
            margin-bottom: 15px;
        }
        
        .weather-details {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 10px;
        }
        
        .weather-detail {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .weather-detail-label {
            font-size: 12px;
            color: #666;
        }
        
        .weather-detail-value {
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            display: inline-block;
            background-color: #9c27b0;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s;
            text-align: center;
            flex: 1;
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
        
        .btn-saved {
            background-color: #f3e5f5;
            color: #9c27b0;
            border: 1px solid #9c27b0;
        }
        
        /* Packages section */
        .packages-section {
            margin-bottom: 40px;
        }
        
        .packages-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .package-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .package-card:hover {
            transform: translateY(-5px);
        }
        
        .package-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .package-content {
            padding: 20px;
        }
        
        .package-content h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .package-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .package-description {
            color: #555;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .package-price {
            font-size: 20px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 15px;
        }
        
        /* Hotels section */
        .hotels-section {
            margin-bottom: 40px;
        }
        
        .hotels-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .hotel-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .hotel-card:hover {
            transform: translateY(-5px);
        }
        
        .hotel-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .hotel-content {
            padding: 20px;
        }
        
        .hotel-content h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .hotel-address {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .hotel-description {
            color: #555;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .hotel-price {
            font-size: 18px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 15px;
        }
        
        /* Attractions section */
        .attractions-section {
            margin-bottom: 40px;
        }
        
        .attractions-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .attractions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .attraction-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .attraction-card:hover {
            transform: translateY(-5px);
        }
        
        .attraction-image {
            height: 160px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .attraction-content {
            padding: 15px;
        }
        
        .attraction-content h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .attraction-description {
            color: #555;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .attraction-rating {
            display: flex;
            align-items: center;
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .destination-content {
                grid-template-columns: 1fr;
            }
            
            .destination-header {
                height: 350px;
            }
            
            .destination-title {
                font-size: 32px;
            }
            
            .packages-grid, .hotels-grid, .attractions-grid {
                grid-template-columns: 1fr;
            }
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
    
    <!-- Destination Header -->
    <section class="destination-header" style="background-image: url('images/<?php echo $destination['image']; ?>');">
        <div class="container destination-header-content">
            <h1 class="destination-title"><?php echo $destination['name']; ?></h1>
            <div class="destination-location">
                <i>📍</i> <?php echo $destination['city'] . ', ' . $destination['country']; ?>
            </div>
            <div class="destination-rating">
                <div class="stars">
                    <?php 
                    $rating = round($destination['rating']);
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < $rating) {
                            echo '★';
                        } else {
                            echo '☆';
                        }
                    }
                    ?>
                </div>
                <span><?php echo $destination['rating']; ?> rating</span>
            </div>
        </div>
    </section>
    
    <!-- Destination Content -->
    <section class="container">
        <div class="destination-content">
            <div class="destination-main">
                <div class="content-card">
                    <h2>About <?php echo $destination['name']; ?></h2>
                    <div class="destination-description">
                        <p><?php echo $destination['description']; ?></p>
                    </div>
                    
                    <div class="destination-highlights">
                        <h3>Highlights</h3>
                        <ul class="highlights-list">
                            <li class="highlight-item">
                                <i>✓</i> Rich cultural heritage
                            </li>
                            <li class="highlight-item">
                                <i>✓</i> World-class museums and galleries
                            </li>
                            <li class="highlight-item">
                                <i>✓</i> Iconic landmarks and architecture
                            </li>
                            <li class="highlight-item">
                                <i>✓</i> Exquisite cuisine and dining
                            </li>
                            <li class="highlight-item">
                                <i>✓</i> Romantic atmosphere
                            </li>
                            <li class="highlight-item">
                                <i>✓</i> Vibrant nightlife
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>Location</h2>
                    <div class="map-container" id="map">
                        <!-- Map will be loaded here -->
                    </div>
                </div>
            </div>
            
            <div class="destination-sidebar">
                <div class="content-card">
                    <h2>Destination Info</h2>
                    <ul class="info-list">
                        <li class="info-item">
                            <div class="info-label">Country:</div>
                            <div class="info-value"><?php echo $destination['country']; ?></div>
                        </li>
                        <li class="info-item">
                            <div class="info-label">Language:</div>
                            <div class="info-value"><?php echo $destination['language'] ?? 'French'; ?></div>
                        </li>
                        <li class="info-item">
                            <div class="info-label">Currency:</div>
                            <div class="info-value"><?php echo $destination['currency'] ?? 'Euro (EUR)'; ?></div>
                        </li>
                        <li class="info-item">
                            <div class="info-label">Time Zone:</div>
                            <div class="info-value"><?php echo $destination['timezone'] ?? 'Europe/Paris'; ?></div>
                        </li>
                        <li class="info-item">
                            <div class="info-label">Best Time to Visit:</div>
                            <div class="info-value"><?php echo $destination['best_time_to_visit'] ?? 'April to June, September to October'; ?></div>
                        </li>
                    </ul>
                    
                    <div class="action-buttons">
                        <a href="packages.php?destination=<?php echo $destinationId; ?>" class="btn">View Packages</a>
                        <?php if ($isLoggedIn): ?>
                            <a href="save-item.php?type=destination&id=<?php echo $destinationId; ?>&redirect=destination.php?id=<?php echo $destinationId; ?>" class="btn btn-outline <?php echo $isSaved ? 'btn-saved' : ''; ?>">
                                <?php echo $isSaved ? 'Saved' : 'Save'; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>Current Weather</h2>
                    <div class="weather-container">
                        <div class="weather-icon">☀️</div>
                        <div class="weather-temp">22°C</div>
                        <div class="weather-desc">Sunny</div>
                        <div class="weather-details">
                            <div class="weather-detail">
                                <div class="weather-detail-value">65%</div>
                                <div class="weather-detail-label">Humidity</div>
                            </div>
                            <div class="weather-detail">
                                <div class="weather-detail-value">10 km/h</div>
                                <div class="weather-detail-label">Wind</div>
                            </div>
                            <div class="weather-detail">
                                <div class="weather-detail-value">0%</div>
                                <div class="weather-detail-label">Rain</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Packages Section -->
    <section class="container packages-section">
        <h2>Travel Packages for <?php echo $destination['name']; ?></h2>
        <div class="packages-grid">
            <?php foreach ($packages as $package): ?>
                <div class="package-card">
                    <div class="package-image" style="background-image: url('images/<?php echo $package['image']; ?>'); background-color: #ddd;"></div>
                    <div class="package-content">
                        <h3><?php echo $package['name']; ?></h3>
                        <div class="package-meta">
                            <span><?php echo $package['duration']; ?> days</span>
                            <div class="stars">
                                <?php 
                                $rating = round($package['rating']);
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $rating) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <p class="package-description"><?php echo $package['description']; ?></p>
                        <div class="package-price">
                            $<?php echo number_format($package['price'], 2); ?>
                        </div>
                        <a href="package.php?id=<?php echo $package['package_id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Hotels Section -->
    <section class="container hotels-section">
        <h2>Popular Hotels in <?php echo $destination['name']; ?></h2>
        <div class="hotels-grid">
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card">
                    <div class="hotel-image" style="background-image: url('images/<?php echo $hotel['image']; ?>'); background-color: #ddd;"></div>
                    <div class="hotel-content">
                        <h3><?php echo $hotel['name']; ?></h3>
                        <div class="hotel-address"><?php echo $hotel['address']; ?></div>
                        <p class="hotel-description"><?php echo $hotel['description']; ?></p>
                        <div class="hotel-price">
                            From $<?php echo number_format($hotel['price_per_night'], 2); ?> per night
                        </div>
                        <a href="hotel.php?id=<?php echo $hotel['hotel_id']; ?>" class="btn">View Hotel</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Attractions Section -->
    <section class="container attractions-section">
        <h2>Top Attractions in <?php echo $destination['name']; ?></h2>
        <div class="attractions-grid">
            <?php foreach ($attractions as $attraction): ?>
                <div class="attraction-card">
                    <div class="attraction-image" style="background-image: url('images/<?php echo $attraction['image']; ?>'); background-color: #ddd;"></div>
                    <div class="attraction-content">
                        <h3><?php echo $attraction['name']; ?></h3>
                        <p class="attraction-description"><?php echo $attraction['description']; ?></p>
                        <div class="attraction-rating">
                            <div class="stars">
                                <?php 
                                $rating = round($attraction['rating']);
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $rating) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }
                                ?>
                            </div>
                            <span><?php echo $attraction['rating']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

    <script>
        // JavaScript for redirection
        function redirectTo(url) {
            window.location.href = url;
        }
        
        // Initialize map
        function initMap() {
            // In a real application, you would use the Google Maps API
            // For now, we'll just display a placeholder
            const mapContainer = document.getElementById('map');
            mapContainer.innerHTML = '<div style="display: flex; justify-content: center; align-items: center; height: 100%; background-color: #eee; color: #666; font-weight: bold;">Map of <?php echo $destination['name']; ?></div>';
        }
        
        // Add event listeners to buttons if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            initMap();
            
            // Example: Add click event to all buttons with class 'btn'
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.getAttribute('href')) {
                        e.preventDefault();
                        redirectTo(this.getAttribute('href'));
                    }
                });
            });
        });
    </script>
</body>
</html>
