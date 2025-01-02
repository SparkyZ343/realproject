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

// Cancel a booking if requested
if (isset($_POST['cancel_booking_id'])) {
    $cancel_booking_id = $_POST['cancel_booking_id'];
    $update_status_query = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$cancel_booking_id' AND status = 'Confirmed'";
    mysqli_query($conn, $update_status_query);
}

// Fetch bookings for the logged-in owner's properties
$owner_id = $user['user_id'];
$query_bookings = "
    SELECT b.booking_id, b.start_date, b.end_date, b.status, 
           p.property_id, p.name AS property_name, p.location, p.price_per_month, 
           u.name AS user_name, u.email, u.phone_number
    FROM bookings b
    JOIN properties p ON b.property_id = p.property_id
    JOIN users u ON b.user_id = u.user_id
    WHERE p.owner_id = '$owner_id'
    ORDER BY b.start_date DESC
";
$result_bookings = mysqli_query($conn, $query_bookings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link rel="stylesheet" href="../css/OwnerViewBookings.css">
</head>
<body>

    <!-- Owner Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>View Bookings</h1>
        </div>
        <ul class="nav-links">
            <li><a href="OwnerDashboard.php">Dashboard</a></li>
            <li><a href="ManageProperties.php">Manage Properties</a></li>
            <li><a href="OwnerProfile.php">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Bookings Content -->
    <section class="bookings-container">
        <h2>Bookings for Your Properties</h2>

        <?php if (mysqli_num_rows($result_bookings) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>Location</th>
                        <th>Price per Month</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Booked By</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                        <th>Reviews</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($result_bookings)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['property_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['location']); ?></td>
                            <td><?php echo htmlspecialchars($booking['price_per_month']); ?></td>
                            <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['end_date'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                            <td><?php echo htmlspecialchars($booking['phone_number']); ?></td>
                            <td>
                                <?php if ($booking['status'] === 'Confirmed'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="cancel_booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" class="cancel-button">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="ViewReviews.php?property_id=<?php echo $booking['property_id']; ?>">
                                    <button class="reviews-button">See Reviews</button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found for your properties.</p>
        <?php endif; ?>
    </section>

</body>
</html>
