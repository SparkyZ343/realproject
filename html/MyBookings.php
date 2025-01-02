<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Handle cancellation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Fetch the booking details
    $query_booking = "SELECT * FROM bookings WHERE booking_id = $booking_id AND user_id = $user_id";
    $result_booking = mysqli_query($conn, $query_booking);

    if (mysqli_num_rows($result_booking) > 0) {
        // Cancel the booking
        $query_cancel = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = $booking_id";
        if (mysqli_query($conn, $query_cancel)) {
            echo "<script>alert('Booking cancelled successfully.'); window.location.href='MyBookings.php';</script>";
        } else {
            echo "<script>alert('Failed to cancel booking.'); window.location.href='MyBookings.php';</script>";
        }
    } else {
        echo "<script>alert('Booking not found.'); window.location.href='MyBookings.php';</script>";
    }
    exit();
}

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
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="../css/MyBookings.css">
    <script>
        // Open Confirmation Popup
        function confirmCancel(bookingId) {
            const popup = document.getElementById("cancelPopup");
            const confirmButton = document.getElementById("confirmCancelButton");
            popup.style.display = "block";
            confirmButton.setAttribute("data-booking-id", bookingId);
        }

        // Close Confirmation Popup
        function closePopup() {
            const popup = document.getElementById("cancelPopup");
            popup.style.display = "none";
        }

        // Handle Cancellation
        function cancelBooking() {
            const confirmButton = document.getElementById("confirmCancelButton");
            const bookingId = confirmButton.getAttribute("data-booking-id");
            document.getElementById("cancelForm").booking_id.value = bookingId;
            document.getElementById("cancelForm").submit();
        }
    </script>
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

                    <!-- Provide Review Button -->
                    <form action="Review.php" method="post" style="display: inline;">
                        <input type="hidden" name="property_id" value="<?php echo $booking['property_id']; ?>">
                        <button type="submit" class="review-button">Provide Review</button>
                    </form>

                    <!-- Cancel Booking Button -->
                    <button class="cancel-button" onclick="confirmCancel('<?php echo $booking['booking_id']; ?>')">Cancel Booking</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</section>

<!-- Cancel Booking Popup -->
<div id="cancelPopup" class="popup-overlay" style="display: none;">
    <div class="popup-content">
        <h2>Are you sure you want to cancel this booking?</h2>
        <p class="refund-note">*Refund is only available if cancellation is done within 24 hours.</p>
        <div class="popup-buttons">
            <button id="confirmCancelButton" class="popup-confirm" onclick="cancelBooking()">Yes, Cancel</button>
            <button class="popup-cancel" onclick="closePopup()">No, Go Back</button>
        </div>
    </div>
</div>

<!-- Hidden Form for Booking Cancellation -->
<form id="cancelForm" method="post" style="display: none;">
    <input type="hidden" name="booking_id" value="">
</form>

</body>
</html>
