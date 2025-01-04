<?php
// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'db_connect.php';

// Fetch statistics
$totalSalesQuery = "SELECT SUM(total_price) AS total_sales FROM orders WHERE status = 'Completed'";
$activeUsersQuery = "SELECT COUNT(*) AS active_users FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
$stockLevelsQuery = "SELECT SUM(stock) AS total_stock FROM books";
$pendingOrdersQuery = "SELECT COUNT(*) AS pending_orders FROM orders WHERE status = 'Pending'";

// Fetch random book quote
$quotes = [
    "“A room without books is like a body without a soul.” – Marcus Tullius Cicero",
    "“Books are a uniquely portable magic.” – Stephen King",
    "“There is no friend as loyal as a book.” – Ernest Hemingway",
    "“Good friends, good books, and a sleepy conscience: this is the ideal life.” – Mark Twain",
    "“So many books, so little time.” – Frank Zappa"
];

// Randomly select a quote
$randomQuote = $quotes[array_rand($quotes)];

$totalSales = $conn->query($totalSalesQuery)->fetch_assoc()["total_sales"];
$activeUsers = $conn->query($activeUsersQuery)->fetch_assoc()["active_users"];
$totalStock = $conn->query($stockLevelsQuery)->fetch_assoc()["total_stock"];
$pendingOrders = $conn->query($pendingOrdersQuery)->fetch_assoc()["pending_orders"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="static/css/admin.css"> <!-- Add your CSS file -->
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <?php include('admin_navbar.php'); ?>
    
    <div class="welcome-message">
        <h2>Welcome, Admin!</h2>
        <p>Your dashboard is the hub for managing the bookstore. Here's a quick overview:</p>
    </div>
    
    <div class="stats">
        <div>Total Sales: $<?php echo $totalSales; ?></div>
        <div>Active Users: <?php echo $activeUsers; ?></div>
        <div>Total Stock Levels: <?php echo $totalStock; ?> items</div>
        <div>Pending Orders: <?php echo $pendingOrders; ?></div>
    </div>
    
    <div class="quote-section">
        <h3>Book of the Day</h3>
        <p><i><?php echo $randomQuote; ?></i></p>
    </div>

    <div class="recent-activity">
        <h3>Recent Activities</h3>
        <ul>
            <li>New order placed: #12345 - Pending</li>
            <li>New user registered: John Doe</li>
            <li>Stock update: 50 new books added to the collection</li>
        </ul>
    </div>

</body>
</html>
