<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get destinations
try {
    $destinations = fetchAll("SELECT * FROM destinations ORDER BY name ASC");
} catch (Exception $e) {
    // If there's an error, create some sample destinations
    $destinations = [
        [
            'destination_id' => 1,
            'name' => 'Paris',
            'city' => 'Paris',
            'country' => 'France',
            'description' => 'The City of Light, known for its stunning architecture, art museums, and romantic ambiance.',
            'image' => 'paris.jpg',
            'rating' => 4.8
        ],
        [
            'destination_id' => 2,
            'name' => 'New York',
            'city' => 'New York',
            'country' => 'USA',
            'description' => 'The Big Apple, a global center for art, fashion, finance, and culture.',
            'image' => 'newyork.jpg',
            'rating' => 4.7
        ],
        [
            'destination_id' => 3,
            'name' => 'Tokyo',
            'city' => 'Tokyo',
            'country' => 'Japan',
            'description' => 'Japan\'s busy capital, mixing the ultramodern and the traditional.',
            'image' => 'tokyo.jpg',
            'rating' => 4.9
        ],
        [
            'destination_id' => 4,
            'name' => 'Rome',
            'city' => 'Rome',
            'country' => 'Italy',
            'description' => 'The Eternal City, with nearly 3,000 years of globally influential art, architecture, and culture.',
            'image' => 'rome.jpg',
            'rating' => 4.6
        ],
        [
            'destination_id' => 5,
            'name' => 'Bali',
            'city' => 'Bali',
            'country' => 'Indonesia',
            'description' => 'Island paradise known for its forested volcanic mountains, beaches, and coral reefs.',
            'image' => 'bali.jpg',
            'rating' => 4.5
        ],
        [
            'destination_id' => 6,
            'name' => 'London',
            'city' => 'London',
            'country' => 'United Kingdom',
            'description' => 'A 21st-century city with history stretching back to Roman times.',
            'image' => 'london.jpg',
            'rating' => 4.7
        ],
        [
            'destination_id' => 7,
            'name' => 'Barcelona',
            'city' => 'Barcelona',
            'country' => 'Spain',
            'description' => 'Known for its art and architecture, including Gaudi\'s Sagrada Familia.',
            'image' => 'barcelona.jpg',
            'rating' => 4.6
        ],
        [
            'destination_id' => 8,
            'name' => 'Sydney',
            'city' => 'Sydney',
            'country' => 'Australia',
            'description' => 'Famous for its harbor, iconic Opera House, and beautiful beaches.',
            'image' => 'sydney.jpg',
            'rating' => 4.7
        ]
    ];
}

// Group destinations by continent for filtering
$continents = [
    'Europe' => ['France', 'Italy', 'United Kingdom', 'Spain'],
    'North America' => ['USA', 'Canada', 'Mexico'],
    'Asia' => ['Japan', 'Indonesia', 'Thailand', 'China', 'India'],
    'Oceania' => ['Australia', 'New Zealand'],
    'Africa' => ['South Africa', 'Egypt', 'Morocco'],
    'South America' => ['Brazil', 'Argentina', 'Peru']
];

// Get filter parameters
$continentFilter = $_GET['continent'] ?? '';
$countryFilter = $_GET['country'] ?? '';
$searchQuery = $_GET['q'] ?? '';

// Apply filters
$filteredDestinations = [];
foreach ($destinations as $destination) {
    // Apply continent filter
    if (!empty($continentFilter)) {
        $matchesContinent = false;
        foreach ($continents as $continent => $countries) {
            if ($continent === $continentFilter && in_array($destination['country'], $countries)) {
                $matchesContinent = true;
                break;
            }
        }
        if (!$matchesContinent) {
            continue;
        }
    }
    
    // Apply country filter
    if (!empty($countryFilter) && $destination['country'] !== $countryFilter) {
        continue;
    }
    
    // Apply search query
    if (!empty($searchQuery)) {
        $searchLower = strtolower($searchQuery);
        $nameMatch = strpos(strtolower($destination['name']), $searchLower) !== false;
        $cityMatch = strpos(strtolower($destination['city']), $searchLower) !== false;
        $countryMatch = strpos(strtolower($destination['country']), $searchLower) !== false;
        $descMatch = strpos(strtolower($destination['description']), $searchLower) !== false;
        
        if (!$nameMatch && !$cityMatch && !$countryMatch && !$descMatch) {
            continue;
        }
    }
    
    $filteredDestinations[] = $destination;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - Theretowhere</title>
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
        
        /* Destinations styles */
        .destinations-container {
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
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
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
            width: 100%;
        }
        
        .filter-button:hover {
            background-color: #7b1fa2;
        }
        
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .destination-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .destination-card:hover {
            transform: translateY(-5px);
        }
        
        .destination-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .destination-content {
            padding: 20px;
        }
        
        .destination-content h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .destination-location {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .destination-description {
            color: #555;
            margin-bottom: 15px;
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .destination-rating {
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
            text-align: center;
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
        
        .destination-actions {
            display: flex;
        }
        
        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .no-results h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #666;
        }
        
        .no-results p {
            color: #888;
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
            .destinations-container {
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
            <h1>Explore Destinations</h1>
            <p>Discover amazing places around the world</p>
        </div>
    </section>
    
    <!-- Destinations Content -->
    <section class="container">
        <div class="destinations-container">
            <!-- Filters Sidebar -->
            <div class="filters">
                <h2>Filter Destinations</h2>
                
                <form action="destinations.php" method="GET">
                    <div class="search-box">
                        <input type="text" name="q" placeholder="Search destinations..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <h3>Continents</h3>
                        <div class="filter-options">
                            <?php foreach ($continents as $continent => $countries): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="continent" value="<?php echo $continent; ?>" <?php echo $continentFilter === $continent ? 'checked' : ''; ?>>
                                    <?php echo $continent; ?>
                                </label>
                            <?php endforeach; ?>
                            <?php if (!empty($continentFilter)): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="continent" value=""> All Continents
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3>Popular Countries</h3>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="radio" name="country" value="France" <?php echo $countryFilter === 'France' ? 'checked' : ''; ?>>
                                France
                            </label>
                            <label class="filter-checkbox">
                                <input type="radio" name="country" value="USA" <?php echo $countryFilter === 'USA' ? 'checked' : ''; ?>>
                                USA
                            </label>
                            <label class="filter-checkbox">
                                <input type="radio" name="country" value="Japan" <?php echo $countryFilter === 'Japan' ? 'checked' : ''; ?>>
                                Japan
                            </label>
                            <label class="filter-checkbox">
                                <input type="radio" name="country" value="Italy" <?php echo $countryFilter === 'Italy' ? 'checked' : ''; ?>>
                                Italy
                            </label>
                            <label class="filter-checkbox">
                                <input type="radio" name="country" value="Australia" <?php echo $countryFilter === 'Australia' ? 'checked' : ''; ?>>
                                Australia
                            </label>
                            <?php if (!empty($countryFilter)): ?>
                                <label class="filter-checkbox">
                                    <input type="radio" name="country" value=""> All Countries
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="filter-button">Apply Filters</button>
                    
                    <?php if (!empty($continentFilter) || !empty($countryFilter) || !empty($searchQuery)): ?>
                        <a href="destinations.php" style="display: block; text-align: center; margin-top: 10px;">Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Destinations Grid -->
            <div class="destinations-grid">
                <?php if (empty($filteredDestinations)): ?>
                    <div class="no-results">
                        <h3>No destinations found</h3>
                        <p>Try adjusting your filters or search criteria</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($filteredDestinations as $destination): ?>
                        <div class="destination-card">
                            <div class="destination-image" style="background-image: url('images/<?php echo $destination['image']; ?>'); background-color: #ddd;"></div>
                            <div class="destination-content">
                                <h2><?php echo $destination['name']; ?></h2>
                                <div class="destination-location">
                                    <?php echo $destination['city'] . ', ' . $destination['country']; ?>
                                </div>
                                <p class="destination-description"><?php echo $destination['description']; ?></p>
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
                                    <span class="rating-value"><?php echo $destination['rating']; ?></span>
                                </div>
                                <div class="destination-actions">
                                    <a href="destination.php?id=<?php echo $destination['destination_id']; ?>" class="btn">View Details</a>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="save-item.php?type=destination&id=<?php echo $destination['destination_id']; ?>&redirect=destinations.php" class="btn btn-outline">Save</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
        });
    </script>
</body>
</html>
