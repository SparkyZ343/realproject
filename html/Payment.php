<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
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

// Fetch the owner details
$owner_id = $property['owner_id'];
$query_owner = "SELECT * FROM users WHERE user_id = $owner_id";
$result_owner = mysqli_query($conn, $query_owner);
$owner = mysqli_fetch_assoc($result_owner);

// Handle the payment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $total_amount = $property['price_per_month'];
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL; // Optional end date
    $booking_date = date('Y-m-d H:i:s'); // Booking date with time
    $status = 'pending'; // Default booking status

    // Check if the property is available for the selected dates
    $query_check_availability = "SELECT * FROM bookings 
                                 WHERE property_id = $property_id 
                                 AND (start_date <= '$end_date' AND end_date >= '$start_date')
                                 AND status IN ('booked', 'pending')";

    $result_check_availability = mysqli_query($conn, $query_check_availability);
    
    if (mysqli_num_rows($result_check_availability) > 0) {
        echo "<script>alert('The property is not available for the selected dates. Please choose different dates.');</script>";
    } else {
        // Determine payment status
        if ($payment_method == 'card') {
            $payment_status = 'completed'; // Simulating successful online payment
            $status = 'booked';
            echo "<script>alert('Payment successful via card.');</script>";
        } elseif ($payment_method == 'cash') {
            $payment_status = 'pending'; // For cash payments, set payment status as pending
            $status = 'booked';
            echo "<script>alert('You have selected to pay directly to the owner.');</script>";
        }

        // Insert booking details into the bookings table
        $query_booking = "INSERT INTO bookings (user_id, property_id, booking_date, start_date, end_date, payment_status, total_amount, status)
                          VALUES ('$user_id', '$property_id', '$booking_date', '$start_date', ".($end_date ? "'$end_date'" : "NULL").", '$payment_status', '$total_amount', '$status')";

        if (mysqli_query($conn, $query_booking)) {
            // Automatically decrease the available rooms count after booking
            $query_update_rooms = "UPDATE properties 
                                   SET available_rooms = available_rooms - 1 
                                   WHERE property_id = $property_id AND available_rooms > 0";
            
            mysqli_query($conn, $query_update_rooms);

            // Optional: Check if all rooms are booked and mark the property as unavailable
            $query_check_availability = "SELECT available_rooms, total_rooms 
                                         FROM properties 
                                         WHERE property_id = $property_id";
            $result_check_availability = mysqli_query($conn, $query_check_availability);
            $property_check = mysqli_fetch_assoc($result_check_availability);

            if ($property_check['available_rooms'] == 0) {
                // Optionally, mark the property as unavailable in the frontend or add additional status
                echo "<script>alert('The property is fully booked.');</script>";
            }

            // Redirect to My Bookings page
            echo "<script>alert('Booking successful!'); window.location.href='MyBookings.php';</script>";
        } else {
            echo "<script>alert('Error in booking.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for <?php echo htmlspecialchars($property['name']); ?></title>
    <link rel="stylesheet" href="../css/Payment.css">
</head>
<body>

    <section class="payment-container">
        <h1>Complete Your Booking</h1>

        <!-- Property details and amount -->
        <div class="property-summary">
            <h2>Property: <?php echo htmlspecialchars($property['name']); ?></h2>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
            <p><strong>Price per Month:</strong> ₹<?php echo htmlspecialchars($property['price_per_month']); ?></p>
        </div>

        <!-- Payment Form -->
        <form action="" method="POST" class="payment-form">
            <h2>Payment Options</h2>
            
            <!-- Booking start and end dates -->
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date (Optional)</label>
                <input type="date" id="end_date" name="end_date">
            </div>

            <!-- Payment amount -->
            <p class="amount"><strong>Total Amount:</strong> ₹<?php echo htmlspecialchars($property['price_per_month']); ?></p>

            <!-- Payment Method Selection -->
            <label>
                <input type="radio" name="payment_method" value="card" required> Pay Online (Credit/Debit Card)
            </label>

            <div class="card-payment-details">
                <div>
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" placeholder="xxxx xxxx xxxx xxxx">
                </div>
                <div>
                    <label for="expiry_date">Expiry Date:</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                </div>
                <div>
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" placeholder="xxx">
                </div>
            </div>

            <label>
                <input type="radio" name="payment_method" value="cash"> Pay Directly to Owner
            </label>

            <!-- Owner details -->
            <p class="owner-details"><strong>Owner:</strong> <?php echo htmlspecialchars($owner['name']); ?></p>
            <p class="owner-details"><strong>Phone:</strong> <?php echo htmlspecialchars($owner['phone_number']); ?></p>

            <button type="submit" class="submit-btn">Confirm Payment</button>
        </form>
    </section>

</body>
</html>
