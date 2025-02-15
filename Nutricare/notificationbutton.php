<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "Nutricare";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch notifications
$sql = "SELECT message, created_at FROM Notifications WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$user_id = 1; // Replace with the actual user_id
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
if ($result->num_rows > 0) {
    // Fetch all notifications into an array
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .notifications-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .notifications-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-message {
            font-size: 16px;
            color: #333;
        }
        .notification-time {
            font-size: 14px;
            color: #888;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 14px;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="notifications-container">
        <div class="notifications-header">Notifications</div>
        <?php if (!empty($notifications)) : ?>
            <?php foreach ($notifications as $notification) : ?>
                <div class="notification-item">
                    <div class="notification-message">
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </div>
                    <div class="notification-time">
                        <?php echo date('d M Y, H:i', strtotime($notification['created_at'])); ?>
                    </div>
                    <form method="post" style="margin: 0;">
                       
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="notification-item">No notifications found.</div>
        <?php endif; ?>
    </div>

    <script>
        // Optionally add JavaScript functionality if needed
    </script>
</body>
</html>
