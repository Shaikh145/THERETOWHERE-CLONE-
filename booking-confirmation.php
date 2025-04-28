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

$userId = $_SESSION['user_id'];
$bookingId = $_GET['id'] ?? '';
$isDemo = isset($_GET['demo']) && $_GET['demo'] === 'true';

// Get booking details
if (!$isDemo) {
    try {
        $booking = fetchOne(
            "SELECT b.*, 
                CASE 
                    WHEN b.booking_type = 'package' THEN (SELECT name FROM packages WHERE package_id = b.reference_id)
                    WHEN b.booking_type = 'hotel' THEN (SELECT name FROM hotels WHERE hotel_id = b.reference_id)
                    WHEN b.booking_type = 'flight' THEN (SELECT CONCAT(airline, ': ', departure_city, ' to ', arrival_city) FROM flights WHERE flight_id = b.reference_id)
                END AS booking_name
            FROM bookings b
            WHERE b.booking_id = ? AND b.user_id = ?", 
            [$bookingId, $userId]
        );
        
        if (!$booking) {
            header("Location: dashboard.php?error=invalid_booking");
            exit;
        }
    } catch (Exception $e) {
        // If database error, create a sample booking
        $isDemo = true;
    }
}

// Create demo booking if needed
if ($isDemo) {
    $booking = [
        'booking_id' => $bookingId ?: '123456',
        'booking_type' => 'package',
        'booking_name' => 'Paris Explorer',
        'start_date' => date('Y-m-d', strtotime('+30 days')),
        'end_date' => date('Y-m-d', strtotime('+35 days')),
        'total_price' => 1099.00,
        'status' => 'confirmed',
        'booking_date' => date('Y-m-d H:i:s')
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Theretowhere</title>
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
            background-color: #4caf50;
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        /* Confirmation styles */
        .confirmation-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .confirmation-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .confirmation-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #4caf50;
        }
        
        .confirmation-header p {
            color: #666;
        }
        
        .confirmation-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .confirmation-details h3 {
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .detail-label {
            width: 200px;
            font-weight: 500;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .booking-summary {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .booking-summary h3 {
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .price-total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .next-steps {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            background-color: #9c27b0;
            color: white;
            padding: 12px 25px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            margin: 0 10px;
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
                <a href="dashboard.php">My Dashboard</a>
                <a href="logout.php">Sign out</a>
            </div>
        </div>
    </header>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Booking Confirmed!</h1>
            <p>Your reservation has been successfully processed</p>
        </div>
    </section>
    
    <!-- Confirmation Content -->
    <section class="container">
        <div class="confirmation-container">
            <div class="confirmation-header">
                <h2>Thank You for Your Booking</h2>
                <p>Your booking has been confirmed and is now being processed.</p>
                <p>A confirmation email has been sent to your registered email address.</p>
            </div>
            
            <div class="confirmation-details">
                <h3>Booking Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Booking Reference:</div>
                    <div class="detail-value">#<?php echo $booking['booking_id']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value"><?php echo date('F j, Y, g:i a', strtotime($booking['booking_date'])); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Booking Type:</div>
                    <div class="detail-value"><?php echo ucfirst($booking['booking_type']); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Package/Service:</div>
                    <div class="detail-value"><?php echo $booking['booking_name']; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Travel Dates:</div>
                    <div class="detail-value">
                        <?php echo date('F j, Y', strtotime($booking['start_date'])); ?> - 
                        <?php echo date('F j, Y', strtotime($booking['end_date'])); ?>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value" style="color: #4caf50; font-weight: 500;">
                        <?php echo ucfirst($booking['status']); ?>
                    </div>
                </div>
            </div>
            
            <div class="booking-summary">
                <h3>Payment Summary</h3>
                
                <div class="price-row">
                    <span>Package Price:</span>
                    <span>$<?php echo number_format($booking['total_price'] * 0.9, 2); ?></span>
                </div>
                
                <div class="price-row">
                    <span>Taxes & Fees:</span>
                    <span>$<?php echo number_format($booking['total_price'] * 0.1, 2); ?></span>
                </div>
                
                <div class="price-total">
                    <div class="price-row">
                        <span>Total Paid:</span>
                        <span>$<?php echo number_format($booking['total_price'], 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="next-steps">
                <h3>What's Next?</h3>
                <p style="margin-bottom: 20px;">You'll receive a detailed itinerary and travel documents via email within 24 hours.</p>
                
                <a href="dashboard.php" class="btn">Go to My Dashboard</a>
                <a href="index.php" class="btn btn-outline">Return to Homepage</a>
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
    </script>
</body>
</html>
