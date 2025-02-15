<?php
session_start();

// Include the database connection file
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "User not logged in";
    exit;
}

// Check if feedback is set in POST request
if (isset($_POST['feedback'])) {
    $feedback_text = $_POST['feedback'];
    $email = $_SESSION['email'];

    // Get the current date
    $feedback_date = date('Y-m-d');

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO feedback (feedback_date, email, feedback_text) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $feedback_date, $email, $feedback_text);

    if ($stmt->execute()) {
        echo "Feedback received successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Feedback text is required";
}
?>
