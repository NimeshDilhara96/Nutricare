<?php
session_start(); // Start the session to access session variables

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: consultant/counsultlogin.html");
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

// Retrieve form data
$day_of_week = $_POST['day_of_week'];
$examples = $_POST['examples'];

// Check if a diet plan for this day already exists
$check_sql = "SELECT * FROM diet_plan WHERE day_of_week='$day_of_week'";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows > 0) {
    // Update the existing diet plan
    $update_sql = "UPDATE diet_plan SET examples='$examples' WHERE day_of_week='$day_of_week'";
    if ($conn->query($update_sql) === TRUE) {
        $message = "Diet plan updated successfully!";
    } else {
        $message = "Error: " . $update_sql . "<br>" . $conn->error;
    }
} else {
    // Insert new diet plan
    $insert_sql = "INSERT INTO diet_plan (day_of_week, examples)
                   VALUES ('$day_of_week', '$examples')";

    if ($conn->query($insert_sql) === TRUE) {
        $message = "New diet plan added successfully!";
    } else {
        $message = "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();

// Redirect back to the form with a message
header("Location: adminCoundiet_plan2.php?message=" . urlencode($message));
exit();
?>
