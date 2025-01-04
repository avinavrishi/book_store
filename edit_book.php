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

// Fetch the book ID from the URL
if (isset($_GET['edit'])) {
    $book_id = $_GET['edit'];

    // Fetch the current book details from the database
    $sql = "SELECT * FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book_result = $stmt->get_result();
    $book = $book_result->fetch_assoc();
    $stmt->close();

    // Check if the book exists
    if (!$book) {
        header("Location: manage_books.php");
        exit();
    }
}

// Handle book update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_book"])) {
    $title = $_POST["title"];
    $author_id = $_POST["author_id"];
    $category_id = $_POST["category_id"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $cover_image = $_POST["cover_image"];

    // Update the book in the database
    $sql = "UPDATE books SET title = ?, author_id = ?, category_id = ?, description = ?, price = ?, stock = ?, cover_image = ? WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisdssi", $title, $author_id, $category_id, $description, $price, $stock, $cover_image, $book_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_books.php"); // Redirect back to the manage books page
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="static/css/edit_book.css">
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
    <h1>Edit Book</h1>
    <?php include('admin_navbar.php'); ?>

    <!-- Edit Book Form -->
    <form method="POST" action="edit_book.php?edit=<?php echo $book_id; ?>">
        <input type="text" name="title" placeholder="Book Title" value="<?php echo $book['title']; ?>" required>
        <input type="number" name="author_id" placeholder="Author ID" value="<?php echo $book['author_id']; ?>" required>
        <input type="number" name="category_id" placeholder="Category ID" value="<?php echo $book['category_id']; ?>" required>
        <textarea name="description" placeholder="Book Description" required><?php echo $book['description']; ?></textarea>
        <input type="number" name="price" placeholder="Price" value="<?php echo $book['price']; ?>" required step="0.01">
        <input type="number" name="stock" placeholder="Stock" value="<?php echo $book['stock']; ?>" required>
        <input type="text" name="cover_image" placeholder="Cover Image URL" value="<?php echo $book['cover_image']; ?>" required>
        <button type="submit" name="edit_book">Update Book</button>
    </form>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
