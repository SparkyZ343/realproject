<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PG Accommodation Booking</title>
    <link rel="stylesheet" href="../css/UserRegister.css">
</head>
<body>

    <!-- Navigation Bar -->
    <nav>
        <div class="logo">
            <h1>PG Finder</h1>
        </div>
        <ul class="nav-links">
            <li><a href="home.html">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <!-- Register Dropdown -->
            <li class="dropdown">
                <a href="#" class="dropbtn">Register</a>
                <div class="dropdown-content">
                    <a href="UserRegister.php">User</a>
                    <a href="OwnerRegister.php">Owner</a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Register Form -->
    <section class="register-container">
        <h2>Student Registration</h2>
        <form action="#" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <!-- Hidden field for user type -->
            <input type="hidden" name="user_type" value="student">
            <button type="submit" class="register-button">Register</button>
        </form>
    </section>

</body>
</html>

<?php
include 'db_connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $user_type = 'student'; // Default user type

    // Password validation
    if ($password === $confirm_password) {
        // Check if user already exists
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            // Insert new user into the database
            $query = "INSERT INTO users (name, email, phone_number, password, user_type) VALUES ('$name', '$email', '$phone_number', '$password', '$user_type')";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Error: Could not register user.'); window.location.href='UserRegister.php';</script>";
            }
        } else {
            echo "<script>alert('Email already registered. Please use a different email.'); window.location.href='UserRegister.php';</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match.'); window.location.href='UserRegister.php';</script>";
    }
}
?>
