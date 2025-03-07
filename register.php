<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Database connection
$host = 'localhost'; 
$user = 'root'; 
$password = ''; 
$dbname = 'ebook1';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password and confirm password
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit; // Stop further execution
    }

    // Check if the username already exists
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose a different one.";
        exit; // Stop further execution
    } else {
        // Insert the new user into the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $sql = "INSERT INTO user (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: login.html");
            exit(); // Stop further execution
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
ob_end_flush(); // End output buffering and flush output
?>
