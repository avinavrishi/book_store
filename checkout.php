<?php
// Start the session
session_start();

// Include database connection (using mysqli)
require_once('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Check if the cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $book) {
    $totalPrice += $book['price'] * $book['quantity'];
}

// Handle form submission for checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get shipping details from the form
    $shippingName = mysqli_real_escape_string($conn, $_POST['shipping_name']);
    $shippingAddress = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $shippingCity = mysqli_real_escape_string($conn, $_POST['shipping_city']);
    $shippingZip = mysqli_real_escape_string($conn, $_POST['shipping_zip']);
    $shippingCountry = mysqli_real_escape_string($conn, $_POST['shipping_country']);

    // Insert the order into the orders table
    $userId = $_SESSION['user_id'];
    $status = 'Pending'; // Default order status

    // Insert the order into the database
    $query = "INSERT INTO orders (user_id, total_price, status) VALUES ('$userId', '$totalPrice', '$status')";
    if (mysqli_query($conn, $query)) {
        // Get the last inserted order ID
        $orderId = mysqli_insert_id($conn);

        // Insert order details into the order_details table
        foreach ($_SESSION['cart'] as $bookId => $book) {
            $quantity = $book['quantity'];
            $price = $book['price'];

            // Insert each book in the cart into the order_details table
            $query = "INSERT INTO order_details (order_id, book_id, quantity, price) 
                      VALUES ('$orderId', '$bookId', '$quantity', '$price')";
            mysqli_query($conn, $query);
        }

        // Insert the shipping address into the shipping_addresses table
        $query = "INSERT INTO shipping_addresses (order_id, recipient_name, address_line1, address_line2, city, state, postal_code, country, phone) 
                  VALUES ('$orderId', '$shippingName', '$shippingAddress', NULL, '$shippingCity', '', '$shippingZip', '$shippingCountry', '')";
        mysqli_query($conn, $query);

        // After inserting the order and details, clear the cart
        unset($_SESSION['cart']);

        // Optionally, you can store the shipping information in a separate table (not done here for simplicity)

        // Redirect to the order confirmation page
        $_SESSION['order_success'] = true;
        header("Location: order_confirmation.php");
        exit();
    } else {
        // Handle error in order insertion
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="static/css/checkout.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1>Checkout</h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <main>
        <h2>Review Your Order</h2>

        <!-- Cart Review -->
        <section class="cart-review">
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
                    <?php foreach ($_SESSION['cart'] as $bookId => $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td>$<?php echo number_format($book['price'], 2); ?></td>
                            <td><?php echo $book['quantity']; ?></td>
                            <td>$<?php echo number_format($book['price'] * $book['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total">
                <h3>Total Price: $<?php echo number_format($totalPrice, 2); ?></h3>
            </div>
        </section>

        <!-- Shipping Information Form -->
        <h2>Shipping Information</h2>
        <form method="POST" action="checkout.php">
            <div class="form-group">
                <label for="shipping_name">Full Name</label>
                <input type="text" id="shipping_name" name="shipping_name" required>
            </div>
            <div class="form-group">
                <label for="shipping_address">Address</label>
                <input type="text" id="shipping_address" name="shipping_address" required>
            </div>
            <div class="form-group">
                <label for="shipping_city">City</label>
                <input type="text" id="shipping_city" name="shipping_city" required>
            </div>
            <div class="form-group">
                <label for="shipping_zip">Postal Code</label>
                <input type="text" id="shipping_zip" name="shipping_zip" required>
            </div>
            <div class="form-group">
                <label for="shipping_country">Country</label>
                <input type="text" id="shipping_country" name="shipping_country" required>
            </div>

            <div class="checkout-actions">
                <button type="submit" class="btn btn-checkout">Place Order</button>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
