<?php
session_start();
include 'connect.php'; // Include your database connection
// Check if user or consultant is logged in
if (isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];
    $sender_type = 'user';
} elseif (isset($_SESSION['consultant_id'])) {
    $sender_id = $_SESSION['consultant_id'];
    $sender_type = 'consultant';
} else {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

// Get the consultation ID from query parameters
if (isset($_GET['consultation_id'])) {
    $consultation_id = $_GET['consultation_id'];
} else {
    die("Consultation ID not provided.");
}

// Fetch chat messages for the consultation
$query = "SELECT chat.*, 
                 IF(chat.sender_type = 'user', User.fname, consultant.name) AS sender_name 
          FROM chat 
          LEFT JOIN User ON chat.sender_type = 'user' AND chat.sender_id = User.user_id 
          LEFT JOIN consultant ON chat.sender_type = 'consultant' AND chat.sender_id = consultant.consultant_id 
          WHERE chat.consultation_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $consultation_id);
$stmt->execute();
$result = $stmt->get_result();
$chats = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <style>
        .chat-box { max-width: 600px; margin: 0 auto; }
        .message { padding: 10px; border-bottom: 1px solid #ddd; }
        .message.user { text-align: right; background-color: #e0f7fa; }
        .message.consultant { text-align: left; background-color: #fce4ec; }
    </style>
</head>
<body>
    <div class="chat-box">
        <?php foreach ($chats as $chat): ?>
            <div class="message <?= $chat['sender_type'] ?>">
                <strong><?= htmlspecialchars($chat['sender_name']) ?>:</strong>
                <p><?= htmlspecialchars($chat['message']) ?></p>
                <small><?= $chat['timestamp'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="send_message.php" method="post">
        <input type="hidden" name="consultation_id" value="<?= $consultation_id ?>">
        <input type="hidden" name="sender_id" value="<?= $sender_id ?>">
        <input type="hidden" name="sender_type" value="<?= $sender_type ?>">
        <textarea name="message" rows="4" cols="50" required></textarea>
        <button type="submit">Send</button>
    </form>
</body>
</html>