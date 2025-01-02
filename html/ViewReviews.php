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
$query = "SELECT user_type FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if the logged-in user is an owner
if ($user['user_type'] !== 'owner') {
    echo "<script>alert('Unauthorized access! Only owners can access this page.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch property ID from query parameter
if (!isset($_GET['property_id'])) {
    echo "<script>alert('Invalid property selection!'); window.location.href='OwnerViewBookings.php';</script>";
    exit();
}

$property_id = $_GET['property_id'];

// Fetch property reviews
$query_reviews = "
    SELECT r.rating, r.review_text, r.created_at, 
           u.name AS user_name
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.property_id = '$property_id'
    ORDER BY r.created_at DESC
";
$result_reviews = mysqli_query($conn, $query_reviews);

// Fetch property name for the header
$query_property_name = "SELECT name FROM properties WHERE property_id = '$property_id'";
$result_property_name = mysqli_query($conn, $query_property_name);
$property = mysqli_fetch_assoc($result_property_name);
$property_name = $property['name'] ?? 'Unknown Property';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Reviews</title>
    <link rel="stylesheet" href="../css/ViewReviews.css">
</head>
<body>

    <!-- Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Property Reviews</h1>
        </div>
        <ul class="nav-links">
            <li><a href="OwnerDashboard.php">Dashboard</a></li>
            <li><a href="ManageProperties.php">Manage Properties</a></li>
            <li><a href="OwnerViewBookings.php">View Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Reviews Section -->
    <section class="reviews-container">
        <h2>Reviews for: <?php echo htmlspecialchars($property_name); ?></h2>

        <?php if (mysqli_num_rows($result_reviews) > 0): ?>
            <div class="reviews-list">
                <?php while ($review = mysqli_fetch_assoc($result_reviews)): ?>
                    <div class="review-card">
                        <h3><?php echo htmlspecialchars($review['user_name']); ?></h3>
                        <p class="rating">Rating: <?php echo htmlspecialchars($review['rating']); ?> / 10</p>
                        <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                        <p class="review-date">Reviewed on: <?php echo htmlspecialchars($review['created_at']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No reviews available for this property.</p>
        <?php endif; ?>
    </section>

</body>
</html>
