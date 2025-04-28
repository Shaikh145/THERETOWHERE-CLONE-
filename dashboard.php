<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user information
$userId = $_SESSION['user_id'];
$user = fetchOne("SELECT * FROM users WHERE user_id = ?", [$userId]);

// Get user's bookings
$bookings = fetchAll("
    SELECT b.*, 
           CASE 
               WHEN b.booking_type = 'flight' THEN (SELECT CONCAT(airline, ': ', departure_city, ' to ', arrival_city) FROM flights WHERE flight_id = b.reference_id)
               WHEN b.booking_type = 'hotel' THEN (SELECT name FROM hotels WHERE hotel_id = b.reference_id)
               WHEN b.booking_type = 'package' THEN (SELECT name FROM packages WHERE package_id = b.reference_id)
           END AS booking_name
    FROM bookings b
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
", [$userId]);

// Get user's saved items
$savedItems = fetchAll("
    SELECT si.*, 
           CASE 
               WHEN si.item_type = 'destination' THEN (SELECT name FROM destinations WHERE destination_id = si.item_id)
               WHEN si.item_type = 'hotel' THEN (SELECT name FROM hotels WHERE hotel_id = si.item_id)
               WHEN si.item_type = 'package' THEN (SELECT name FROM packages WHERE package_id = si.item_id)
           END AS item_name
    FROM saved_items si
    WHERE si.user_id = ?
    ORDER BY si.saved_at DESC
", [$userId]);

// Get user's saved heatmaps
$heatmaps = fetchAll("SELECT * FROM heatmaps WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Theretowhere</title>
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
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            color: #555;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #9c27b0;
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-menu-button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            color: #333;
            font-size: 16px;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #9c27b0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .user-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 200px;
            z-index: 10;
            display: none;
        }
        
        .user-menu-dropdown.active {
            display: block;
        }
        
        .user-menu-dropdown ul {
            list-style: none;
        }
        
        .user-menu-dropdown ul li {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .user-menu-dropdown ul li:last-child {
            border-bottom: none;
        }
        
        .user-menu-dropdown ul li a {
            color: #333;
            display: block;
        }
        
        .user-menu-dropdown ul li a:hover {
            color: #9c27b0;
        }
        
        /* Dashboard styles */
        .dashboard-header {
            background-color: #9c27b0;
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .dashboard-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 30px;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .dashboard-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .dashboard-card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-card-header h2 {
            font-size: 18px;
            color: #333;
        }
        
        .dashboard-card-content {
            padding: 20px;
        }
        
        .booking-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .booking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-radius: 4px;
            background-color: #f9f9f9;
            transition: transform 0.3s;
        }
        
        .booking-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .booking-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .booking-meta {
            font-size: 14px;
            color: #666;
        }
        
        .booking-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-confirmed {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .status-pending {
            background-color: #fff8e1;
            color: #ffa000;
        }
        
        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .saved-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .saved-item {
            background-color: #f9f9f9;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .saved-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .saved-item h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .saved-item p {
            font-size: 14px;
            color: #666;
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
        
        .btn-sm {
            font-size: 12px;
            padding: 5px 10px;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #9c27b0;
            color: #9c27b0;
        }
        
        .btn-outline:hover {
            background-color: #f3e5f5;
        }
        
        .profile-card {
            text-align: center;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #9c27b0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            margin: 0 auto 15px;
        }
        
        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .profile-username {
            color: #666;
            margin-bottom: 15px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .profile-stat {
            text-align: center;
        }
        
        .profile-stat-value {
            font-size: 18px;
            font-weight: bold;
        }
        
        .profile-stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .quick-links {
            list-style: none;
        }
        
        .quick-links li {
            margin-bottom: 10px;
        }
        
        .quick-links li a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            transition: color 0.3s;
        }
        
        .quick-links li a:hover {
            color: #9c27b0;
        }
        
        .quick-links li a i {
            width: 20px;
            text-align: center;
        }
        
        .heatmap-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .heatmap-item {
            background-color: #f9f9f9;
            border-radius: 4px;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .heatmap-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .heatmap-meta {
            font-size: 14px;
            color: #666;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
        .empty-state p {
            margin-bottom: 15px;
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
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="index.php" class="logo">Theretowhere</a>
            <div class="nav-links">
                <a href="destinations.php">Destinations</a>
                <a href="packages.php">Packages</a>
                <a href="heatmaps.php">Heatmaps</a>
                <a href="blog.php">Travel Guide</a>
            </div>
            <div class="user-menu">
                <button class="user-menu-button" id="userMenuButton">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo $user['username']; ?></span>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="profile.php">My Profile</a></li>
                        <li><a href="bookings.php">My Bookings</a></li>
                        <li><a href="saved-items.php">Saved Items</a></li>
                        <li><a href="settings.php">Account Settings</a></li>
                        <li><a href="logout.php">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Dashboard Header -->
    <section class="dashboard-header">
        <div class="container">
            <h1>Welcome, <?php echo $user['first_name'] ?? $user['username']; ?>!</h1>
            <p>Manage your trips, saved destinations, and preferences</p>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3><?php echo count($bookings); ?></h3>
                    <p>Bookings</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count($savedItems); ?></h3>
                    <p>Saved Items</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count($heatmaps); ?></h3>
                    <p>Heatmaps</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Dashboard Content -->
    <section class="container">
        <div class="dashboard-grid">
            <div class="main-content">
                <!-- Recent Bookings -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2>Recent Bookings</h2>
                        <a href="bookings.php" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="dashboard-card-content">
                        <?php if (count($bookings) > 0): ?>
                            <div class="booking-list">
                                <?php foreach (array_slice($bookings, 0, 3) as $booking): ?>
                                    <div class="booking-item">
                                        <div class="booking-info">
                                            <h3><?php echo $booking['booking_name']; ?></h3>
                                            <div class="booking-meta">
                                                <?php echo date('M d, Y', strtotime($booking['start_date'])); ?> - 
                                                <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                                            </div>
                                        </div>
                                        <div class="booking-status status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>You don't have any bookings yet.</p>
                                <a href="packages.php" class="btn">Explore Packages</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Saved Items -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2>Saved Items</h2>
                        <a href="saved-items.php" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="dashboard-card-content">
                        <?php if (count($savedItems) > 0): ?>
                            <div class="saved-list">
                                <?php foreach (array_slice($savedItems, 0, 4) as $item): ?>
                                    <div class="saved-item">
                                        <h3><?php echo $item['item_name']; ?></h3>
                                        <p><?php echo ucfirst($item['item_type']); ?></p>
                                        <a href="<?php echo $item['item_type']; ?>.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm">View Details</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>You haven't saved any items yet.</p>
                                <a href="destinations.php" class="btn">Explore Destinations</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Saved Heatmaps -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2>Your Heatmaps</h2>
                        <a href="heatmaps.php" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="dashboard-card-content">
                        <?php if (count($heatmaps) > 0): ?>
                            <div class="heatmap-list">
                                <?php foreach (array_slice($heatmaps, 0, 3) as $heatmap): ?>
                                    <div class="heatmap-item">
                                        <div class="heatmap-info">
                                            <h3><?php echo $heatmap['name']; ?></h3>
                                            <div class="heatmap-meta">
                                                <?php echo $heatmap['city']; ?> • 
                                                Created <?php echo date('M d, Y', strtotime($heatmap['created_at'])); ?>
                                            </div>
                                        </div>
                                        <a href="view-heatmap.php?id=<?php echo $heatmap['heatmap_id']; ?>" class="btn btn-sm">View</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>You haven't created any heatmaps yet.</p>
                                <a href="create-heatmap.php" class="btn">Create Heatmap</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="sidebar">
                <!-- Profile Card -->
                <div class="dashboard-card">
                    <div class="dashboard-card-content profile-card">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)); ?>
                        </div>
                        <div class="profile-name">
                            <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                        </div>
                        <div class="profile-username">
                            @<?php echo $user['username']; ?>
                        </div>
                        <div class="profile-stats">
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo count($bookings); ?></div>
                                <div class="profile-stat-label">Bookings</div>
                            </div>
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo count($savedItems); ?></div>
                                <div class="profile-stat-label">Saved</div>
                            </div>
                            <div class="profile-stat">
                                <div class="profile-stat-value"><?php echo count($heatmaps); ?></div>
                                <div class="profile-stat-label">Heatmaps</div>
                            </div>
                        </div>
                        <a href="profile.php" class="btn btn-outline">Edit Profile</a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2>Quick Links</h2>
                    </div>
                    <div class="dashboard-card-content">
                        <ul class="quick-links">
                            <li><a href="create-heatmap.php"><i>🔥</i> Create New Heatmap</a></li>
                            <li><a href="distance-matrix.php"><i>📏</i> Distance Matrix</a></li>
                            <li><a href="bookings.php"><i>🧳</i> Manage Bookings</a></li>
                            <li><a href="saved-items.php"><i>❤️</i> Saved Items</a></li>
                            <li><a href="settings.php"><i>⚙️</i> Account Settings</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Recommended Destinations -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2>Recommended For You</h2>
                    </div>
                    <div class="dashboard-card-content">
                        <div class="saved-list">
                            <?php 
                            // Get random destinations for recommendations
                            $recommendations = fetchAll("SELECT * FROM destinations ORDER BY RAND() LIMIT 2");
                            foreach ($recommendations as $rec): 
                            ?>
                                <div class="saved-item">
                                    <h3><?php echo $rec['name']; ?></h3>
                                    <p><?php echo $rec['city'] . ', ' . $rec['country']; ?></p>
                                    <a href="destination.php?id=<?php echo $rec['destination_id']; ?>" class="btn btn-sm">Explore</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
        // JavaScript for user menu dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenuDropdown = document.getElementById('userMenuDropdown');
            
            userMenuButton.addEventListener('click', function() {
                userMenuDropdown.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                    userMenuDropdown.classList.remove('active');
                }
            });
            
            // JavaScript for redirection
            function redirectTo(url) {
                window.location.href = url;
            }
        });
    </script>
</body>
</html>
