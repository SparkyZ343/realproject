<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - PG Accommodation Booking</title>
    <link rel="stylesheet" href="../css/AdminRegister.css">
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

    <!-- Admin Registration Form -->
    <section class="register-container">
        <h2>Admin Registration</h2>
        <form action="AdminRegister.php" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="register-button">Register</button>
        </form>
    </section>

</body>
</html>

<?php
include 'db_connect.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $user_type = 'admin'; // Set user type to admin

    if ($password === $confirm_password) {
        // Check if an admin already exists in the users table
        $admin_check_query = "SELECT * FROM users WHERE user_type = 'admin'";
        $admin_check_result = mysqli_query($conn, $admin_check_query);

        if (mysqli_num_rows($admin_check_result) == 0) {
            // No admin exists, proceed with registration
            $check_query = "SELECT * FROM users WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) == 0) {
                // Insert new admin into the users table
                $query = "INSERT INTO users (name, email, password, user_type) VALUES ('$name', '$email', '$password', '$user_type')";
                if (mysqli_query($conn, $query)) {
                    echo "<script>alert('Admin registration successful!'); window.location.href='login.php';</script>";
                } else {
                    echo "<script>alert('Error: Could not register admin.'); window.location.href='AdminRegister.php';</script>";
                }
            } else {
                echo "<script>alert('Email already registered. Please use a different email.'); window.location.href='AdminRegister.php';</script>";
            }
        } else {
            // Admin already exists
            echo "<script>alert('Unauthorized access! Admin already exists.'); window.location.href='AdminRegister.php';</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match.'); window.location.href='AdminRegister.php';</script>";
    }
}
?>
