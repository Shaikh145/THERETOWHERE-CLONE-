<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get packages
try {
    $packages = fetchAll("SELECT p.*, d.name as destination_name, d.city, d.country 
                         FROM packages p 
                         JOIN destinations d ON p.destination_id = d.destination_id 
                         ORDER BY p.rating DESC");
} catch (Exception $e) {
    // If there's an error, create some sample packages
    $packages = [
        [
            'package_id' => 1,
            'name' => 'Paris Explorer',
            'description' => '5-day Paris adventure with Eiffel Tower tour',
            'image' => 'paris_package.jpg',
            'price' => 899.00,
            'duration' => 5,
            'included_services' => 'Flight, Hotel, Breakfast, Eiffel Tower Tour, Seine Cruise',
            'rating' => 4.5,
            'destination_name' => 'Paris',
            'city' => 'Paris',
            'country' => 'France'
        ],
        [
            'package_id' => 2,
            'name' => 'NYC Weekend',
            'description' => '3-day New York experience',
            'image' => 'nyc_package.jpg',
            'price' => 799.00,
            'duration' => 3,
            'included_services' => 'Flight, Hotel, City Pass, Broadway Show',
            'rating' => 4.4,
            'destination_name' => 'New York',
            'city' => 'New York',
            'country' => 'USA'
        ],
        [
            'package_id' => 3,
            'name' => 'Tokyo Discovery',
            'description' => '7-day complete Tokyo experience',
            'image' => 'tokyo_package.jpg',
            'price' => 1299.00,
            'duration' => 7,
            'included_services' => 'Flight, Hotel, Breakfast, Mt. Fuji Tour, Robot Restaurant',
            'rating' => 4.7,
            'destination_name' => 'Tokyo',
            'city' => 'Tokyo',
            'country' => 'Japan'
        ],
        [
            'package_id' => 4,
            'name' => 'Roman Holiday',
            'description' => '4-day Rome getaway',
            'image' => 'rome_package.jpg',
            'price' => 849.00,
            'duration' => 4,
            'included_services' => 'Flight, Hotel, Breakfast, Colosseum Tour, Vatican Visit',
            'rating' => 4.3,
            'destination_name' => 'Rome',
            'city' => 'Rome',
            'country' => 'Italy'
        ],
        [
            'package_id' => 5,
            'name' => 'Bali Bliss',
            'description' => '6-day relaxation package',
            'image' => 'bali_package.jpg',
            'price' => 999.00,
            'duration' => 6,
            'included_services' => 'Flight, Resort Stay, All Meals, Spa Treatment, Ubud Tour',
            'rating' => 4.8,
            'destination_name' => 'Bali',
            'city' => 'Bali',
            'country' => 'Indonesia'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Packages - Theretowhere</title>
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
        
        /* Packages styles */
        .packages-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }
        
        .filters {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            align-self: start;
        }
        
        .filters h2 {
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .filter-checkbox {
            display: flex;
            align-items: center;
        }
        
        .filter-checkbox input {
            margin-right: 8px;
        }
        
        .price-range {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .price-inputs {
            display: flex;
            gap: 10px;
        }
        
        .price-inputs input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .filter-button {
            background-color: #9c27b0;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .filter-button:hover {
            background-color: #7b1fa2;
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
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .package-content {
            padding: 20px;
        }
        
        .package-content h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .package-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .package-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .package-features {
            margin-bottom: 15px;
        }
        
        .feature-tag {
            display: inline-block;
            background-color: #f3e5f5;
            color: #9c27b0;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .package-price {
            font-size: 22px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 15px;
        }
        
        .package-rating {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .stars {
            color: #ffc107;
            margin-right: 5px;
        }
        
        .rating-value {
            font-weight: 500;
        }
        
        .btn {
            display: inline-block;
            background-color: #9c27b0;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #7b1fa2;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #9c27b0;
            color: #9c27b0;
            margin-left: 10px;
        }
        
        .btn-outline:hover {
            background-color: #f3e5f5;
        }
        
        .package-actions {
            display: flex;
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
            .packages-container {
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
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Travel Packages</h1>
            <p>Discover our curated collection of travel experiences</p>
        </div>
    </section>
    
    <!-- Packages Content -->
    <section class="container">
        <div class="packages-container">
            <!-- Filters Sidebar -->
            <div class="filters">
                <h2>Filter Packages</h2>
                
                <div class="filter-group">
                    <h3>Destinations</h3>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="checkbox" name="destination" value="europe"> Europe
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="destination" value="asia"> Asia
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="destination" value="north-america"> North America
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="destination" value="south-america"> South America
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="destination" value="africa"> Africa
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="destination" value="oceania"> Oceania
                        </label>
                    </div>
                </div>
                
                <div class="filter-group">
                    <h3>Duration</h3>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="checkbox" name="duration" value="1-3"> 1-3 days
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="duration" value="4-7"> 4-7 days
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="duration" value="8-14"> 8-14 days
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="duration" value="15+"> 15+ days
                        </label>
                    </div>
                </div>
                
                <div class="filter-group">
                    <h3>Price Range</h3>
                    <div class="price-range">
                        <div class="price-inputs">
                            <input type="number" placeholder="Min" min="0">
                            <input type="number" placeholder="Max" min="0">
                        </div>
                        <button class="filter-button">Apply</button>
                    </div>
                </div>
                
                <div class="filter-group">
                    <h3>Rating</h3>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="checkbox" name="rating" value="5"> 5 Stars
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="rating" value="4"> 4+ Stars
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="rating" value="3"> 3+ Stars
                        </label>
                    </div>
                </div>
                
                <button class="filter-button">Apply Filters</button>
            </div>
            
            <!-- Packages Grid -->
            <div class="packages-grid">
                <?php foreach ($packages as $package): ?>
                    <div class="package-card">
                        <div class="package-image" style="background-image: url('images/<?php echo $package['image']; ?>'); background-color: #ddd;"></div>
                        <div class="package-content">
                            <h2><?php echo $package['name']; ?></h2>
                            <div class="package-meta">
                                <span><?php echo $package['destination_name'] . ', ' . $package['country']; ?></span>
                                <span><?php echo $package['duration']; ?> days</span>
                            </div>
                            <p class="package-description"><?php echo $package['description']; ?></p>
                            <div class="package-features">
                                <?php 
                                $services = explode(', ', $package['included_services']);
                                foreach (array_slice($services, 0, 3) as $service): 
                                ?>
                                    <span class="feature-tag"><?php echo $service; ?></span>
                                <?php endforeach; ?>
                                <?php if (count($services) > 3): ?>
                                    <span class="feature-tag">+<?php echo count($services) - 3; ?> more</span>
                                <?php endif; ?>
                            </div>
                            <div class="package-price">
                                $<?php echo number_format($package['price'], 2); ?>
                            </div>
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
                                <span class="rating-value"><?php echo $package['rating']; ?></span>
                            </div>
                            <div class="package-actions">
                                <a href="package.php?id=<?php echo $package['package_id']; ?>" class="btn">View Details</a>
                                <?php if ($isLoggedIn): ?>
                                    <a href="save-item.php?type=package&id=<?php echo $package['package_id']; ?>&redirect=packages.php" class="btn btn-outline">Save</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
            
            // Filter functionality
            const filterButton = document.querySelector('.filter-button');
            filterButton.addEventListener('click', function() {
                // In a real app, this would filter the packages based on selected options
                alert('Filters applied! This would filter the packages in a real application.');
            });
        });
    </script>
</body>
</html>
