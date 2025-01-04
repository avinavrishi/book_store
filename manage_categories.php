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

// Handle Add Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_category"])) {
    $name = $_POST["name"];
    $description = $_POST["description"];

    // Insert the new category into the database
    $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_categories.php"); // Redirect back to the manage categories page
}

// Handle Update Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_category"])) {
    $category_id = $_POST["category_id"];
    $name = $_POST["name"];
    $description = $_POST["description"];

    // Update the category in the database
    $sql = "UPDATE categories SET name = ?, description = ? WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $description, $category_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_categories.php"); // Redirect back to the manage categories page
}

// Handle Delete Category
if (isset($_GET["delete_id"])) {
    $category_id = $_GET["delete_id"];
    $sql = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_categories.php"); // Redirect back to the manage categories page
}

// Fetch all categories
$sql = "SELECT * FROM categories";
$categories_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="static/css/manage_categories.css"> <!-- Add your CSS file -->
    <link rel="stylesheet" href="static/css/admin_navbar.css">
    <script>
        function editCategory(id, name, description) {
            // Set the form to update category and populate the fields
            document.getElementById("category_id").value = id;
            document.getElementById("category_name").value = name;
            document.getElementById("category_description").value = description;
            document.getElementById("form_title").innerText = "Edit Category";
            document.getElementById("add_category_button").style.display = "none";
            document.getElementById("update_category_button").style.display = "inline-block";
            document.getElementById("cancel_button").style.display = "inline-block";  // Show the Cancel button
        }

        function resetForm() {
            document.getElementById("category_form").reset();
            document.getElementById("form_title").innerText = "Add New Category";
            document.getElementById("add_category_button").style.display = "inline-block";
            document.getElementById("update_category_button").style.display = "none";
            document.getElementById("cancel_button").style.display = "none";  // Hide the Cancel button
        }
    </script>
</head>
<body>
    <h1>Manage Categories</h1>
    <?php include('admin_navbar.php'); ?>

    <!-- Add or Edit Category Form -->
    <form method="POST" id="category_form" action="manage_categories.php">
        <h2 id="form_title">Add New Category</h2>
        <input type="hidden" id="category_id" name="category_id">
        <input type="text" id="category_name" name="name" placeholder="Category Name" required>
        <textarea id="category_description" name="description" placeholder="Category Description" required></textarea>
        <button type="submit" id="add_category_button" name="add_category">Add Category</button>
        <button type="submit" id="update_category_button" name="update_category" style="display:none;">Update Category</button>
        <button type="button" id="cancel_button" onclick="resetForm()" style="display:none;">Cancel</button> <!-- Cancel button initially hidden -->
    </form>

    <h2>Existing Categories</h2>
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($category = $categories_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($category["name"]); ?></td>
                <td><?php echo htmlspecialchars($category["description"]); ?></td>
                <td>
                    <a href="javascript:void(0)" onclick="editCategory(<?php echo $category['category_id']; ?>, '<?php echo addslashes($category['name']); ?>', '<?php echo addslashes($category['description']); ?>')">Edit</a> |
                    <a href="manage_categories.php?delete_id=<?php echo $category['category_id']; ?>" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
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
