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

// Fetch properties for the logged-in owner
$owner_id = $user['user_id'];
$query_properties = "SELECT * FROM properties WHERE owner_id = '$owner_id'";
$result_properties = mysqli_query($conn, $query_properties);
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
            <li><a href="ManageProperties.php">Manage Properties</a></li>
            <li><a href="viewBookings.php">View Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Dashboard Content -->
    <section class="dashboard-container">
        <h2>Welcome, Owner!</h2>
        <p>Manage your properties and view bookings here.</p>
        
        <!-- Display Properties -->
        <h2>Your Properties</h2>
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Price per Month</th>
                    <th>Total Rooms</th>
                    <th>Available Rooms</th>
                    <th>Amenities</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result_properties) > 0): ?>
                    <?php while ($property = mysqli_fetch_assoc($result_properties)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($property['name']); ?></td>
                            <td><?php echo htmlspecialchars($property['description']); ?></td>
                            <td><?php echo htmlspecialchars($property['location']); ?></td>
                            <td><?php echo htmlspecialchars($property['price_per_month']); ?></td>
                            <td><?php echo htmlspecialchars($property['total_rooms']); ?></td>
                            <td><?php echo htmlspecialchars($property['available_rooms']); ?></td>
                            <td><?php echo htmlspecialchars($property['amenities']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="Property Image" style="width: 100px;"></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No properties found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</body>
</html>
