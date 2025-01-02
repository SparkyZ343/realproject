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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price_per_month = mysqli_real_escape_string($conn, $_POST['price_per_month']);
    $total_rooms = mysqli_real_escape_string($conn, $_POST['total_rooms']);
    $available_rooms = mysqli_real_escape_string($conn, $_POST['available_rooms']);
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);

    // Image upload logic
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Create the uploads directory if it doesn't exist
    }

    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 5000000) { // 5MB limit
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow specific file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Try to upload file
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;  // Save the image path for database insertion

            // Insert property into the database
            $owner_id = $user['user_id']; // Owner's ID from the session
            $query = "INSERT INTO properties (owner_id, name, description, location, price_per_month, available_rooms, total_rooms, amenities, image_url, created_at) 
                      VALUES ('$owner_id', '$property_name', '$description', '$location', '$price_per_month', '$available_rooms', '$total_rooms', '$amenities', '$image_url', NOW())";

            if (mysqli_query($conn, $query)) {
                $property_id = mysqli_insert_id($conn); // Get the ID of the inserted property

                // Handle multiple image upload for the property_images table
                if (isset($_FILES['property_images'])) {
                    foreach ($_FILES['property_images']['tmp_name'] as $key => $tmp_name) {
                        $image_name = basename($_FILES['property_images']['name'][$key]);
                        $image_path = $target_dir . $image_name;

                        // Move each uploaded file
                        if (move_uploaded_file($tmp_name, $image_path)) {
                            // Insert into the property_images table
                            $query_images = "INSERT INTO property_images (property_id, image_url) VALUES ('$property_id', '$image_path')";
                            mysqli_query($conn, $query_images);
                        }
                    }
                }

                echo "<script>alert('Property added successfully with images!'); window.location.href='ManageProperties.php';</script>";
            } else {
                echo "<script>alert('Error: Could not add property.'); window.location.href='ManageProperties.php';</script>";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties</title>
    <link rel="stylesheet" href="../css/ManageProperties.css">
</head>
<body>

    <!-- Owner Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>Owner Dashboard</h1>
        </div>
        <ul class="nav-links">
            <li><a href="manageProperties.php">Manage Properties</a></li>
            <li><a href="viewBookings.php">View Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Add Property Form -->
    <section class="property-container">
        <h2>Add New Property</h2>
        <form action="ManageProperties.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="property_name">Property Name</label>
                <input type="text" id="property_name" name="property_name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="price_per_month">Price per Month</label>
                <input type="number" id="price_per_month" name="price_per_month" required>
            </div>
            <div class="form-group">
                <label for="total_rooms">Total Rooms</label>
                <input type="number" id="total_rooms" name="total_rooms" required>
            </div>
            <div class="form-group">
                <label for="available_rooms">Available Rooms</label>
                <input type="number" id="available_rooms" name="available_rooms" required>
            </div>
            <div class="form-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <input type="text" id="amenities" name="amenities" required>
            </div>
            <div class="form-group">
                <label for="image">Upload Main Image</label>
                <input type="file" id="image" name="image" required>
            </div>
            <div class="form-group">
                <label for="property_images">Upload Additional Images</label>
                <input type="file" id="property_images" name="property_images[]" multiple>
            </div>
            <button type="submit" class="add-property-button">Add Property</button>
        </form>
    </section>

</body>
</html>
