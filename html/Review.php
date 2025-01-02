<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $property_id = $_POST['property_id'];

    // If the form is submitted
    if (isset($_POST['rating']) && isset($_POST['review_text'])) {
        $rating = $_POST['rating'];
        $review_text = $_POST['review_text'];

        // Insert the review into the database
        $query = "
            INSERT INTO reviews (user_id, property_id, rating, review_text)
            VALUES ('$user_id', '$property_id', '$rating', '$review_text')
        ";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Review submitted successfully!'); window.location.href='MyBookings.php';</script>";
        } else {
            echo "<script>alert('Error submitting review: " . mysqli_error($conn) . "'); window.location.href='MyBookings.php';</script>";
        }
        exit();
    }
} else {
    echo "<script>alert('Invalid access.'); window.location.href='MyBookings.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provide Review</title>
    <link rel="stylesheet" href="../css/Review.css">
</head>
<body>

<section class="review-container">
    <h1>Provide Your Review</h1>
    <form method="post" action="">
        <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($_POST['property_id']); ?>">

        <label for="rating">Rating (Out of 10):</label>
        <input type="number" name="rating" id="rating" min="1" max="10" step="0.1" required>

        <label for="review_text">Detailed Review:</label>
        <textarea name="review_text" id="review_text" rows="5" placeholder="Write your review here..." required></textarea>

        <button type="submit" class="submit-review-button">Submit Review</button>
    </form>
</section>

</body>
</html>
