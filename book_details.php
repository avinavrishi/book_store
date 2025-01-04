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

// Get book details
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$bookId = intval($_GET['id']);
$bookQuery = "
    SELECT book_id, title, cover_image, description, price
    FROM books
    WHERE book_id = $bookId;
";
$book = $conn->query($bookQuery)->fetch_assoc();

if (!$book) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link rel="stylesheet" href="static/css/book_details.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <main>
        <section class="book-details">
            <img src="static/images/<?php echo $book['cover_image']; ?>" alt="Book Cover">
            <div class="book-info">
                <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                <p>Price: $<?php echo number_format($book['price'], 2); ?></p>
                <div class="actions">
                    <a href="add_to_cart.php?id=<?php echo $book['book_id']; ?>" class="btn btn-cart">Add to Cart</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
