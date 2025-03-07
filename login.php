<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$host = 'localhost'; 
$user = 'root'; 
$password = ''; 
$dbname = 'ebook1';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); 
        echo "Stored hash: " . $user['Password'] . "<br>";  
        
        if (isset($user['Password']) && $user['Password'] !== null) {  
            if (password_verify($password, $user['Password'])) {  
                $_SESSION['username'] = $username; 
                header("Location: index.html");
                exit(); 
            } else {
                echo "Invalid username or password.";
            }
        } else {
            echo "Error: Password data is missing for this user.";
        }
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
}
$conn->close();
?>
