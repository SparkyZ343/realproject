<?php
session_start(); // Start the session

include 'db_connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check user credentials
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_type = $row['user_type']; // Get user type

        // Store user ID, email, and user type in session
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user'] = $email; // Store email in session
        $_SESSION['user_type'] = $user_type; // Store user type (admin, owner, student)

        if ($user_type == 'admin') {
            $_SESSION['admin'] = true;
        }

        // Redirect based on user type
        if ($user_type == 'student') {
            header("Location: UserDashboard.html");
        } elseif ($user_type == 'owner') {
            header("Location: OwnerDashboard.php");
        } elseif ($user_type == 'admin') {
            header("Location: AdminDashboard.html");
        }
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PG Accommodation Booking</title>
    <link rel="stylesheet" href="../css/login.css">
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
            <li><a href="UserRegister.php">Register</a></li>
        </ul>
    </nav>

    <!-- Login Form -->
    <section class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </section>
</body>
</html>
