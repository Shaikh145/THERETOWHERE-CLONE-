<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get post ID
$postId = $_GET['id'] ?? '';

// Get blog post details
try {
    $post = fetchOne("SELECT * FROM blog_posts WHERE post_id = ?", [$postId]);
    
    if (!$post) {
        header("Location: blog.php");
        exit;
    }
} catch (Exception $e) {
    // If there's an error, create a sample post
    $post = [
        'post_id' => $postId ?: 1,
        'title' => 'Top 10 Paris Attractions',
        'content' => '<p>Paris, the City of Light, is renowned for its stunning architecture, art museums, historical monuments, and romantic ambiance. Here are the top 10 attractions you must visit when in Paris:</p>

<h3>1. Eiffel Tower</h3>
<p>The iconic Eiffel Tower is a must-visit landmark. Built by Gustave Eiffel for the 1889 World\'s Fair, it offers breathtaking views of the city from its observation decks. For the best experience, visit at sunset and stay to see the tower sparkle with lights every hour after dark.</p>

<h3>2. Louvre Museum</h3>
<p>Home to thousands of works of art, including the famous Mona Lisa and Venus de Milo, the Louvre is the world\'s largest art museum. The glass pyramid entrance designed by I.M. Pei is an architectural marvel in itself.</p>

<h3>3. Notre-Dame Cathedral</h3>
<p>This medieval Catholic cathedral is a masterpiece of French Gothic architecture. While the interior is currently closed for restoration after the 2019 fire, the exterior is still worth admiring.</p>

<h3>4. Arc de Triomphe</h3>
<p>Standing at the center of Place Charles de Gaulle, this triumphal arch honors those who fought for France. Climb to the top for panoramic views of the 12 radiating avenues, including the Champs-Élysées.</p>

<h3>5. Montmartre and Sacré-Cœur</h3>
<p>This charming hilltop neighborhood offers artistic history and stunning views. Visit the white-domed Sacré-Cœur Basilica, explore the Place du Tertre where artists set up their easels, and wander the winding streets that inspired many famous painters.</p>

<h3>6. Musée d\'Orsay</h3>
<p>Housed in a former railway station, this museum features an impressive collection of Impressionist and Post-Impressionist masterpieces by artists like Monet, Renoir, and Van Gogh.</p>

<h3>7. Seine River Cruise</h3>
<p>A boat tour along the Seine offers a unique perspective of Paris\'s landmarks. Many of the city\'s famous monuments are along the riverbanks, making this a relaxing way to sightsee.</p>

<h3>8. Champs-Élysées and Place de la Concorde</h3>
<p>Stroll down this famous avenue from the Arc de Triomphe to Place de la Concorde, enjoying luxury shops, cafes, and theaters along the way.</p>

<h3>9. Centre Pompidou</h3>
<p>This inside-out building houses the National Museum of Modern Art. Its unique architecture features exposed structural elements and mechanical systems coded by color.</p>

<h3>10. Luxembourg Gardens</h3>
<p>These beautiful gardens surrounding the Luxembourg Palace offer a peaceful retreat from the city\'s hustle and bustle. Enjoy the meticulously manicured lawns, flower beds, and fountains.</p>

<p>When planning your Paris itinerary, consider purchasing a Paris Museum Pass for access to many of these attractions. Also, try to visit popular sites early in the morning or during weekday evenings to avoid crowds.</p>',
        'image' => 'paris_blog.jpg',
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
    ];
}

// Get related posts
try {
    $relatedPosts = fetchAll("SELECT * FROM blog_posts WHERE post_id != ? ORDER BY created_at DESC LIMIT 3", [$postId]);
} catch (Exception $e) {
    // If there's an error, create sample related posts
    $relatedPosts = [
        [
            'post_id' => 2,
            'title' => 'New York on a Budget',
            'content' => 'Visiting New York City doesn\'t have to break the bank. With some planning and insider knowledge, you can experience the best of the Big Apple without spending a fortune...',
            'image' => 'nyc_budget.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ],
        [
            'post_id' => 3,
            'title' => 'Tokyo Travel Guide',
            'content' => 'Tokyo, Japan\'s busy capital, mixes the ultramodern and the traditional, from neon-lit skyscrapers to historic temples...',
            'image' => 'tokyo_guide.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
        ],
        [
            'post_id' => 4,
            'title' => 'Hidden Gems in Rome',
            'content' => 'While the Colosseum and Vatican are must-sees in Rome, the Eternal City has countless hidden treasures waiting to be discovered...',
            'image' => 'rome_hidden.jpg',
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title']; ?> - Theretowhere</title>
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
        
        /* Blog post styles */
        .blog-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 30px;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .blog-main {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .blog-image {
            width: 100%;
            height: 400px;
            background-size: cover;
            background-position: center;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .blog-content {
            padding: 30px;
        }
        
        .blog-title {
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .blog-meta {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .blog-meta span {
            margin-right: 15px;
        }
        
        .blog-text {
            color: #444;
            font-size: 16px;
            line-height: 1.8;
        }
        
        .blog-text p {
            margin-bottom: 20px;
        }
        
        .blog-text h3 {
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .blog-text ul, .blog-text ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        .blog-text li {
            margin-bottom: 10px;
        }
        
        .blog-sidebar {
            display: flex;
            flex-direction: column;
            gap: 30px;
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
        
        .related-posts {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .related-post {
            display: flex;
            gap: 10px;
        }
        
        .related-post-image {
            width: 80px;
            height: 60px;
            border-radius: 4px;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .related-post-content h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .related-post-content p {
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
        
        .share-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .share-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            font-size: 18px;
            transition: opacity 0.3s;
        }
        
        .share-button:hover {
            opacity: 0.8;
        }
        
        .facebook {
            background-color: #3b5998;
        }
        
        .twitter {
            background-color: #1da1f2;
        }
        
        .linkedin {
            background-color: #0077b5;
        }
        
        .pinterest {
            background-color: #bd081c;
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
            padding: 10px;
            background-color: #9c27b0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .newsletter-form button:hover {
            background-color: #7b1fa2;
        }
        
        .author-card {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .author-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #9c27b0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
        }
        
        .author-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .author-info p {
            color: #666;
            font-size: 14px;
        }
        
        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 30px 0;
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
            
            .blog-image {
                height: 250px;
            }
            
            .blog-title {
                font-size: 26px;
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
    
    <!-- Blog Post Content -->
    <section class="container blog-container">
        <div class="blog-main">
            <div class="blog-image" style="background-image: url('images/<?php echo $post['image']; ?>'); background-color: #ddd;"></div>
            <div class="blog-content">
                <h1 class="blog-title"><?php echo $post['title']; ?></h1>
                <div class="blog-meta">
                    <span><i>📅</i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                    <span><i>👤</i> By Admin</span>
                    <span><i>🏷️</i> Travel Tips</span>
                </div>
                <div class="blog-text">
                    <?php echo $post['content']; ?>
                </div>
                
                <div class="author-card">
                    <div class="author-avatar">A</div>
                    <div class="author-info">
                        <h3>Admin</h3>
                        <p>Travel enthusiast and content creator at Theretowhere. Passionate about exploring new destinations and sharing travel tips.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="blog-sidebar">
            <!-- Search -->
            <div class="sidebar-card">
                <h3>Search</h3>
                <form action="blog-search.php" method="GET">
                    <div class="newsletter-form">
                        <input type="text" name="q" placeholder="Search articles...">
                        <button type="submit">Search</button>
                    </div>
                </form>
            </div>
            
            <!-- Related Posts -->
            <div class="sidebar-card">
                <h3>Related Posts</h3>
                <div class="related-posts">
                    <?php foreach ($relatedPosts as $relatedPost): ?>
                        <div class="related-post">
                            <div class="related-post-image" style="background-image: url('images/<?php echo $relatedPost['image']; ?>'); background-color: #ddd;"></div>
                            <div class="related-post-content">
                                <h4><a href="blog-post.php?id=<?php echo $relatedPost['post_id']; ?>"><?php echo $relatedPost['title']; ?></a></h4>
                                <p><?php echo date('M j, Y', strtotime($relatedPost['created_at'])); ?></p>
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
            
            <!-- Share -->
            <div class="sidebar-card">
                <h3>Share This Post</h3>
                <div class="share-buttons">
                    <a href="#" class="share-button facebook">f</a>
                    <a href="#" class="share-button twitter">t</a>
                    <a href="#" class="share-button linkedin">in</a>
                    <a href="#" class="share-button pinterest">p</a>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="sidebar-card">
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for the latest travel tips and city guides.</p>
                <form action="subscribe.php" method="POST" class="newsletter-form">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
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
            // Example: Add click event to all buttons
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.form && this.form.getAttribute('action')) {
                        e.preventDefault();
                        redirectTo(this.form.getAttribute('action') + '?' + new URLSearchParams(new FormData(this.form)).toString());
                    }
                });
            });
            
            // Share buttons functionality
            const shareButtons = document.querySelectorAll('.share-button');
            shareButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = encodeURIComponent(window.location.href);
                    const title = encodeURIComponent(document.title);
                    
                    let shareUrl = '';
                    if (this.classList.contains('facebook')) {
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                    } else if (this.classList.contains('twitter')) {
                        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                    } else if (this.classList.contains('linkedin')) {
                        shareUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`;
                    } else if (this.classList.contains('pinterest')) {
                        const image = encodeURIComponent(document.querySelector('.blog-image').style.backgroundImage.slice(5, -2));
                        shareUrl = `https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`;
                    }
                    
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                });
            });
        });
    </script>
</body>
</html>
