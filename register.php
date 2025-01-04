<?php
require_once 'db_connect.php'; // Include the database connection file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if the username is already taken
    $check_username_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_username_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username is already taken. Please choose a different username.";
    } else {
        // Check if the email is already registered
        $check_email_sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Email is already registered. Please use a different email.";
        } else {
            // Generate a user_id (you can use various methods for this)
            $user_id = md5(uniqid(rand(), true));

            // Hash the password for security (use password_hash in production)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Prepare and execute the SQL query to insert the user data into the database
            $insert_sql = "INSERT INTO users (user_id, username, password, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $user_id, $username, $hashed_password, $email);

            if ($stmt->execute()) {
                echo "Registration successful!";
                echo '<br><a href="login.html">Click here to login</a>';

            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}

mysqli_close($conn);
?>
