<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch the user's bookings along with the property image
$query_bookings = "
    SELECT b.*, p.name AS property_name, p.location, p.price_per_month, p.image_url
    FROM bookings b
    JOIN properties p ON b.property_id = p.property_id
    WHERE b.user_id = '$user_id'
";
$result_bookings = mysqli_query($conn, $query_bookings);

// Check for query errors
if (!$result_bookings) {
    die("Query failed: " . mysqli_error($conn)); // Debugging SQL query
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="../css/MyBookings.css">
</head>
<body>

<section class="bookings-container">
    <h1>My Bookings</h1>

    <?php if (mysqli_num_rows($result_bookings) > 0): ?>
        <?php while ($booking = mysqli_fetch_assoc($result_bookings)): ?>
            <div class="booking-card">
                <!-- Property Image on the Left -->
                <div class="property-image">
                    <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['property_name']); ?>" />
                </div>

                <!-- Property Details on the Right -->
                <div class="property-details">
                    <h2><?php echo htmlspecialchars($booking['property_name']); ?></h2>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                    <p><strong>Total Amount:</strong> ₹<?php echo htmlspecialchars($booking['price_per_month']); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($booking['payment_status']); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</section>

</body>
</html>
