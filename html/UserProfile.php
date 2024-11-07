<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

$query_user = "SELECT name, email, phone_number FROM users WHERE user_id = '$user_id'";
$result_user = mysqli_query($conn, $query_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $user = mysqli_fetch_assoc($result_user);
} else {
    echo "<script>alert('Error fetching user details.'); window.location.href='UserDashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/UserProfile.css">
</head>
<body>

    <!-- User Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>User Profile</h1>
        </div>
        <ul class="nav-links">
            <li><a href="UserDashboard.php">Dashboard</a></li>
            <li><a href="SearchPG.php">Search PG</a></li>
            <li><a href="MyBookings.php">My Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Profile Container -->
    <section class="profile-container">
        <div class="profile-card">
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <button class="edit-button" onclick="window.location.href='EditUserProfile.php'">Edit Profile</button>
        </div>
    </section>

</body>
</html>
