<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get example heatmaps
$exampleHeatmaps = [
    [
        'id' => 1,
        'name' => 'New York City - Family Friendly',
        'description' => 'Areas in NYC that are great for families with children',
        'image' => 'nyc_family_heatmap.jpg'
    ],
    [
        'id' => 2,
        'name' => 'San Francisco - Tech Worker',
        'description' => 'Best neighborhoods for tech workers based on commute to major companies',
        'image' => 'sf_tech_heatmap.jpg'
    ],
    [
        'id' => 3,
        'name' => 'Chicago - Foodie Paradise',
        'description' => 'Areas with the highest concentration of top-rated restaurants',
        'image' => 'chicago_food_heatmap.jpg'
    ],
    [
        'id' => 4,
        'name' => 'Austin - Music Lover',
        'description' => 'Neighborhoods with the best access to live music venues',
        'image' => 'austin_music_heatmap.jpg'
    ]
];

// Get user's saved heatmaps if logged in
$userHeatmaps = [];
if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $userHeatmaps = fetchAll("SELECT * FROM heatmaps WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heatmaps - Theretowhere</title>
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
        
        /* Heatmap styles */
        .section-title {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .heatmap-intro {
            margin-bottom: 40px;
        }
        
        .heatmap-intro p {
            margin-bottom: 15px;
            color: #666;
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
        
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .heatmap-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .heatmap-card:hover {
            transform: translateY(-5px);
        }
        
        .heatmap-image {
            height: 180px;
            background-size: cover;
            background-position: center;
        }
        
        .heatmap-info {
            padding: 20px;
        }
        
        .heatmap-info h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .heatmap-info p {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .heatmap-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .create-heatmap {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .create-heatmap h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .create-heatmap p {
            color: #666;
            margin-bottom: 20px;
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
        
        .empty-state {
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .empty-state p {
            color: #666;
            margin-bottom: 20px;
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
            <h1>Heatmaps</h1>
            <p>Visualize the best neighborhoods based on your preferences</p>
        </div>
    </section>
    
    <!-- Create Heatmap Section -->
    <section class="container">
        <div class="create-heatmap">
            <h2>Create Your Own Heatmap</h2>
            <p>Customize a heatmap based on what matters most to you: commute times, schools, restaurants, parks, and more.</p>
            <a href="create-heatmap.php" class="btn">Create a Heatmap</a>
        </div>
    </section>
    
    <!-- User's Heatmaps Section (if logged in) -->
    <?php if ($isLoggedIn && count($userHeatmaps) > 0): ?>
    <section class="container">
        <h2 class="section-title">Your Heatmaps</h2>
        <div class="heatmap-grid">
            <?php foreach ($userHeatmaps as $heatmap): ?>
                <div class="heatmap-card">
                    <div class="heatmap-image" style="background-image: url('images/heatmap_placeholder.jpg')"></div>
                    <div class="heatmap-info">
                        <h3><?php echo $heatmap['name']; ?></h3>
                        <div class="heatmap-meta">
                            <span><?php echo $heatmap['city']; ?></span>
                            <span>Created <?php echo date('M d, Y', strtotime($heatmap['created_at'])); ?></span>
                        </div>
                        <a href="view-heatmap.php?id=<?php echo $heatmap['heatmap_id']; ?>" class="btn">View Heatmap</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Example Heatmaps Section -->
    <section class="container">
        <h2 class="section-title">Example Heatmaps</h2>
        <div class="heatmap-intro">
            <p>Explore our curated collection of heatmaps for different cities and preferences. These examples show how Theretowhere can help you find the perfect neighborhood.</p>
        </div>
        
        <div class="heatmap-grid">
            <?php foreach ($exampleHeatmaps as $heatmap): ?>
                <div class="heatmap-card">
                    <div class="heatmap-image" style="background-image: url('images/<?php echo $heatmap['image']; ?>')"></div>
                    <div class="heatmap-info">
                        <h3><?php echo $heatmap['name']; ?></h3>
                        <p><?php echo $heatmap['description']; ?></p>
                        <a href="example-heatmap.php?id=<?php echo $heatmap['id']; ?>" class="btn">View Heatmap</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section class="container how-it-works">
        <h2>How Heatmaps Work</h2>
        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Select Your City</h3>
                <p>Choose the city you're interested in exploring or moving to.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Set Your Preferences</h3>
                <p>Tell us what matters to you: commute times, schools, restaurants, parks, etc.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Adjust Importance</h3>
                <p>Rank how important each factor is to you on a scale from 1-10.</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>View Your Heatmap</h3>
                <p>See a color-coded map showing which neighborhoods best match your preferences.</p>
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
