<?php
session_start();
include 'connect.php'; // Include your database connection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $consultation_id = $_POST['consultation_id'];
    $sender_id = $_POST['sender_id'];
    $sender_type = $_POST['sender_type'];
    $message = $_POST['message'];

    // Insert message into the chat table
    $query = "INSERT INTO chat (consultation_id, sender_id, sender_type, message, timestamp) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiss', $consultation_id, $sender_id, $sender_type, $message);
    $stmt->execute();

    // Redirect back to chat form
    header("Location: chat_form.php?consultation_id=" . $consultation_id);
    exit();
}
?>