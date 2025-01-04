<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'db_connect.php';

// Check if book ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$bookId = intval($_GET['id']);

// Fetch book details
$bookQuery = "SELECT book_id, title, price FROM books WHERE book_id = $bookId";
$bookResult = $conn->query($bookQuery);
$book = $bookResult->fetch_assoc();

if (!$book) {
    header("Location: dashboard.php");
    exit();
}

// Add book to the cart (session-based cart)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the book is already in the cart
if (isset($_SESSION['cart'][$bookId])) {
    // If already in cart, increment quantity
    $_SESSION['cart'][$bookId]['quantity']++;
} else {
    // If not in cart, add the book with quantity 1
    $_SESSION['cart'][$bookId] = [
        'title' => $book['title'],
        'price' => $book['price'],
        'quantity' => 1
    ];
}

// Redirect to cart page
header("Location: cart.php");
exit();
?>
