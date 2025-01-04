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

// Check if order_id is provided via GET
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch the order details based on the order_id
    $orderQuery = "
        SELECT o.order_id, o.user_id, o.total_price, o.order_date, o.status, u.username
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = ?
    ";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    $order = $orderResult->fetch_assoc();
    $stmt->close();

    // Fetch the order details (books in the order)
    $orderDetailsQuery = "
        SELECT od.order_detail_id, b.title, od.quantity, od.price, (od.quantity * od.price) AS total_price
        FROM order_details od
        JOIN books b ON od.book_id = b.book_id
        WHERE od.order_id = ?
    ";
    $stmt = $conn->prepare($orderDetailsQuery);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $orderDetailsResult = $stmt->get_result();
    $stmt->close();
} else {
    // Redirect if no order_id is provided
    header("Location: manage_orders.php");
    exit();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Order #<?php echo $order['order_id']; ?></title>
    <link rel="stylesheet" href="static/css/manage_order_details.css">
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
    <h1>Order Details - Order #<?php echo $order['order_id']; ?></h1>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <p><strong>Customer:</strong> <?php echo $order['username']; ?></p>
        <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
    </div>

    <h2>Books in This Order</h2>
    <table class="order-details-table">
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($detail = $orderDetailsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $detail['title']; ?></td>
                    <td><?php echo $detail['quantity']; ?></td>
                    <td>$<?php echo number_format($detail['price'], 2); ?></td>
                    <td>$<?php echo number_format($detail['total_price'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="manage_orders.php" class="back-button">Back to Orders</a>
</body>
</html>
