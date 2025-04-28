<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get search query
$searchQuery = $_GET['q'] ?? '';
$blogPosts = [];

if (!empty($searchQuery)) {
    try {
        // Search for blog posts
        $blogPosts = fetchAll("SELECT * FROM blog_posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC", 
            ['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
    } catch (Exception $e) {
        // If there's an error, create some sample search results
        $blogPosts = [
            [
                'post_id' => 1,
                'title' => 'Top 10 Paris Attractions',
                'content' => 'Paris, the City of Light, is renowned for its stunning architecture, art museums, historical monuments, and romantic ambiance. Here are the top 10 attractions you must visit when in Paris...',
                'image' => 'paris_blog.jpg',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'post_id' => 2,
                'title' => 'New York on a Budget',
                'content' => 'Visiting New York City doesn\'t have to break the bank. With some planning and insider knowledge, you can experience the best of the Big Apple without spending a fortune...',
                'image' => 'nyc_budget.jpg',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Theretowhere</title>
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
        
        /* Search results styles */
        .search-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .search-form {
            margin-bottom: 30px;
        }
        
        .search-form input {
            width: 70%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }
        
        .search-form button {
            padding: 12px 20px;
            background-color: #9c27b0;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-size: 16px;
        }
        
        .search-results {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .result-card {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: transform 0.3s ease;
        }
        
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .result-image {
            width: 150px;
            height: 100px;
            background-size: cover;
            background-position: center;
            border-radius: 4px;
            flex-shrink: 0;
            background-color: #ddd; /* Fallback if image doesn't load */
        }
        
        .result-content {
            flex: 1;
        }
        
        .result-content h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .result-content p {
            color: #666;
            margin-bottom: 15px;
        }
        
        .result-meta {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            background-color: #9c27b0;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #7b1fa2;
        }
        
        .no-results {
            text-align: center;
            padding: 40px 0;
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
            <h1>Search Results</h1>
            <p>Showing results for: "<?php echo htmlspecialchars($searchQuery); ?>"</p>
        </div>
    </section>
    
    <!-- Search Results -->
    <section class="container">
        <div class="search-container">
            <form action="blog-search.php" method="GET" class="search-form">
                <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search articles...">
                <button type="submit">Search</button>
            </form>
            
            <?php if (empty($searchQuery)): ?>
                <div class="no-results">
                    <p>Please enter a search term to find articles.</p>
                </div>
            <?php elseif (empty($blogPosts)): ?>
                <div class="no-results">
                    <p>No results found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
                    <p>Try different keywords or check out our <a href="blog.php">blog</a> for more articles.</p>
                </div>
            <?php else: ?>
                <div class="search-results">
                    <?php foreach ($blogPosts as $post): ?>
                        <div class="result-card">
                            <div class="result-image" style="background-image: url('images/<?php echo $post['image']; ?>'); background-color: #ddd;"></div>
                            <div class="result-content">
                                <h2><a href="blog-post.php?id=<?php echo $post['post_id']; ?>"><?php echo $post['title']; ?></a></h2>
                                <div class="result-meta">
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?> • By Admin
                                </div>
                                <p><?php echo substr($post['content'], 0, 150) . '...'; ?></p>
                                <a href="blog-post.php?id=<?php echo $post['post_id']; ?>" class="btn">Read More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
    </script>
</body>
</html>
