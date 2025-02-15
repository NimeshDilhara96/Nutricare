<?php
session_start(); // Start the session to access session variables

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: admin/adminlogin.html");
    exit();
}

// Database connection parameters
$servername = "localhost"; // Change this if your database is hosted on a different server
$db_username = "root"; // Change this to your database username
$db_password = ""; // Change this to your database password
$db_name = "Nutricare";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $specification = $_POST["specification"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Hash the password for security
    
    // Insert new doctor into the database
    $sql = "INSERT INTO consultant (name, username, specification, email, password) VALUES ('$name', '$username', '$specification', '$email', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New doctor added successfully!";
        header("Location: adminadd_doctor2.php"); // Redirect to dashboard or another page
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
