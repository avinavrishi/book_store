<?php
// Start the session
session_start();

// Include database connection
require_once('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Check if the order was successfully placed
if (!isset($_SESSION['order_success']) || !$_SESSION['order_success']) {
    header("Location: cart.php");
    exit();
}

// Clear the order success session flag
unset($_SESSION['order_success']);

// Get the last order ID for the user
$userId = $_SESSION['user_id'];

// Fetch the latest order for the user along with the shipping information from shipping_addresses
$query = "SELECT o.*, sa.recipient_name, sa.address_line1, sa.address_line2, sa.city, sa.state, sa.postal_code, sa.country, sa.phone 
          FROM orders o
          JOIN shipping_addresses sa ON o.order_id = sa.order_id
          WHERE o.user_id = '$userId' 
          ORDER BY o.order_date DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

// If no order found, redirect to the cart page
if (!$order) {
    header("Location: cart.php");
    exit();
}

// Fetch the order details (books in the order)
$query = "SELECT od.*, b.title, b.price FROM order_details od JOIN books b ON od.book_id = b.book_id WHERE od.order_id = '{$order['order_id']}'";
$result = mysqli_query($conn, $query);
$orderDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="static/css/order_confirmation.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1>Order Confirmation</h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <main>
        <h2>Thank you for your order!</h2>
        <p>Your order has been successfully placed. Below are your order details:</p>

        <section class="order-details">
            <h3>Order #<?php echo $order['order_id']; ?></h3>
            <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
            <p>Order Date: <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
        </section>

        <!-- Shipping Information -->
        <section class="shipping-details">
            <h3>Shipping Information</h3>
            <p><strong>Recipient Name:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
            <p><strong>Address Line 1:</strong> <?php echo htmlspecialchars($order['address_line1']); ?></p>
            <?php if (!empty($order['address_line2'])) { ?>
                <p><strong>Address Line 2:</strong> <?php echo htmlspecialchars($order['address_line2']); ?></p>
            <?php } ?>
            <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
            <p><strong>State:</strong> <?php echo htmlspecialchars($order['state']); ?></p>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($order['postal_code']); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($order['country']); ?></p>
            <?php if (!empty($order['phone'])) { ?>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <?php } ?>
        </section>

        <!-- Order Items -->
        <section class="order-items">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrice = 0;
                    foreach ($orderDetails as $item) {
                        $itemTotal = $item['quantity'] * $item['price'];
                        $totalPrice += $itemTotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($itemTotal, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="total">
                <h3>Total Price: $<?php echo number_format($totalPrice, 2); ?></h3>
            </div>
        </section>

        <div class="actions">
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
            <a href="cart.php" class="btn">Back to Cart</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
