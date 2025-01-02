<?php
session_start();

// Include the database connection
include 'db_connect.php';

// Query to fetch all properties, their details, the owner, the student who booked them, and the booking status
$query = "
    SELECT 
        p.property_id, 
        p.name AS property_name, 
        p.description, 
        p.price_per_month, 
        p.available_rooms, 
        p.total_rooms, 
        p.image_url, 
        o.name AS owner_name, 
        s.name AS student_name,
        IF(b.status = 'Cancelled', 'Cancelled', IF(b.status IS NULL, 'Available', 'Booked')) AS booking_status
    FROM properties p
    LEFT JOIN users o ON p.owner_id = o.user_id  -- Get the owner info
    LEFT JOIN bookings b ON p.property_id = b.property_id  -- Get the booking info
    LEFT JOIN users s ON b.user_id = s.user_id  -- Get the student info
";

$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Handle the deletion of a property
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the property
    $delete_query = "DELETE FROM properties WHERE property_id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Property deleted successfully.'); window.location.href='AdminPGListings.php';</script>";
    } else {
        echo "<script>alert('Error deleting property.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin PG Listings</title>
    <link rel="stylesheet" href="../css/AdminPGListings.css">
</head>
<body>

    <!-- Admin Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Admin Dashboard</h1>
        </div>
        <ul class="nav-links">
            <li><a href="ManageUsers.php">Manage Users</a></li>
            <li><a href="ManageOwners.php">Manage Owners</a></li>
            <li><a href="AdminPGListings.php">Manage PG Listings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- PG Listings -->
    <section class="pg-listings-container">
        <h2>All PG Listings</h2>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Property Name</th>
                    <th>Description</th>
                    <th>Price per Month</th>
                    <th>Available Rooms</th>
                    <th>Owned By</th>
                    <th>Booked By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($property = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="Property Image"></td>
                            <td><?php echo htmlspecialchars($property['property_name']); ?></td>
                            <td><?php echo htmlspecialchars($property['description']); ?></td>
                            <td><?php echo htmlspecialchars($property['price_per_month']); ?></td>
                            <td><?php echo htmlspecialchars($property['available_rooms']); ?></td>
                            <td><?php echo htmlspecialchars($property['owner_name']); ?></td>
                            <td><?php echo htmlspecialchars($property['student_name']); ?></td>
                            <td class="status">
                                <?php if ($property['booking_status'] === 'Booked'): ?>
                                    Booked
                                    <br>
                                    <?php if ($property['booking_status'] === 'Cancelled'): ?>
                                        Cancelled
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($property['booking_status']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Delete button -->
                                <a href="AdminPGListings.php?delete_id=<?php echo $property['property_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this property?');">
                                    <button>Delete</button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No PG listings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</body>
</html>
