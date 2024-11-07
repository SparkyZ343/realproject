<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch current user details
$query_user = "SELECT name, email, phone_number FROM users WHERE user_id = '$user_id'";
$result_user = mysqli_query($conn, $query_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $user = mysqli_fetch_assoc($result_user);
} else {
    echo "<script>alert('Error fetching user details.'); window.location.href='UserDashboard.php';</script>";
    exit();
}

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_password = $_POST['password'];
    $current_password = $_POST['current_password'];

    // Check if password is being updated
    if (!empty($new_password)) {
        // Verify the current password
        $query_check_password = "SELECT password FROM users WHERE user_id = '$user_id'";
        $result_check = mysqli_query($conn, $query_check_password);
        $current_user = mysqli_fetch_assoc($result_check);

        if (!password_verify($current_password, $current_user['password'])) {
            echo "<script>alert('Current password is incorrect.');</script>";
        } else {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = '$user_id'";
            mysqli_query($conn, $update_password_query);
        }
    }

    // Update other details (name, email, phone)
    $update_user_query = "UPDATE users SET name = '$new_name', email = '$new_email', phone_number = '$new_phone' WHERE user_id = '$user_id'";
    mysqli_query($conn, $update_user_query);

    echo "<script>alert('Profile updated successfully.'); window.location.href='UserProfile.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../css/EditUserProfile.css">
</head>
<body>

    <!-- User Navigation Bar -->
    <!-- <nav>
        <div class="logo">
            <h1>Edit Profile</h1>
        </div>
        <ul class="nav-links">
            <li><a href="UserDashboard.php">Dashboard</a></li>
            <li><a href="SearchPG.php">Search PG</a></li>
            <li><a href="MyBookings.php">My Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav> -->

    <!-- Edit Profile Container -->
    <section class="edit-profile-container">
        <div class="edit-profile-card">
            <h2>Edit Your Details</h2>
            <form method="POST" action="EditUserProfile.php">
                <!-- Name Field -->
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

                <!-- Email Field -->
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

                <!-- Phone Field -->
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required><br><br>

                <!-- Current Password Field -->
                <label for="current_password">Current Password (leave blank if not changing):</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter your current password"><br><br>

                <!-- New Password Field -->
                <label for="password">New Password (leave blank if not changing):</label>
                <input type="password" id="password" name="password" placeholder=""><br><br>

                <!-- Submit Button -->
                <button type="submit" class="submit-button">Update Profile</button>
            </form>
        </div>
    </section>

</body>
</html>
