<?php
// Start the session
session_start();

// Include database connection
require_once('db_connect.php');

// Check if the user is logged in and is an admin (or customer if needed)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'customer')) {
    header("Location: login.php");
    exit();
}

// Fetch all orders for the user (if admin, show all orders)
$userId = $_SESSION['user_id'];
if ($_SESSION['role'] === 'admin') {
    $query = "SELECT * FROM orders ORDER BY order_date DESC";
} else {
    $query = "SELECT * FROM orders WHERE user_id = '$userId' ORDER BY order_date DESC";
}
$result = mysqli_query($conn, $query);

// Check if there are any orders
if (mysqli_num_rows($result) == 0) {
    $noOrders = true; // Flag to check if no orders are found
} else {
    $noOrders = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="static/css/orders.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1>Orders</h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <main>
        <?php if ($noOrders) { ?>
            <p>No orders found. <a href="category.php">Continue Shopping</a></p>
        <?php } else { ?>
            <?php while ($order = mysqli_fetch_assoc($result)) { 
                // Get order details
                $orderId = $order['order_id'];
                $orderDetailsQuery = "SELECT od.*, b.title, b.price FROM order_details od JOIN books b ON od.book_id = b.book_id WHERE od.order_id = '$orderId'";
                $orderDetailsResult = mysqli_query($conn, $orderDetailsQuery);
            ?>

            <section class="order">
                <h3>Order #<?php echo $order['order_id']; ?></h3>
                <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                <p>Order Date: <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                <p><strong>Total Price: $<?php echo number_format($order['total_price'], 2); ?></strong></p>

                <h4>Order Items</h4>
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
                        $orderTotal = 0;
                        while ($item = mysqli_fetch_assoc($orderDetailsResult)) {
                            $itemTotal = $item['quantity'] * $item['price'];
                            $orderTotal += $itemTotal;
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

                <div class="order-total">
                    <h4>Order Total: $<?php echo number_format($orderTotal, 2); ?></h4>
                </div>
            </section>

            <?php } ?>
        <?php } ?>
    </main>

    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
