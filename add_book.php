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

// Handle book addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_book"])) {
    $title = $_POST["title"];
    $author_id = $_POST["author_id"];
    $category_id = $_POST["category_id"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];

    // Handle file upload
    if (isset($_FILES["cover_image"]) && $_FILES["cover_image"]["error"] == UPLOAD_ERR_OK) {
        $uploadDir = "static/images/";
        $fileName = basename($_FILES["cover_image"]["name"]);
        $uploadFilePath = $uploadDir . $fileName;

        // Move the uploaded file to the static/images directory
        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $uploadFilePath)) {
            $cover_image = $fileName;
        } else {
            $cover_image = null;
            echo "<p>File upload failed.</p>";
        }
    } else {
        $cover_image = null;
    }

    // Insert the new book into the database
    $sql = "INSERT INTO books (title, author_id, category_id, description, price, stock, cover_image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisdss", $title, $author_id, $category_id, $description, $price, $stock, $cover_image);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_books.php"); // Redirect back to the manage books page
}

// Fetch authors and categories for dropdowns
$authorsQuery = "SELECT author_id, name FROM authors";
$authorsResult = $conn->query($authorsQuery);

$categoriesQuery = "SELECT category_id, name FROM categories";
$categoriesResult = $conn->query($categoriesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="static/css/add_book.css">
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
    <h1>Add New Book</h1>

    <?php include('admin_navbar.php'); ?>

    <!-- Add New Book Form -->
    <form method="POST" action="add_book.php" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Book Title" required>
        
        <!-- Author Dropdown -->
        <label for="author_id">Author</label>
        <select name="author_id" required>
            <option value="" disabled selected>Select Author</option>
            <?php while ($author = $authorsResult->fetch_assoc()) { ?>
                <option value="<?php echo $author['author_id']; ?>"><?php echo $author['name']; ?></option>
            <?php } ?>
            <option value="manage_authors" class="manage-option">Manage Authors</option>
        </select>

        <!-- Category Dropdown -->
        <label for="category_id">Category</label>
        <select name="category_id" required>
            <option value="" disabled selected>Select Category</option>
            <?php while ($category = $categoriesResult->fetch_assoc()) { ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
            <?php } ?>
            <option value="manage_categories" class="manage-option">Manage Categories</option>
        </select>

        <textarea name="description" placeholder="Book Description" required></textarea>
        <input type="number" name="price" placeholder="Price" required step="0.01">
        <input type="number" name="stock" placeholder="Stock" required>
        
        <!-- File Input for Cover Image -->
        <label for="cover_image">Cover Image</label>
        <input type="file" name="cover_image" required>
        
        <button type="submit" name="add_book">Add Book</button>
    </form>

    <script>
        // Add event listeners to handle the redirects
        document.querySelector('select[name="author_id"]').addEventListener('change', function() {
            if (this.value === 'manage_authors') {
                window.location.href = 'manage_authors.php'; // Redirect to Manage Authors Page
            }
        });

        document.querySelector('select[name="category_id"]').addEventListener('change', function() {
            if (this.value === 'manage_categories') {
                window.location.href = 'manage_categories.php'; // Redirect to Manage Categories Page
            }
        });
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
