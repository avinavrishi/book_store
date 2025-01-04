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

// Fetch the category ID from the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if no category is specified
    exit();
}
$categoryId = intval($_GET['id']);

// Fetch category details
$categoryQuery = "SELECT name FROM categories WHERE category_id = ?";
$stmt = $conn->prepare($categoryQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$categoryResult = $stmt->get_result();
if ($categoryResult->num_rows === 0) {
    echo "Category not found.";
    exit();
}
$category = $categoryResult->fetch_assoc();

// Fetch books by category
$booksQuery = "
    SELECT books.book_id, books.title, books.description, books.price, books.cover_image,
           authors.name AS author_name
    FROM books
    JOIN authors ON books.author_id = authors.author_id
    WHERE books.category_id = ?
    ORDER BY books.title ASC;
";
$stmt = $conn->prepare($booksQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$booksResult = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books in <?php echo htmlspecialchars($category['name']); ?></title>
    <link rel="stylesheet" href="static/css/category.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1>Books in <?php echo htmlspecialchars($category['name']); ?></h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <!-- Books List -->
    <section class="books-section">
        <h2><?php echo htmlspecialchars($category['name']); ?></h2>
        <?php if ($booksResult->num_rows > 0) { ?>
            <div class="books-container">
                <?php while ($book = $booksResult->fetch_assoc()) { ?>
                    <!-- Wrap the book card in an anchor tag to make it clickable -->
                    <a href="book_details.php?id=<?php echo $book['book_id']; ?>" class="book-card">
                        <img src="static/images/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Book Cover">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p>By: <?php echo htmlspecialchars($book['author_name']); ?></p>
                        <p>Price: $<?php echo htmlspecialchars(number_format($book['price'], 2)); ?></p>
                        <p><?php echo htmlspecialchars($book['description']); ?></p>
                    </a>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>No books found in this category.</p>
        <?php } ?>
    </section>

    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
