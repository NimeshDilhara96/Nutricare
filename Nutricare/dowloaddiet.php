<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
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

// Query to retrieve diet plan information
$diet_plan_sql = "SELECT day_of_week, examples FROM diet_plan";
$diet_plan_result = $conn->query($diet_plan_sql);

if ($diet_plan_result->num_rows > 0) {
    // Set headers to download file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=diet_plan.csv');

    // Open file output stream
    $output = fopen('php://output', 'w');

    // Output column headings
    fputcsv($output, array('Day of Week', 'Examples'));

    // Output rows from the database
    while ($row = $diet_plan_result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    // Close file output stream
    fclose($output);
} else {
    echo "No diet plan found";
}

// Close connection
$conn->close();
exit();
?>
