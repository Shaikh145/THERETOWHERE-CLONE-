<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get blog posts with error handling
try {
    $blogPosts = fetchAll("SELECT * FROM blog_posts ORDER BY created_at DESC");
} catch (Exception $e) {
    // If there's an error, create some sample blog posts
    $blogPosts = [
        [
            'post_id' => 1,
            'title' => 'Top 10 Paris Attractions',
            'content' => 'Paris, the City of Light, is renowned for its stunning architecture, art museums, historical monuments, and romantic ambiance. Here are the top 10 attractions you must visit when in Paris. From the iconic Eiffel Tower to the historic Notre-Dame Cathedral, these landmarks showcase the city\'s rich cultural heritage and timeless beauty...',
            'image' => 'paris_blog.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'post_id' => 2,
            'title' => 'New York on a Budget',
            'content' => 'Visiting New York City doesn\'t have to break the bank. With some planning and insider knowledge, you can experience the best of the Big Apple without spending a fortune. From free museum days to affordable dining options and discount Broadway tickets, this guide will help you make the most of your NYC trip while sticking to a budget...',
            'image' => 'nyc_budget.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ],
        [
            'post_id' => 3,
            'title' => 'Tokyo Travel Guide',
            'content' => 'Tokyo, Japan\'s busy capital, mixes the ultramodern and the traditional, from neon-lit skyscrapers to historic temples. This comprehensive guide covers everything you need to know before visiting Tokyo, including the best time to visit, transportation tips, must-see attractions, food recommendations, and cultural etiquette...',
            'image' => 'tokyo_guide.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
        ],
        [
            'post_id' => 4,
            'title' => 'Hidden Gems in Rome',
            'content' => 'While the Colosseum and Vatican are must-sees in Rome, the Eternal City has countless hidden treasures waiting to be discovered. This article reveals lesser-known attractions, charming neighborhoods, and authentic local experiences that most tourists miss. Explore ancient ruins without the crowds, find the best local trattorias, and discover secret viewpoints for unforgettable Roman vistas...',
            'image' => 'rome_hidden.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
        ],
        [
            'post_id' => 5,
            'title' => 'Bali: Island of the Gods',
            'content' => 'Bali, Indonesia\'s most famous island, is known for its forested volcanic mountains, iconic rice paddies, beaches, and coral reefs. This guide explores why Bali should be your next destination, covering its rich culture, spiritual temples, luxury resorts, adventure activities, and the warm hospitality of the Balinese people...',
            'image' => 'bali_gods.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-14 days'))
        ]
    ];
}

// If no blog posts are found, create sample data
if (empty($blogPosts)) {
    $blogPosts = [
        [
            'post_id' => 1,
            'title' => 'Top 10 Paris Attractions',
            'content' => 'Paris, the City of Light, is renowned for its stunning architecture, art museums, historical monuments, and romantic ambiance. Here are the top 10 attractions you must visit when in Paris. From the iconic Eiffel Tower to the historic Notre-Dame Cathedral, these landmarks showcase the city\'s rich cultural heritage and timeless beauty...',
            'image' => 'paris_blog.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'post_id' => 2,
            'title' => 'New York on a Budget',
            'content' => 'Visiting New York City doesn\'t have to break the bank. With some planning and insider knowledge, you can experience the best of the Big Apple without spending a fortune. From free museum days to affordable dining options and discount Broadway tickets, this guide will help you make the most of your NYC trip while sticking to a budget...',
            'image' => 'nyc_budget.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ],
        [
            'post_id' => 3,
            'title' => 'Tokyo Travel Guide',
            'content' => 'Tokyo, Japan\'s busy capital, mixes the ultramodern and the traditional, from neon-lit skyscrapers to historic temples. This comprehensive guide covers everything you need to know before visiting Tokyo, including the best time to visit, transportation tips, must-see attractions, food recommendations, and cultural etiquette...',
            'image' => 'tokyo_guide.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
        ],
        [
            'post_id' => 4,
            'title' => 'Hidden Gems in Rome',
            'content' => 'While the Colosseum and Vatican are must-sees in Rome, the Eternal City has countless hidden treasures waiting to be discovered. This article reveals lesser-known attractions, charming neighborhoods, and authentic local experiences that most tourists miss. Explore ancient ruins without the crowds, find the best local trattorias, and discover secret viewpoints for unforgettable Roman vistas...',
            'image' => 'rome_hidden.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
        ],
        [
            'post_id' => 5,
            'title' => 'Bali: Island of the Gods',
            'content' => 'Bali, Indonesia\'s most famous island, is known for its forested volcanic mountains, iconic rice paddies, beaches, and coral reefs. This guide explores why Bali should be your next destination, covering its rich culture, spiritual temples, luxury resorts, adventure activities, and the warm hospitality of the Balinese people...',
            'image' => 'bali_gods.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-14 days'))
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide & Blog - Theretowhere</title>
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
        
        /* Blog styles */
        .blog-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 30px;
        }
        
        .blog-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .blog-sidebar {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .blog-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
        }
        
        .blog-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .blog-content {
            padding: 25px;
        }
        
        .blog-content h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .blog-meta {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .blog-meta span {
            margin-right: 15px;
        }
        
        .blog-excerpt {
            color: #555;
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
        
        .sidebar-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .sidebar-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .popular-posts {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .popular-post {
            display: flex;
            gap: 10px;
        }
        
        .popular-post-image {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
            flex-shrink: 0;
        }
        
        .popular-post-content h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .popular-post-content p {
            font-size: 12px;
            color: #666;
        }
        
        .categories-list {
            list-style: none;
        }
        
        .categories-list li {
            margin-bottom: 10px;
        }
        
        .categories-list li a {
            display: flex;
            justify-content: space-between;
            color: #333;
            transition: color 0.3s;
        }
        
        .categories-list li a:hover {
            color: #9c27b0;
        }
        
        .categories-list li a span {
            color: #666;
            font-size: 14px;
        }
        
        .tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .tag {
            display: inline-block;
            padding: 5px 10px;
            background-color: #f5f5f5;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
            transition: all 0.3s;
        }
        
        .tag:hover {
            background-color: #9c27b0;
            color: white;
        }
        
        .newsletter-form {
            margin-top: 15px;
        }
        
        .newsletter-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .newsletter-form button {
            width: 100%;
        }
        
        /* Add Blog Post Form */
        .add-post-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 8px;
        }
        
        .add-post-form h3 {
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        textarea.form-control {
            min-height: 150px;
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
            .blog-container {
                grid-template-columns: 1fr;
            }
        }
        
        /* Message styles */
        .message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
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
            <h1>Travel Guide & Blog</h1>
            <p>Discover travel tips, destination guides, and moving advice</p>
        </div>
    </section>
    
    <!-- Blog Content -->
    <section class="container">
        <?php if ($isLoggedIn): ?>
        <!-- Add Blog Post Form (Only for logged-in users) -->
        <div class="sidebar-card" style="margin-bottom: 30px;">
            <h3>Add New Blog Post</h3>
            <form action="add-blog-post.php" method="POST" class="add-post-form">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Image Name</label>
                    <input type="text" id="image" name="image" class="form-control" placeholder="e.g., paris_blog.jpg">
                </div>
                <button type="submit" class="btn">Add Post</button>
            </form>
        </div>
        <?php endif; ?>
        
        <div class="blog-container">
            <div class="blog-main">
                <?php foreach ($blogPosts as $post): ?>
                    <article class="blog-card">
                        <div class="blog-image" style="background-image: url('images/<?php echo $post['image']; ?>'); background-color: #ddd;"></div>
                        <div class="blog-content">
                            <h2><?php echo $post['title']; ?></h2>
                            <div class="blog-meta">
                                <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                <span>By Admin</span>
                            </div>
                            <div class="blog-excerpt">
                                <?php echo substr($post['content'], 0, 250) . '...'; ?>
                            </div>
                            <a href="blog-post.php?id=<?php echo $post['post_id']; ?>" class="btn">Read More</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <div class="blog-sidebar">
                <!-- Search -->
                <div class="sidebar-card">
                    <h3>Search</h3>
                    <form action="blog-search.php" method="GET">
                        <div class="newsletter-form">
                            <input type="text" name="q" placeholder="Search articles...">
                            <button type="submit" class="btn">Search</button>
                        </div>
                    </form>
                </div>
                
                <!-- Popular Posts -->
                <div class="sidebar-card">
                    <h3>Popular Posts</h3>
                    <div class="popular-posts">
                        <?php 
                        // Get random posts for demonstration
                        $popularPosts = array_slice($blogPosts, 0, 3);
                        foreach ($popularPosts as $post): 
                        ?>
                            <div class="popular-post">
                                <div class="popular-post-image" style="background-image: url('images/<?php echo $post['image']; ?>'); background-color: #ddd;"></div>
                                <div class="popular-post-content">
                                    <h4><a href="blog-post.php?id=<?php echo $post['post_id']; ?>"><?php echo $post['title']; ?></a></h4>
                                    <p><?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="sidebar-card">
                    <h3>Categories</h3>
                    <ul class="categories-list">
                        <li><a href="blog-category.php?category=travel-tips">Travel Tips <span>(12)</span></a></li>
                        <li><a href="blog-category.php?category=destination-guides">Destination Guides <span>(8)</span></a></li>
                        <li><a href="blog-category.php?category=moving-advice">Moving Advice <span>(6)</span></a></li>
                        <li><a href="blog-category.php?category=city-living">City Living <span>(4)</span></a></li>
                        <li><a href="blog-category.php?category=neighborhood-spotlights">Neighborhood Spotlights <span>(7)</span></a></li>
                    </ul>
                </div>
                
                <!-- Tags -->
                <div class="sidebar-card">
                    <h3>Tags</h3>
                    <div class="tags-list">
                        <a href="blog-tag.php?tag=travel" class="tag">Travel</a>
                        <a href="blog-tag.php?tag=moving" class="tag">Moving</a>
                        <a href="blog-tag.php?tag=relocation" class="tag">Relocation</a>
                        <a href="blog-tag.php?tag=city-guide" class="tag">City Guide</a>
                        <a href="blog-tag.php?tag=neighborhoods" class="tag">Neighborhoods</a>
                        <a href="blog-tag.php?tag=commuting" class="tag">Commuting</a>
                        <a href="blog-tag.php?tag=apartments" class="tag">Apartments</a>
                        <a href="blog-tag.php?tag=real-estate" class="tag">Real Estate</a>
                    </div>
                </div>
                
                <!-- Newsletter -->
                <div class="sidebar-card">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest travel tips and city guides.</p>
                    <form action="subscribe.php" method="POST" class="newsletter-form">
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit" class="btn">Subscribe</button>
                    </form>
                </div>
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
