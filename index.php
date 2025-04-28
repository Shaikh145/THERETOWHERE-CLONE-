<?php
// Include database connection
require_once 'db.php';

// Get popular destinations
$popularDestinations = fetchAll("SELECT * FROM destinations WHERE popular = TRUE LIMIT 6");

// Get featured packages
$featuredPackages = fetchAll("SELECT p.*, d.name as destination_name, d.city, d.country 
                             FROM packages p 
                             JOIN destinations d ON p.destination_id = d.destination_id 
                             LIMIT 3");

// Get latest blog posts
$latestPosts = fetchAll("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3");

// Check if user is logged in
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theretowhere - Moving just became easier</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
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
        
        /* Notification banner */
        .notification-banner {
            background-color: #d1ecf1;
            color: #0c5460;
            text-align: center;
            padding: 10px 0;
        }
        
        /* Hero section */
        .hero {
            background-color: #ffebee;
            padding: 60px 0;
            text-align: left;
        }
        
        .hero-content {
            max-width: 600px;
        }
        
        .hero h1 {
            font-size: 42px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #555;
        }
        
        .highlight {
            background-color: #fff2cc;
            padding: 0 5px;
        }
        
        /* Feature boxes */
        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 40px 0;
        }
        
        .feature-box {
            flex: 1;
            min-width: 300px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            transition: transform 0.3s ease;
        }
        
        .feature-box:hover {
            transform: translateY(-5px);
        }
        
        .feature-box h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .feature-box p {
            color: #666;
            margin-bottom: 20px;
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
        }
        
        .btn-outline:hover {
            background-color: #f3e5f5;
        }
        
        /* Chrome extension promo */
        .extension-promo {
            text-align: center;
            margin: 40px 0;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 8px;
        }
        
        .extension-promo img {
            height: 30px;
            vertical-align: middle;
            margin-right: 10px;
        }
        
        /* Features showcase */
        .showcase {
            margin: 60px 0;
            text-align: center;
        }
        
        .showcase h2 {
            font-size: 32px;
            margin-bottom: 40px;
        }
        
        .showcase-image {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        /* Destinations section */
        .section-title {
            font-size: 28px;
            margin: 60px 0 30px;
            text-align: center;
        }
        
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
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
            height: 180px;
            background-size: cover;
            background-position: center;
        }
        
        .destination-info {
            padding: 15px;
        }
        
        .destination-info h3 {
            margin-bottom: 5px;
        }
        
        .destination-info p {
            color: #666;
            font-size: 14px;
        }
        
        .rating {
            color: #ffc107;
            margin-top: 10px;
        }
        
        /* Packages section */
        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .package-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .package-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        
        .package-info {
            padding: 20px;
        }
        
        .package-info h3 {
            margin-bottom: 10px;
        }
        
        .package-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: #666;
            font-size: 14px;
        }
        
        .package-price {
            font-size: 20px;
            font-weight: bold;
            color: #9c27b0;
            margin-bottom: 15px;
        }
        
        /* Blog section */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 60px;
        }
        
        .blog-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .blog-image {
            height: 180px;
            background-size: cover;
            background-position: center;
        }
        
        .blog-info {
            padding: 20px;
        }
        
        .blog-info h3 {
            margin-bottom: 10px;
        }
        
        .blog-date {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 60px 0 30px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: #bbb;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: #fff;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #444;
            color: #bbb;
            font-size: 14px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            
            .feature-box {
                min-width: 100%;
            }
            
            .packages-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">Theretowhere</div>
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
    
    <!-- Notification Banner -->
    <div class="notification-banner">
        <div class="container">
            <p>📢 This project is still in active development! Reach out over <a href="#">Twitter</a>, <a href="#">BlueSky</a> or <a href="#">Email</a>!</p>
        </div>
    </div>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Moving just became easier.</h1>
                <p>There to Where helps you evaluate listings based on their proximity to things <span class="highlight">you</span> care about.</p>
                <p>
                    No more juggling of Google Maps tabs.<br>
                    No more "Arg! I forgot this is far from work" moments.<br>
                    No more frustration.
                </p>
                <p><strong>Handle everything in one page.</strong></p>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="container">
        <div class="features">
            <div class="feature-box">
                <h3>Wondering how far a listing is from work, your friends, that gym, maybe some chess clubs, and your favorite grocery chain?</h3>
                <p>Our distance matrix tool helps you calculate travel times to all your important places at once.</p>
                <a href="distance-matrix.php" class="btn">Check out our Distance Matrix</a>
            </div>
            
            <div class="feature-box">
                <h3>Wondering which parts of your city fit your preferences the most?</h3>
                <p>Our heatmaps show you the best neighborhoods based on what matters to you.</p>
                <a href="heatmaps.php" class="btn">Check out our example heatmaps</a>
                <div style="margin-top: 15px;">
                    <a href="create-heatmap.php" class="btn btn-outline">Make a Heatmap</a>
                    <a href="saved-heatmaps.php" class="btn btn-outline">See your Saved Heatmaps</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Chrome Extension Promo -->
    <div class="container">
        <div class="extension-promo">
            <a href="#"><img src="chrome_logo.png" alt="Chrome Logo"> CHECK OUT OUR CHROME EXTENSION!</a>
        </div>
    </div>
    
    <!-- Features Showcase -->
    <section class="container showcase">
        <h2>See what theretowhere can do:</h2>
        <img src="heatmap_demo.jpg" alt="Heatmap Demo" class="showcase-image">
    </section>
    
    <!-- Popular Destinations -->
    <section class="container">
        <h2 class="section-title">Popular Destinations</h2>
        <div class="destinations-grid">
            <?php foreach ($popularDestinations as $destination): ?>
                <div class="destination-card">
                    <div class="destination-image" style="background-image: url('images/<?php echo $destination['image']; ?>')"></div>
                    <div class="destination-info">
                        <h3><?php echo $destination['name']; ?></h3>
                        <p><?php echo $destination['city'] . ', ' . $destination['country']; ?></p>
                        <div class="rating">
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
                            <span>(<?php echo $destination['rating']; ?>)</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center;">
            <a href="destinations.php" class="btn">View All Destinations</a>
        </div>
    </section>
    
    <!-- Featured Packages -->
    <section class="container">
        <h2 class="section-title">Featured Travel Packages</h2>
        <div class="packages-grid">
            <?php foreach ($featuredPackages as $package): ?>
                <div class="package-card">
                    <div class="package-image" style="background-image: url('images/<?php echo $package['image']; ?>')"></div>
                    <div class="package-info">
                        <h3><?php echo $package['name']; ?></h3>
                        <div class="package-meta">
                            <span><?php echo $package['destination_name'] . ', ' . $package['country']; ?></span>
                            <span><?php echo $package['duration']; ?> days</span>
                        </div>
                        <p><?php echo substr($package['description'], 0, 100) . '...'; ?></p>
                        <div class="package-price">
                            $<?php echo number_format($package['price'], 2); ?>
                        </div>
                        <a href="package.php?id=<?php echo $package['package_id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center;">
            <a href="packages.php" class="btn">View All Packages</a>
        </div>
    </section>
    
    <!-- Latest Blog Posts -->
    <section class="container">
        <h2 class="section-title">Travel Guides & Tips</h2>
        <div class="blog-grid">
            <?php foreach ($latestPosts as $post): ?>
                <div class="blog-card">
                    <div class="blog-image" style="background-image: url('images/<?php echo $post['image']; ?>')"></div>
                    <div class="blog-info">
                        <h3><?php echo $post['title']; ?></h3>
                        <div class="blog-date">
                            <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                        </div>
                        <p><?php echo substr($post['content'], 0, 120) . '...'; ?></p>
                        <a href="blog-post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-outline">Read More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center;">
            <a href="blog.php" class="btn">View All Articles</a>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>Theretowhere</h3>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="careers.php">Careers</a></li>
                        <li><a href="press.php">Press</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Destinations</h3>
                    <ul>
                        <li><a href="destinations.php?country=France">France</a></li>
                        <li><a href="destinations.php?country=USA">United States</a></li>
                        <li><a href="destinations.php?country=Japan">Japan</a></li>
                        <li><a href="destinations.php?country=Italy">Italy</a></li>
                        <li><a href="destinations.php?country=Indonesia">Indonesia</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Travel Resources</h3>
                    <ul>
                        <li><a href="blog.php">Travel Blog</a></li>
                        <li><a href="guides.php">Travel Guides</a></li>
                        <li><a href="tips.php">Travel Tips</a></li>
                        <li><a href="faq.php">FAQs</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Tools</h3>
                    <ul>
                        <li><a href="distance-matrix.php">Distance Matrix</a></li>
                        <li><a href="heatmaps.php">Heatmaps</a></li>
                        <li><a href="chrome-extension.php">Chrome Extension</a></li>
                    </ul>
                </div>
            </div>
            
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
