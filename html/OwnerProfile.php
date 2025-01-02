<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Unauthorized access! Please login as an owner.'); window.location.href='login.php';</script>";
    exit();
}

// Database connection
include 'db_connect.php';

// Fetch the logged-in user's details
$email = $_SESSION['user'];
$query = "SELECT user_type, user_id, name, email, phone_number FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if the logged-in user is an owner
if ($user['user_type'] !== 'owner') {
    echo "<script>alert('Unauthorized access! Only owners can access this page.'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Profile</title>
    <link rel="stylesheet" href="../css/OwnerProfile.css">
</head>
<body>

    <!-- Owner Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Owner Profile</h1>
        </div>
        <ul class="nav-links">
            <li><a href="OwnerDashboard.php">Dashboard</a></li>
            <li><a href="ManageProperties.php">Manage Properties</a></li>
            <li><a href="viewBookings.php">View Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Profile Container -->
    <section class="profile-container">
        <div class="profile-card">
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <button class="edit-button" onclick="window.location.href='EditOwnerProfile.php'">Edit Profile</button>
        </div>
    </section>

</body>
</html>
