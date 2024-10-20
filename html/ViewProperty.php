<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

// Database connection
include 'db_connect.php';

// Get the property ID from the URL
if (isset($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']);
} else {
    echo "<script>alert('Invalid property.'); window.location.href='SearchPG.php';</script>";
    exit();
}

// Fetch the property details
$query_property = "SELECT * FROM properties WHERE property_id = $property_id";
$result_property = mysqli_query($conn, $query_property);

// Check if the property exists
if (mysqli_num_rows($result_property) > 0) {
    $property = mysqli_fetch_assoc($result_property);
} else {
    echo "<script>alert('Property not found.'); window.location.href='SearchPG.php';</script>";
    exit();
}

// Fetch the property images
$query_images = "SELECT * FROM property_images WHERE property_id = $property_id";
$result_images = mysqli_query($conn, $query_images);

// Fetch the owner details
$owner_id = $property['owner_id'];
$query_owner = "SELECT * FROM users WHERE user_id = $owner_id";
$result_owner = mysqli_query($conn, $query_owner);
$owner = mysqli_fetch_assoc($result_owner);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['name']); ?> - Property Details</title>
    <link rel="stylesheet" href="../css/ViewProperty.css">
    <script>
        function openModal(imageSrc) {
            const modal = document.getElementById("imageModal");
            const modalImg = document.getElementById("modalImage");
            modal.style.display = "block";
            modalImg.src = imageSrc;
        }

        function closeModal() {
            const modal = document.getElementById("imageModal");
            modal.style.display = "none";
        }
    </script>
</head>
<body>

    <!-- Property Details Section -->
    <section class="property-details-container">
        <h1><?php echo htmlspecialchars($property['name']); ?></h1>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
        <p><strong>Price per Month:</strong> ₹<?php echo htmlspecialchars($property['price_per_month']); ?></p>
        <p><strong>Available Rooms:</strong> <?php echo htmlspecialchars($property['available_rooms']); ?> / <?php echo htmlspecialchars($property['total_rooms']); ?></p>
        <p><strong>Amenities:</strong> <?php echo htmlspecialchars($property['amenities']); ?></p>

        <!-- Property Images -->
        <h2>Property Images</h2>
        <div class="property-images">
            <?php if (mysqli_num_rows($result_images) > 0): ?>
                <?php while ($image = mysqli_fetch_assoc($result_images)): ?>
                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Property Image" onclick="openModal('<?php echo htmlspecialchars($image['image_url']); ?>')">
                <?php endwhile; ?>
            <?php else: ?>
                <p>No images available for this property.</p>
            <?php endif; ?>
        </div>

        <!-- Owner Details -->
        <h2>Owner Details</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($owner['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($owner['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($owner['phone_number']); ?></p>

        <!-- Book Now Button -->
        <button class="book-now-btn" onclick="window.location.href='Payment.php?property_id=<?php echo $property['property_id']; ?>'">Book Now</button>
    </section>

    <!-- Image Modal -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="modalImage" src="" alt="Zoomed Image">
        </div>
    </div>

</body>
</html>
