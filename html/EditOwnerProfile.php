<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Unauthorized access! Please login as an owner.'); window.location.href='login.php';</script>";
    exit();
}

// Database connection
include 'db_connect.php';

// Fetch the logged-in owner's details
$email = $_SESSION['user'];
$query = "SELECT user_type, user_id, name, email, phone_number FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if the logged-in user is an owner
if ($user['user_type'] !== 'owner') {
    echo "<script>alert('Unauthorized access! Only owners can access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Update profile on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    // Check if the current password is correct if a new password is provided
    if (!empty($new_password)) {
        $query_password = "SELECT password FROM users WHERE user_id = '{$user['user_id']}'";
        $result_password = mysqli_query($conn, $query_password);
        $row_password = mysqli_fetch_assoc($result_password);

        if (password_verify($current_password, $row_password['password'])) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $query_update = "UPDATE users SET name='$name', email='$email', phone_number='$phone_number', password='$new_password_hashed' WHERE user_id = '{$user['user_id']}'";
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
        }
    } else {
        $query_update = "UPDATE users SET name='$name', email='$email', phone_number='$phone_number' WHERE user_id = '{$user['user_id']}'";
    }

    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Profile updated successfully.'); window.location.href='OwnerProfile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Owner Profile</title>
    <link rel="stylesheet" href="../css/EditOwnerProfile.css">
</head>
<body>

    <!-- Edit Profile Form -->
    <section class="edit-profile-container">
        <h1>Edit Profile</h1>
        <form action="EditOwnerProfile.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" placeholder="Enter current password to change it">

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Leave blank if no password change">

            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </section>

</body>
</html>
