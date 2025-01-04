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

// Fetch popular books based on reviews
$popularBooksQuery = "
    SELECT b.book_id, b.title, b.cover_image, AVG(r.rating) AS avg_rating
    FROM books b
    LEFT JOIN reviews r ON b.book_id = r.book_id
    GROUP BY b.book_id
    ORDER BY avg_rating DESC
    LIMIT 5;
";
$popularBooks = $conn->query($popularBooksQuery);

// Fetch new arrivals
$newArrivalsQuery = "
    SELECT book_id, title, cover_image, created_at
    FROM books
    ORDER BY created_at DESC
    LIMIT 5;
";
$newArrivals = $conn->query($newArrivalsQuery);

// Fetch categories
$categoriesQuery = "
    SELECT category_id, name
    FROM categories
    ORDER BY name ASC;
";
$categories = $conn->query($categoriesQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="static/css/dashboard.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
        <h1>Welcome to Bookstore Dashboard!</h1>
        <?php include('customer_navbar.php'); ?>
    </header>

    <!-- Search Bar -->
    <section class="search-section">
        <form method="GET" action="search_results.php">
            <input type="text" name="query" placeholder="Search books..." required>
            <button type="submit">Search</button>
        </form>
    </section>

    <!-- Categories -->
    <section class="categories-section">
        <h2>Categories</h2>
        <ul class="categories-list">
            <?php while ($category = $categories->fetch_assoc()) { ?>
                <li><a href="category.php?id=<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></a></li>
            <?php } ?>
        </ul>
    </section>

    <!-- Popular Books -->
<section class="section">
    <h2>Popular Books</h2>
    <div class="books-container">
        <?php while ($book = $popularBooks->fetch_assoc()) { ?>
            <a href="book_details.php?id=<?php echo $book['book_id']; ?>" class="book-card-link">
                <div class="book-card">
                    <img src="static/images/<?php echo $book['cover_image']; ?>" alt="Book Cover">
                    <p><?php echo $book['title']; ?></p>
                    <p>Rating: <?php echo number_format($book['avg_rating'], 1); ?></p>
                </div>
            </a>
        <?php } ?>
    </div>
</section>

<!-- New Arrivals -->
<section class="section">
    <h2>New Arrivals</h2>
    <div class="books-container">
        <?php while ($book = $newArrivals->fetch_assoc()) { ?>
            <a href="book_details.php?id=<?php echo $book['book_id']; ?>" class="book-card-link">
                <div class="book-card">
                    <img src="static/images/<?php echo $book['cover_image']; ?>" alt="Book Cover">
                    <p><?php echo $book['title']; ?></p>
                </div>
            </a>
        <?php } ?>
    </div>
</section>


    <footer>
        <p>&copy; 2024 Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
