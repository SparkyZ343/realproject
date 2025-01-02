<?php

// Database connection
include 'db_connect.php';

// Initialize filter variables
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 100000;
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$amenities = isset($_GET['amenities']) ? mysqli_real_escape_string($conn, $_GET['amenities']) : '';

// Build query based on filters
$query_properties = "SELECT * FROM properties WHERE price_per_month BETWEEN $min_price AND $max_price";

// Add location filter if specified
if (!empty($location)) {
    $query_properties .= " AND location LIKE '%$location%'";
}

// Add amenities filter if specified
if (!empty($amenities)) {
    $amenities_array = explode(",", $amenities);
    foreach ($amenities_array as $amenity) {
        $query_properties .= " AND amenities LIKE '%" . mysqli_real_escape_string($conn, trim($amenity)) . "%'";
    }
}

// Execute the query
$result_properties = mysqli_query($conn, $query_properties);

// Check if the query executed successfully
if (!$result_properties) {
    // Output error message if the query failed
    echo "Error executing query: " . mysqli_error($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Properties</title>
    <link rel="stylesheet" href="../css/SearchPG.css">
    <script>
        function toggleFilter() {
            var filterSection = document.getElementById('filter-section');
            filterSection.style.display = filterSection.style.display === "block" ? "none" : "block";
        }

        function closeFilter(event) {
            var filterSection = document.getElementById('filter-section');
            if (event.target !== filterSection && !filterSection.contains(event.target) && event.target.className !== 'filter-toggle-btn') {
                filterSection.style.display = 'none';
            }
        }

        document.addEventListener('click', closeFilter);
    </script>
</head>
<body>

    <!-- User Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Search PG</h1>
        </div>
        <ul class="nav-links">
            <li><a href="home.html">Home</a></li>
        </ul>
    </nav>


    <!-- PG Search Results -->
    <section class="search-results-container">
        <h2>Available PG Accommodations</h2>
        <div class="properties-list">
            <?php if (mysqli_num_rows($result_properties) > 0): ?>
                <?php while ($property = mysqli_fetch_assoc($result_properties)): ?>
                    <div class="property-card">
                        <img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="Property Image">
                        <h3><?php echo htmlspecialchars($property['name']); ?></h3>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
                        <p><strong>Price per Month:</strong> ₹<?php echo htmlspecialchars($property['price_per_month']); ?></p>
                        <p><strong>Available Rooms:</strong> <?php echo htmlspecialchars($property['available_rooms']); ?> / <?php echo htmlspecialchars($property['total_rooms']); ?></p>
                        <p><strong>Amenities:</strong> <?php echo htmlspecialchars($property['amenities']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No properties match your filter criteria.</p>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>
