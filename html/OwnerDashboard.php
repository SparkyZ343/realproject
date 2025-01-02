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
$query = "SELECT user_type, user_id FROM users WHERE email = '$email'";
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
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="../css/OwnerDashboard.css">
</head>
<body>

    <!-- Owner Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Owner Dashboard</h1>
        </div>
        <ul class="nav-links">
            <li><a href="OwnerProfile.php">My Profile</a></li>
            <li><a href="manageProperties.php">Add Properties</a></li> <!-- Link to Manage Properties page -->
            <li><a href="owner_view_properties.php">Manage Properties</a></li>
            <li><a href="OwnerViewBookings.php">View Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Dashboard Content -->
    <section class="dashboard-container">
        <h2>Welcome, Owner!</h2>
        <p>Manage your properties and view bookings here.</p>
    </section>

</body>
</html>
