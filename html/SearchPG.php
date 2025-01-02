<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Unauthorized access! Please login.'); window.location.href='login.php';</script>";
    exit();
}

// Database connection
include 'db_connect.php';

// Initialize filter variables
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; // Default: Low to High

// Build query based on filters
$query_properties = "SELECT * FROM properties WHERE status = 'active'"; // Only active properties

// Add location filter if specified
if (!empty($location)) {
    $query_properties .= " AND location LIKE '%$location%'";  // Use AND to add location filter
}

// Add sorting by price if specified
$query_properties .= " ORDER BY price_per_month $sort_order";

// Execute the query
$result_properties = mysqli_query($conn, $query_properties);

// Check if the query executed successfully
if (!$result_properties) {
    echo "Error executing query: " . mysqli_error($conn);
    exit();
}

// Fetch distinct locations for the location dropdown
$query_locations = "SELECT DISTINCT location FROM properties";
$result_locations = mysqli_query($conn, $query_locations);

// Function to fetch the average rating for a property
function getAverageRating($conn, $property_id) {
    $query_rating = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE property_id = $property_id";
    $result_rating = mysqli_query($conn, $query_rating);
    if ($result_rating) {
        $row = mysqli_fetch_assoc($result_rating);
        return round($row['avg_rating'], 1); // Round to 1 decimal place
    }
    return null; // Return null if no ratings found
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search PG</title>
    <link rel="stylesheet" href="../css/SearchPG.css">
</head>
<body>

    <!-- User Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Search PG</h1>
        </div>
        <ul class="nav-links">
            <li><a href="UserDashboard.html">Dashboard</a></li>
            <li><a href="MyBookings.php">My Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Filter Section (Always Visible) -->
    <section class="filter-section">
        <form action="SearchPG.php" method="GET" class="filter-form">
            <div class="filter-group">
                <label for="location">Location</label>
                <select id="location" name="location">
                    <option value="">Select Location</option>
                    <?php while ($location_option = mysqli_fetch_assoc($result_locations)): ?>
                        <option value="<?php echo htmlspecialchars($location_option['location']); ?>" <?php echo ($location == $location_option['location']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($location_option['location']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort_order">Sort by Price</label>
                <select id="sort_order" name="sort_order">
                    <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Low to High</option>
                    <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>High to Low</option>
                </select>
            </div>
            <button type="submit" class="filter-button">Apply</button>
        </form>
    </section>

    <!-- PG Search Results -->
    <section class="search-results-container">
        
        <div class="properties-list">
            <?php if (mysqli_num_rows($result_properties) > 0): ?>
                <?php while ($property = mysqli_fetch_assoc($result_properties)): ?>
                    <?php
                    // Get average rating for the property
                    $avg_rating = getAverageRating($conn, $property['property_id']);
                    ?>
<div class="property-card">
    <img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="Property Image">
    <h3><?php echo htmlspecialchars($property['name']); ?></h3>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
    <p><strong>Price per Month:</strong> ₹<?php echo htmlspecialchars($property['price_per_month']); ?></p>
    <p><strong>Available Rooms:</strong> 
        <?php 
        if ($property['available_rooms'] == 0) {
            echo 'Fully Booked';
        } else {
            echo htmlspecialchars($property['available_rooms']) . ' / ' . htmlspecialchars($property['total_rooms']);
        }
        ?>
    </p>
    <p><strong>Amenities:</strong> <?php echo htmlspecialchars($property['amenities']); ?></p>
    <p><strong> Rating:</strong> <?php echo ($avg_rating !== null) ? $avg_rating . '/10' : 'No ratings available'; ?></p>
    <button 
        class="book-now-btn" 
        onclick="window.location.href='ViewProperty.php?property_id=<?php echo $property['property_id']; ?>'"
        <?php if ($property['available_rooms'] == 0) echo 'disabled style="cursor:not-allowed; opacity:0.5;"'; ?>>
        View Details
    </button>
</div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No properties match your search criteria.</p>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>
