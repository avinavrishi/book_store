<?php
// Start session
session_start();

// Include database connection
require_once 'db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query the database to retrieve user details
    $sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $db_username, $hashed_password, $role);

    // Fetch the result and validate the password
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        // Close the SELECT statement
        $stmt->close();

        // Password is correct, log in the user
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $db_username;
        $_SESSION["role"] = $role;

        // Update the last_login column
        $updateLastLoginSql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateLastLoginSql);
        $updateStmt->bind_param("i", $user_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Redirect based on role
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else if ($role === 'customer') {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        echo "Incorrect username or password.";
    }

    // Close the SELECT statement if login fails
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
