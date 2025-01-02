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

if (isset($_GET['action']) && isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $action = $_GET['action'];

    // Activate property
    if ($action == 'activate') {
        $query_activate = "UPDATE properties SET status = 'active' WHERE property_id = '$property_id'";
        if (mysqli_query($conn, $query_activate)) {
            echo "<script>alert('Property activated successfully.'); window.location.href='owner_view_properties.php';</script>";
        } else {
            echo "<script>alert('Error activating property.');</script>";
        }
    }

    // Deactivate property
    if ($action == 'deactivate') {
        $query_deactivate = "UPDATE properties SET status = 'inactive' WHERE property_id = '$property_id'";
        if (mysqli_query($conn, $query_deactivate)) {
            echo "<script>alert('Property deactivated successfully.'); window.location.href='owner_view_properties.php';</script>";
        } else {
            echo "<script>alert('Error deactivating property.');</script>";
        }
    }
}
// Fetch properties for the logged-in owner
$owner_id = $user['user_id'];
$query_properties = "SELECT * FROM properties WHERE owner_id = '$owner_id'";
$result_properties = mysqli_query($conn, $query_properties);

// Update property details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_property'])) {
    $property_id = $_POST['property_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price_per_month = $_POST['price_per_month'];
    $total_rooms = $_POST['total_rooms'];
    $available_rooms = $_POST['available_rooms'];
    $amenities = $_POST['amenities'];

    $query_update = "UPDATE properties SET 
        name = '$name', 
        description = '$description', 
        location = '$location', 
        price_per_month = '$price_per_month', 
        total_rooms = '$total_rooms', 
        available_rooms = '$available_rooms', 
        amenities = '$amenities' 
        WHERE property_id = '$property_id'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Property updated successfully.'); window.location.href='owner_view_properties.php';</script>";
    } else {
        echo "<script>alert('Error updating property.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties</title>
    <link rel="stylesheet" href="../css/owner_view_properties.css">
    <!-- <style>

        .edit-form-container {
    display: block; /* Initially hidden */
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.edit-form-container h3 {
    margin-bottom: 15px;
    color: #333;
}

.edit-form-container label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
    color: #555;
}

.edit-form-container input,
.edit-form-container textarea,
.edit-form-container select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
    box-sizing: border-box;
}

.edit-form-container button {
    background-color: #004080;
    color: white;
    border: none;
    padding: 10px 15px;
    font-size: 1em;
    border-radius: 5px;
    cursor: pointer;
}

.edit-form-container button:hover {
    background-color: #0059b3;
}
    </style> -->
</head>
<body>

    <!-- Owner Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Owner Dashboard</h1>
        </div>
        <ul class="nav-links">
            <li><a href="OwnerProfile.php">My Profile</a></li>
            <li><a href="owner_view_properties.php">Manage Properties</a></li>
            <li><a href="OwnerViewBookings.php">View Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Dashboard Content -->
    <section class="dashboard-container">
        <h2>Your Properties</h2>
        
        <!-- Display Properties -->
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
                    <th>Status</th>
                    <th>Actions</th>
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
                            <td><?php echo htmlspecialchars($property['status']); ?></td>
                            <td>
                                <a href="?action=edit&property_id=<?php echo $property['property_id']; ?>">Edit</a> |
                                <?php if ($property['status'] == 'active'): ?>
                                    <a href="?action=deactivate&property_id=<?php echo $property['property_id']; ?>">Deactivate</a>
                                <?php else: ?>
                                    <a href="?action=activate&property_id=<?php echo $property['property_id']; ?>">Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No properties found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Edit Property Form -->
        <?php if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['property_id'])): ?>
            <?php
            $property_id = $_GET['property_id'];
            $query_edit = "SELECT * FROM properties WHERE property_id = '$property_id'";
            $result_edit = mysqli_query($conn, $query_edit);
            $property_to_edit = mysqli_fetch_assoc($result_edit);
            ?>
            <form method="POST" action="" class="edit-form-container">
                <h3>Edit Property</h3>
                <input type="hidden" name="property_id" value="<?php echo $property_to_edit['property_id']; ?>">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($property_to_edit['name']); ?>" required><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($property_to_edit['description']); ?></textarea><br>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($property_to_edit['location']); ?>" required><br>

                <label for="price_per_month">Price per Month:</label>
                <input type="number" id="price_per_month" name="price_per_month" value="<?php echo htmlspecialchars($property_to_edit['price_per_month']); ?>" required><br>

                <label for="total_rooms">Total Rooms:</label>
                <input type="number" id="total_rooms" name="total_rooms" value="<?php echo htmlspecialchars($property_to_edit['total_rooms']); ?>" required><br>

                <label for="available_rooms">Available Rooms:</label>
                <input type="number" id="available_rooms" name="available_rooms" value="<?php echo htmlspecialchars($property_to_edit['available_rooms']); ?>" required><br>

                <label for="amenities">Amenities:</label>
                <textarea id="amenities" name="amenities" required><?php echo htmlspecialchars($property_to_edit['amenities']); ?></textarea><br>

                <button type="submit" name="update_property">Update Property</button>
            </form>
        <?php endif; ?>
    </section>

</body>
</html>
