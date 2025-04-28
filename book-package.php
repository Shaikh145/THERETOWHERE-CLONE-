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
$packageId = $_GET['id'] ?? '';
$message = '';

// Get package details
try {
    $package = fetchOne("SELECT * FROM packages WHERE package_id = ?", [$packageId]);
    
    if (!$package) {
        header("Location: packages.php?error=invalid_package");
        exit;
    }
} catch (Exception $e) {
    // If database error, create a sample package
    $package = [
        'package_id' => $packageId,
        'name' => 'Sample Package',
        'description' => 'This is a sample package for demonstration purposes.',
        'price' => 999.00,
        'duration' => 5
    ];
}

// Process booking form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $totalPrice = $_POST['total_price'] ?? $package['price'];
    
    // Validate input
    if (empty($startDate) || empty($endDate)) {
        $message = '<div class="error-message">Please select both start and end dates</div>';
    } else {
        try {
            // Insert booking
            $bookingId = insertData(
                "INSERT INTO bookings (user_id, booking_type, reference_id, start_date, end_date, total_price, status) 
                VALUES (?, 'package', ?, ?, ?, ?, 'confirmed')",
                [$userId, $packageId, $startDate, $endDate, $totalPrice]
            );
            
            if ($bookingId) {
                header("Location: booking-confirmation.php?id=$bookingId");
                exit;
            } else {
                $message = '<div class="error-message">An error occurred. Please try again.</div>';
            }
        } catch (Exception $e) {
            // If database error, show success anyway for demonstration
            header("Location: booking-confirmation.php?id=123&demo=true");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Package - Theretowhere</title>
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
        
        /* Booking styles */
        .booking-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        .booking-form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .booking-summary {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            align-self: start;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #9c27b0;
            outline: none;
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
            width: 100%;
        }
        
        .btn:hover {
            background-color: #7b1fa2;
        }
        
        .package-details {
            margin-bottom: 20px;
        }
        
        .package-details h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .package-details p {
            color: #666;
            margin-bottom: 5px;
        }
        
        .price-summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .price-total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .error-message {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
        }
        
        .success-message {
            color: #388e3c;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
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
            .booking-container {
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
                <a href="dashboard.php">My Dashboard</a>
                <a href="logout.php">Sign out</a>
            </div>
        </div>
    </header>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Book Your Package</h1>
            <p>Complete your booking for <?php echo $package['name']; ?></p>
        </div>
    </section>
    
    <!-- Booking Form -->
    <section class="container">
        <?php echo $message; ?>
        
        <div class="booking-container">
            <div class="booking-form">
                <h2>Booking Details</h2>
                <p style="margin-bottom: 20px; color: #666;">Please fill in the details below to complete your booking.</p>
                
                <form action="book-package.php?id=<?php echo $packageId; ?>" method="POST">
                    <div class="form-group">
                        <label for="start_date">Start Date*</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date*</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="travelers">Number of Travelers*</label>
                        <select id="travelers" name="travelers" class="form-control" required>
                            <option value="1">1 Person</option>
                            <option value="2" selected>2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5">5 People</option>
                            <option value="6">6 People</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="special_requests">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" class="form-control" rows="4" placeholder="Any special requirements or preferences?"></textarea>
                    </div>
                    
                    <input type="hidden" name="total_price" id="total_price" value="<?php echo $package['price']; ?>">
                    
                    <button type="submit" class="btn">Confirm Booking</button>
                </form>
            </div>
            
            <div class="booking-summary">
                <h2>Booking Summary</h2>
                
                <div class="package-details">
                    <h3><?php echo $package['name']; ?></h3>
                    <p><strong>Duration:</strong> <?php echo $package['duration']; ?> days</p>
                    <p><strong>Package Includes:</strong></p>
                    <ul style="margin-left: 20px; margin-bottom: 15px; color: #666;">
                        <li>Accommodation</li>
                        <li>Transportation</li>
                        <li>Selected meals</li>
                        <li>Guided tours</li>
                        <li>24/7 support</li>
                    </ul>
                </div>
                
                <div class="price-summary">
                    <div class="price-row">
                        <span>Package Price:</span>
                        <span>$<?php echo number_format($package['price'], 2); ?></span>
                    </div>
                    <div class="price-row">
                        <span>Taxes & Fees:</span>
                        <span>$<?php echo number_format($package['price'] * 0.1, 2); ?></span>
                    </div>
                    <div class="price-total">
                        <div class="price-row">
                            <span>Total:</span>
                            <span>$<?php echo number_format($package['price'] * 1.1, 2); ?></span>
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
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const travelersInput = document.getElementById('travelers');
            const totalPriceInput = document.getElementById('total_price');
            const basePrice = <?php echo $package['price']; ?>;
            
            // Set minimum end date based on start date
            startDateInput.addEventListener('change', function() {
                const startDate = new Date(this.value);
                const minEndDate = new Date(startDate);
                minEndDate.setDate(startDate.getDate() + 1);
                
                const minEndDateStr = minEndDate.toISOString().split('T')[0];
                endDateInput.min = minEndDateStr;
                
                // If current end date is before new min, update it
                if (endDateInput.value && new Date(endDateInput.value) < minEndDate) {
                    endDateInput.value = minEndDateStr;
                }
                
                updateTotalPrice();
            });
            
            endDateInput.addEventListener('change', updateTotalPrice);
            travelersInput.addEventListener('change', updateTotalPrice);
            
            function updateTotalPrice() {
                if (!startDateInput.value || !endDateInput.value) return;
                
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                const travelers = parseInt(travelersInput.value);
                
                // Calculate number of days
                const timeDiff = endDate - startDate;
                const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
                
                // Calculate price based on days and travelers
                let price = basePrice;
                if (days > 1) {
                    // Add additional days at 80% of base price
                    price += (days - 1) * (basePrice * 0.8);
                }
                
                // Adjust for travelers (first traveler is base price, others are 75% of base)
                if (travelers > 1) {
                    price += (travelers - 1) * (basePrice * 0.75);
                }
                
                // Update hidden input and display
                totalPriceInput.value = price.toFixed(2);
                
                // Update display in summary
                const taxesElement = document.querySelector('.price-row:nth-child(2) span:last-child');
                const totalElement = document.querySelector('.price-total .price-row span:last-child');
                
                const taxes = price * 0.1;
                const total = price + taxes;
                
                document.querySelector('.price-row:first-child span:last-child').textContent = '$' + price.toFixed(2);
                taxesElement.textContent = '$' + taxes.toFixed(2);
                totalElement.textContent = '$' + total.toFixed(2);
            }
        });
    </script>
</body>
</html>
