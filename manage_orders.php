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

// Fetch all orders with their details
$ordersQuery = "
    SELECT o.order_id, o.user_id, o.total_price, o.order_date, o.status, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
";
$ordersResult = $conn->query($ordersQuery);

// Handle order status update
if (isset($_GET['update_status']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $new_status = $_GET['update_status'];
    
    // Update the order status
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_orders.php"); // Reload the page to reflect changes
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="static/css/manage_orders.css">
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
    <h1>Manage Orders</h1>
    <?php include('admin_navbar.php'); ?>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $ordersResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['username']; ?></td>
                <td><?php echo number_format($order['total_price'], 2); ?></td>
                <td><?php echo $order['order_date']; ?></td>
                <td><?php echo ucfirst($order['status']); ?></td>
                <td>
                    <a href="manage_order_details.php?order_id=<?php echo $order['order_id']; ?>">View Details</a>
                    <?php if ($order['status'] === 'Pending') { ?>
                        <a href="manage_orders.php?update_status=Processing&order_id=<?php echo $order['order_id']; ?>">Mark as Processing</a>
                    <?php } elseif ($order['status'] === 'Processing') { ?>
                        <a href="manage_orders.php?update_status=Completed&order_id=<?php echo $order['order_id']; ?>">Mark as Completed</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
