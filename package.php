<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get package ID
$packageId = $_GET['id'] ?? '';

// Get package details
try {
    $package = fetchOne("SELECT p.*, d.name as destination_name, d.city, d.country 
                        FROM packages p 
                        JOIN destinations d ON p.destination_id = d.destination_id 
                        WHERE p.package_id = ?", 
                        [$packageId]);
    
    if (!$package) {
        header("Location: packages.php");
        exit;
    }
} catch (Exception $e) {
    // If there's an error, create a sample package
    $package = [
        'package_id' => $packageId ?: 1,
        'name' => 'Paris Explorer',
        'description' => '5-day Paris adventure with Eiffel Tower tour. Experience the magic of the City of Light with this comprehensive package that includes all the must-see attractions and some hidden gems. Enjoy guided tours, delicious cuisine, and free time to explore on your own.',
        'image' => 'paris_package.jpg',
        'price' => 899.00,
        'duration' => 5,
        'included_services' => 'Flight, Hotel, Breakfast, Eiffel Tower Tour, Seine Cruise',
        'rating' => 4.5,
        'destination_name' => 'Paris',
        'city' => 'Paris',
        'country' => 'France'
    ];
}

// Get reviews
try {
    $reviews = fetchAll("SELECT r.*, u.username, u.first_name, u.last_name 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.user_id 
                        WHERE r.reference_type = 'package' AND r.reference_id = ? 
                        ORDER BY r.created_at DESC", 
                        [$packageId]);
} catch (Exception $e) {
    // If there's an error, create sample reviews
    $reviews = [
        [
            'review_id' => 1,
            'rating' => 5,
            'comment' => 'Amazing experience! The tour guides were knowledgeable and friendly. The hotel was in a perfect location. Highly recommend!',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            'username' => 'traveler123',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ],
        [
            'review_id' => 2,
            'rating' => 4,
            'comment' => 'Great package overall. The hotel was nice and the tours were well organized. The only downside was the airport transfer was a bit delayed.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            'username' => 'wanderlust',
            'first_name' => 'Jane',
            'last_name' => 'Smith'
        ],
        [
            'review_id' => 3,
            'rating' => 5,
            'comment' => 'Perfect vacation! Everything was well planned and the itinerary had a good balance of guided tours and free time. Will definitely book with Theretowhere again!',
            'created_at' => date('Y-m-d H:i:s', strtotime('-25 days')),
            'username' => 'globetrotter',
            'first_name' => 'Mike',
            'last_name' => 'Johnson'
        ]
    ];
}

// Check if user has saved this package
$isSaved = false;
if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    try {
        $savedItem = fetchOne("SELECT * FROM saved_items WHERE user_id = ? AND item_type = 'package' AND item_id = ?", 
                            [$userId, $packageId]);
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
    <title><?php echo $package['name']; ?> - Theretowhere</title>
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
        
        /* Package details styles */
        .package-header {
            position: relative;
            height: 400px;
            background-size: cover;
            background-position: center;
            color: white;
            margin-bottom: 40px;
            display: flex;
            align-items: flex-end;
        }
        
        .package-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }
        
        .package-header-content {
            position: relative;
            z-index: 2;
            padding: 40px 0;
            width: 100%;
        }
        
        .package-title {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .package-location {
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .package-location i {
            margin-right: 5px;
        }
        
        .package-meta {
            display: flex;
            gap: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
        }
        
        .meta-item i {
            margin-right: 5px;
        }
        
        .package-rating {
            display: flex;
            align-items: center;
        }
        
        .stars {
            color: #ffc107;
            margin-right: 5px;
        }
        
        .package-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .package-details {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .package-details h2 {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .package-description {
            margin-bottom: 30px;
        }
        
        .package-highlights {
            margin-bottom: 30px;
        }
        
        .highlights-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .highlight-item {
            display: flex;
            align-items: center;
        }
        
        .highlight-item i {
            color: #9c27b0;
            margin-right: 10px;
        }
        
        .package-itinerary {
            margin-bottom: 30px;
        }
        
        .itinerary-day {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .itinerary-day:last-child {
            border-bottom: none;
        }
        
        .day-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #9c27b0;
        }
        
        .package-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .booking-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            position: sticky;
            top: 100px;
        }
        
        .booking-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .package-price {
            font-size: 28px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 10px;
        }
        
        .price-note {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .booking-features {
            margin-bottom: 20px;
        }
        
        .booking-feature {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .booking-feature i {
            color: #4caf50;
            margin-right: 10px;
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
            width: 100%;
        }
        
        .btn:hover {
            background-color: #7b1fa2;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #9c27b0;
            color: #9c27b0;
            margin-top: 10px;
        }
        
        .btn-outline:hover {
            background-color: #f3e5f5;
        }
        
        .btn-saved {
            background-color: #f3e5f5;
            color: #9c27b0;
            border: 1px solid #9c27b0;
        }
        
        .info-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        .info-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .info-list {
            list-style: none;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-item i {
            color: #9c27b0;
            margin-right: 10px;
            min-width: 20px;
        }
        
        .reviews-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .reviews-header h2 {
            font-size: 24px;
        }
        
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .review-card {
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .reviewer-info {
            display: flex;
            align-items: center;
        }
        
        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #9c27b0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .reviewer-name {
            font-weight: 500;
        }
        
        .review-date {
            color: #666;
            font-size: 14px;
        }
        
        .review-rating {
            color: #ffc107;
        }
        
        .review-content {
            color: #555;
        }
        
        .similar-packages {
            margin-bottom: 40px;
        }
        
        .similar-packages h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .similar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .similar-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .similar-card:hover {
            transform: translateY(-5px);
        }
        
        .similar-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .similar-content {
            padding: 15px;
        }
        
        .similar-content h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .similar-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .similar-price {
            font-size: 18px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 10px;
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
            .package-content {
                grid-template-columns: 1fr;
            }
            
            .package-header {
                height: 300px;
            }
            
            .package-title {
                font-size: 28px;
            }
            
            .similar-grid {
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
    
    <!-- Package Header -->
    <section class="package-header" style="background-image: url('images/<?php echo $package['image']; ?>');">
        <div class="container package-header-content">
            <h1 class="package-title"><?php echo $package['name']; ?></h1>
            <div class="package-location">
                <i>📍</i> <?php echo $package['destination_name'] . ', ' . $package['country']; ?>
            </div>
            <div class="package-meta">
                <div class="meta-item">
                    <i>🕒</i> <?php echo $package['duration']; ?> days
                </div>
                <div class="meta-item package-rating">
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
                    <span><?php echo $package['rating']; ?> (<?php echo count($reviews); ?> reviews)</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Package Content -->
    <section class="container">
        <div class="package-content">
            <div class="package-main">
                <div class="package-details">
                    <h2>Package Details</h2>
                    <div class="package-description">
                        <p><?php echo $package['description']; ?></p>
                    </div>
                    
                    <div class="package-highlights">
                        <h3>Package Highlights</h3>
                        <ul class="highlights-list">
                            <?php 
                            $services = explode(', ', $package['included_services']);
                            foreach ($services as $service): 
                            ?>
                                <li class="highlight-item">
                                    <i>✓</i> <?php echo $service; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="package-itinerary">
                        <h3>Itinerary</h3>
                        
                        <?php for ($day = 1; $day <= $package['duration']; $day++): ?>
                            <div class="itinerary-day">
                                <div class="day-title">Day <?php echo $day; ?></div>
                                <?php if ($day == 1): ?>
                                    <p>Arrival in <?php echo $package['city']; ?>. Transfer to your hotel and check-in. Welcome dinner in the evening.</p>
                                <?php elseif ($day == $package['duration']): ?>
                                    <p>Breakfast at the hotel. Free time for last-minute shopping or sightseeing. Transfer to the airport for your departure flight.</p>
                                <?php else: ?>
                                    <p>Full day exploring the attractions of <?php echo $package['city']; ?>. Guided tours and free time included.</p>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            
            <div class="package-sidebar">
                <div class="booking-card">
                    <h3>Book This Package</h3>
                    <div class="package-price">
                        $<?php echo number_format($package['price'], 2); ?>
                    </div>
                    <p class="price-note">per person, based on double occupancy</p>
                    
                    <div class="booking-features">
                        <div class="booking-feature">
                            <i>✓</i> Free cancellation up to 30 days before departure
                        </div>
                        <div class="booking-feature">
                            <i>✓</i> Pay only 20% deposit now
                        </div>
                        <div class="booking-feature">
                            <i>✓</i> 24/7 customer support
                        </div>
                    </div>
                    
                    <?php if ($isLoggedIn): ?>
                        <a href="book-package.php?id=<?php echo $package['package_id']; ?>" class="btn">Book Now</a>
                        <a href="save-item.php?type=package&id=<?php echo $package['package_id']; ?>&redirect=package.php?id=<?php echo $package['package_id']; ?>" class="btn btn-outline <?php echo $isSaved ? 'btn-saved' : ''; ?>">
                            <?php echo $isSaved ? 'Saved to Wishlist' : 'Save to Wishlist'; ?>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn">Sign In to Book</a>
                    <?php endif; ?>
                </div>
                
                <div class="info-card">
                    <h3>Package Information</h3>
                    <ul class="info-list">
                        <li class="info-item">
                            <i>🏨</i> <strong>Accommodation:</strong> 4-star hotel
                        </li>
                        <li class="info-item">
                            <i>✈️</i> <strong>Transportation:</strong> Flights included
                        </li>
                        <li class="info-item">
                            <i>🍽️</i> <strong>Meals:</strong> Breakfast included
                        </li>
                        <li class="info-item">
                            <i>👥</i> <strong>Group Size:</strong> Max 15 people
                        </li>
                        <li class="info-item">
                            <i>🗣️</i> <strong>Language:</strong> English-speaking guide
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Reviews Section -->
    <section class="container">
        <div class="reviews-section">
            <div class="reviews-header">
                <h2>Customer Reviews</h2>
                <div class="package-rating">
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
                    <span><?php echo $package['rating']; ?> (<?php echo count($reviews); ?> reviews)</span>
                </div>
            </div>
            
            <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                    <p>No reviews yet. Be the first to review this package!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <?php echo strtoupper(substr($review['first_name'] ?? $review['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="reviewer-name">
                                            <?php 
                                            if (!empty($review['first_name']) && !empty($review['last_name'])) {
                                                echo $review['first_name'] . ' ' . $review['last_name'];
                                            } else {
                                                echo $review['username'];
                                            }
                                            ?>
                                        </div>
                                        <div class="review-date">
                                            <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <?php 
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $review['rating']) {
                                            echo '★';
                                        } else {
                                            echo '☆';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="review-content">
                                <?php echo $review['comment']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Similar Packages -->
    <section class="container similar-packages">
        <h2>Similar Packages</h2>
        <div class="similar-grid">
            <?php 
            // In a real app, this would fetch similar packages from the database
            $similarPackages = [
                [
                    'package_id' => 2,
                    'name' => 'NYC Weekend',
                    'image' => 'nyc_package.jpg',
                    'price' => 799.00,
                    'duration' => 3,
                    'city' => 'New York',
                    'country' => 'USA'
                ],
                [
                    'package_id' => 3,
                    'name' => 'Tokyo Discovery',
                    'image' => 'tokyo_package.jpg',
                    'price' => 1299.00,
                    'duration' => 7,
                    'city' => 'Tokyo',
                    'country' => 'Japan'
                ],
                [
                    'package_id' => 4,
                    'name' => 'Roman Holiday',
                    'image' => 'rome_package.jpg',
                    'price' => 849.00,
                    'duration' => 4,
                    'city' => 'Rome',
                    'country' => 'Italy'
                ]
            ];
            
            foreach ($similarPackages as $similar): 
            ?>
                <div class="similar-card">
                    <div class="similar-image" style="background-image: url('images/<?php echo $similar['image']; ?>'); background-color: #ddd;"></div>
                    <div class="similar-content">
                        <h3><?php echo $similar['name']; ?></h3>
                        <div class="similar-meta">
                            <span><?php echo $similar['city'] . ', ' . $similar['country']; ?></span>
                            <span><?php echo $similar['duration']; ?> days</span>
                        </div>
                        <div class="similar-price">
                            $<?php echo number_format($similar['price'], 2); ?>
                        </div>
                        <a href="package.php?id=<?php echo $similar['package_id']; ?>" class="btn">View Details</a>
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
        
        // Add event listeners to buttons if needed
        document.addEventListener('DOMContentLoaded', function() {
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
