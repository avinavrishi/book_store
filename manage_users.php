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

// Fetch all users from the database
$usersQuery = "SELECT user_id, username, email, role FROM users";
$usersResult = $conn->query($usersQuery);

// Handle role change (promotion/demotion)
if (isset($_GET['promote'])) {
    $user_id = $_GET['promote'];
    $new_role = 'admin'; // Promote to admin
    $sql = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php"); // Reload the page to reflect changes
}

// Handle role demotion (demote to customer)
if (isset($_GET['demote'])) {
    $user_id = $_GET['demote'];
    $new_role = 'customer'; // Demote to customer
    $sql = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php"); // Reload the page to reflect changes
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="static/css/manage_users.css">
    <link rel="stylesheet" href="static/css/admin_navbar.css">
</head>
<body>
    <h1>Manage Users</h1>

    <?php include('admin_navbar.php'); ?>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $usersResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo ucfirst($user['role']); ?></td>
                <td>
                    <?php if ($user['role'] === 'customer') { ?>
                        <a href="manage_users.php?promote=<?php echo $user['user_id']; ?>">Promote to Admin</a>
                    <?php } else { ?>
                        <a href="manage_users.php?demote=<?php echo $user['user_id']; ?>">Demote to Customer</a>
                    <?php } ?>
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
