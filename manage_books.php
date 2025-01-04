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

// Fetch all books with author name and category name
$booksQuery = "
    SELECT b.book_id, b.title, a.name AS author_name, c.name AS category_name, b.price, b.stock
    FROM books b
    JOIN authors a ON b.author_id = a.author_id
    JOIN categories c ON b.category_id = c.category_id
";
$booksResult = $conn->query($booksQuery);

// Handle book deletion
if (isset($_GET["delete"])) {
    $book_id = $_GET["delete"];
    $sql = "DELETE FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_books.php"); // Reload the page to reflect changes
}

// Handle bulk upload
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["bulk_upload"])) {
//     if ($_FILES["file"]["name"]) {
//         $filename = $_FILES["file"]["tmp_name"];
//         $file = fopen($filename, "r");

//         while (($column = fgetcsv($file)) !== false) {
//             $title = $column[0];
//             $author_id = $column[1];
//             $category_id = $column[2];
//             $description = $column[3];
//             $price = $column[4];
//             $stock = $column[5];
//             $cover_image = $column[6];

//             $sql = "INSERT INTO books (title, author_id, category_id, description, price, stock, cover_image)
//                     VALUES (?, ?, ?, ?, ?, ?, ?)";
//             $stmt = $conn->prepare($sql);
//             $stmt->bind_param("siisdss", $title, $author_id, $category_id, $description, $price, $stock, $cover_image);
//             $stmt->execute();
//         }

//         fclose($file);
//         header("Location: manage_books.php"); // Reload the page to reflect changes
//     }
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="static/css/manage_books.css">
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
    <h1>Manage Books</h1>

    <?php include('admin_navbar.php'); ?>

    <!-- Button to Add New Book -->
    <h2><a href="add_book.php">Add New Book</a></h2>

    <!-- Bulk Upload Form 
    <h2>Bulk Upload Books (CSV)</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="bulk_upload">Upload CSV</button>
    </form>
    -->

    <!-- Book List -->
    <h2>Books List</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($book = $booksResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $book['title']; ?></td>
                <td><?php echo $book['author_name']; ?></td>
                <td><?php echo $book['category_name']; ?></td>
                <td>$<?php echo $book['price']; ?></td>
                <td><?php echo $book['stock']; ?></td>
                <td>
                    <a href="edit_book.php?edit=<?php echo $book['book_id']; ?>">Edit</a> |
                    <a href="manage_books.php?delete=<?php echo $book['book_id']; ?>" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
