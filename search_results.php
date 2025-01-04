<?php
// Start session
session_start();

// Include database connection
require_once 'db_connect.php';

// Initialize variables
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($searchQuery !== '') {
    // Prepare and execute the search query
    $sql = "SELECT books.book_id, books.title, books.description, books.price, books.cover_image, 
               authors.name AS author_name, categories.name AS category_name
        FROM books
        JOIN authors ON books.author_id = authors.author_id
        JOIN categories ON books.category_id = categories.category_id
        WHERE books.title LIKE ? OR authors.name LIKE ? OR categories.name LIKE ?";

    
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $searchQuery . "%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null; // No search query provided
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="static/css/search_results.css">
    <link rel="stylesheet" href="static/css/customer_navbar.css">
</head>
<body>
    <header>
    <?php include('customer_navbar.php'); ?>
    </header>

    <h1>Search Results</h1>

    <!-- Search Form -->
    <form method="GET" action="search_results.php">
        <input type="text" name="query" placeholder="Search for books, authors, or categories" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($searchQuery === ''): ?>
        <p>Please enter a search query.</p>
    <?php elseif ($result->num_rows > 0): ?>
        <ul class="search-results">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="search-item">
                    <img src="static/images/<?php echo htmlspecialchars($row['cover_image']); ?>" alt="Cover Image" class="cover-image">
                    <div class="book-info">
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($row['author_name']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category_name']); ?></p>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($row['price']); ?></p>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No results found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
    <?php endif; ?>

    <!-- Back to Home Link -->
    <p><a href="dashboard.php">Back to Home</a></p>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
