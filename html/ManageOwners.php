<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Unauthorized access! Please login as an admin.'); window.location.href='login.php';</script>";
    exit();
}

// Database connection
include 'db_connect.php';

// Fetch all owners (assuming owners are identified by 'user_type' = 'owner')
$query = "SELECT user_id, name, email, phone_number FROM users WHERE user_type = 'owner'";
$result = mysqli_query($conn, $query);

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM users WHERE user_id = '$delete_id' AND user_type = 'owner'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Owner deleted successfully.'); window.location.href='ManageOwners.php';</script>";
    } else {
        echo "<script>alert('Error deleting owner.');</script>";
    }
}

// Handle edit action - display a simple form for editing
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $update_query = "UPDATE users SET name='$name', email='$email', phone_number='$phone_number' WHERE user_id='$user_id' AND user_type = 'owner'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Owner information updated successfully.'); window.location.href='ManageOwners.php';</script>";
    } else {
        echo "<script>alert('Error updating owner information.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Owners</title>
    <link rel="stylesheet" href="../css/ManageOwners.css"> <!-- Use the same CSS file or create a new one for owners -->
</head>
<body>

    <!-- Admin Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Admin Dashboard</h1>
        </div>
        <ul class="nav-links">
            <li><a href="AdminDashboard.php">Dashboard</a></li>
            <li><a href="ManageUsers.php">Manage Students</a></li>
            <li><a href="ManageOwners.php">Manage Owners</a></li>
            <li><a href="managePGs.php">Manage PG Listings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Owners List -->
    <section class="users-container">
        <h2>Manage Owners</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($owner = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($owner['name']); ?></td>
                            <td><?php echo htmlspecialchars($owner['email']); ?></td>
                            <td><?php echo htmlspecialchars($owner['phone_number']); ?></td>
                            <td>
                                <a href="ManageOwners.php?edit_id=<?php echo $owner['user_id']; ?>">Edit</a> |
                                <a href="ManageOwners.php?delete_id=<?php echo $owner['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this owner?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No owners found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Edit Form (if edit link is clicked) -->
        <?php if (isset($_GET['edit_id'])):
            $edit_id = $_GET['edit_id'];
            $edit_query = "SELECT * FROM users WHERE user_id = '$edit_id' AND user_type = 'owner'";
            $edit_result = mysqli_query($conn, $edit_query);
            $edit_owner = mysqli_fetch_assoc($edit_result);
        ?>
            <div class="edit-form">
                <h3>Edit Owner</h3>
                <form method="post" action="ManageOwners.php">
                    <input type="hidden" name="user_id" value="<?php echo $edit_owner['user_id']; ?>">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($edit_owner['name']); ?>" required>
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($edit_owner['email']); ?>" required>
                    <label>Phone Number:</label>
                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($edit_owner['phone_number']); ?>" required>
                    <button type="submit" name="update">Update</button>
                </form>
            </div>
        <?php endif; ?>
    </section>

</body>
</html>
