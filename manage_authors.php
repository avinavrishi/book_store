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

// Handle Add Author
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_author"])) {
    $name = $_POST["name"];
    $biography = $_POST["biography"];
    $birth_date = $_POST["birth_date"];

    // Insert the new author into the database
    $sql = "INSERT INTO authors (name, biography, birth_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $biography, $birth_date);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_authors.php"); // Redirect back to the manage authors page
}

// Handle Update Author
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_author"])) {
    $author_id = $_POST["author_id"];
    $name = $_POST["name"];
    $biography = $_POST["biography"];
    $birth_date = $_POST["birth_date"];

    // Update the author in the database
    $sql = "UPDATE authors SET name = ?, biography = ?, birth_date = ? WHERE author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $biography, $birth_date, $author_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_authors.php"); // Redirect back to the manage authors page
}

// Handle Delete Author
if (isset($_GET["delete_id"])) {
    $author_id = $_GET["delete_id"];
    $sql = "DELETE FROM authors WHERE author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_authors.php"); // Redirect back to the manage authors page
}

// Fetch all authors
$sql = "SELECT * FROM authors";
$authors_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authors</title>
    <link rel="stylesheet" href="static/css/manage_authors.css"> <!-- Link to CSS -->
    <link rel="stylesheet" href="static/css/admin_navbar.css">
    <script>
        function editAuthor(id, name, biography, birth_date) {
            document.getElementById("author_id").value = id;
            document.getElementById("author_name").value = name;
            document.getElementById("author_biography").value = biography;
            document.getElementById("author_birth_date").value = birth_date;
            document.getElementById("form_title").innerText = "Edit Author";
            document.getElementById("add_author_button").style.display = "none";
            document.getElementById("update_author_button").style.display = "inline-block";
            document.getElementById("cancel_button").style.display = "inline-block";
        }

        function resetForm() {
            document.getElementById("author_form").reset();
            document.getElementById("form_title").innerText = "Add New Author";
            document.getElementById("add_author_button").style.display = "inline-block";
            document.getElementById("update_author_button").style.display = "none";
            document.getElementById("cancel_button").style.display = "none";
        }
    </script>
</head>
<body>
    <h1>Manage Authors</h1>

    <?php include('admin_navbar.php'); ?>

    <!-- Add or Edit Author Form -->
    <form method="POST" id="author_form" action="manage_authors.php">
        <h2 id="form_title">Add New Author</h2>
        <input type="hidden" id="author_id" name="author_id">
        <input type="text" id="author_name" name="name" placeholder="Author Name" required>
        <textarea id="author_biography" name="biography" placeholder="Author Biography" required></textarea>
        <input type="date" id="author_birth_date" name="birth_date" placeholder="Birth Date" required>
        <button type="submit" id="add_author_button" name="add_author">Add Author</button>
        <button type="submit" id="update_author_button" name="update_author" style="display:none;">Update Author</button>
        <button type="button" id="cancel_button" onclick="resetForm()" style="display:none;">Cancel</button>
    </form>

    <h2>Existing Authors</h2>
    <table>
        <thead>
            <tr>
                <th>Author Name</th>
                <th>Biography</th>
                <th>Birth Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($author = $authors_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($author["name"]); ?></td>
                <td><?php echo htmlspecialchars($author["biography"]); ?></td>
                <td><?php echo htmlspecialchars($author["birth_date"]); ?></td>
                <td>
                    <a href="javascript:void(0)" onclick="editAuthor(<?php echo $author['author_id']; ?>, '<?php echo addslashes($author['name']); ?>', '<?php echo addslashes($author['biography']); ?>', '<?php echo $author['birth_date']; ?>')">Edit</a> |
                    <a href="manage_authors.php?delete_id=<?php echo $author['author_id']; ?>" onclick="return confirm('Are you sure you want to delete this author?')">Delete</a>
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
