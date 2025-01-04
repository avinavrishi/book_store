<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Check if the cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cartEmpty = true;
} else {
    $cartEmpty = false;
}

// Handle quantity update or delete item
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $bookId => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$bookId]);
        } else {
            $_SESSION['cart'][$bookId]['quantity'] = $quantity;
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle delete individual item
if (isset($_GET['delete'])) {
    $bookId = intval($_GET['delete']);
    if (isset($_SESSION['cart'][$bookId])) {
        unset($_SESSION['cart'][$bookId]);
    }
    header("Location: cart.php");
    exit();
}

// Calculate total price
$totalPrice = 0;
if (!$cartEmpty) {
    foreach ($_SESSION['cart'] as $book) {
        $totalPrice += $book['price'] * $book['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="static/css/cart.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1>Your Cart</h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <main>
        <?php if ($cartEmpty): ?>
            <p>Your cart is empty. <a href="dashboard.php">Continue Shopping</a></p>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <table>
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $bookId => $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td>$<?php echo number_format($book['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $bookId; ?>]" value="<?php echo $book['quantity']; ?>" min="1">
                                </td>
                                <td>$<?php echo number_format($book['price'] * $book['quantity'], 2); ?></td>
                                <td>
                                    <a href="cart.php?delete=<?php echo $bookId; ?>" class="btn btn-delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-actions">
                    <button type="submit" name="update_cart">Update Cart</button>
                    <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
                </div>
            </form>
            <div class="total">
                <h3>Total Price: $<?php echo number_format($totalPrice, 2); ?></h3>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
