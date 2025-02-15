<?php
session_start(); // Start the session to access session variables

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

// Retrieve user ID from the session
$email = $_SESSION['email'];
$sql = "SELECT user_id FROM user WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
} else {
    echo "User information not found!";
    exit();
}

// Get appointment details from the form
$consultant_id = $_POST['consultant_id'];
$date = $_POST['date'];
$time = $_POST['time'];

// Prepare and execute the SQL query to insert appointment details
$sql = "INSERT INTO consultation (date, time, user_id, consultant_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $date, $time, $user_id, $consultant_id);

if ($stmt->execute()) {
    $success = true;
} else {
    $success = false;
    $error_message = $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <style>
        /* CSS for Popup */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            z-index: 1000;
        }
        .popup h1 {
            margin-top: 0;
            font-size: 24px;
        }
        .popup p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .popup button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .popup button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div id="popup" class="popup">
        <h1>Appointment <?php echo $success ? "Confirmed" : "Failed"; ?></h1>
        <p><?php echo $success ? "Your appointment has been successfully booked." : "There was an error booking your appointment. Please try again later."; ?></p>
        <button id="okButton">OK</button>
        <p id="countdown"></p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show popup
            var popup = document.getElementById('popup');
            popup.style.display = 'block';

            // Countdown timer (5 seconds)
            var countdownElement = document.getElementById('countdown');
            var countdownTime = 5;
            countdownElement.textContent = 'Redirecting in ' + countdownTime + ' seconds';

            var countdownInterval = setInterval(function() {
                countdownTime--;
                countdownElement.textContent = 'Redirecting in ' + countdownTime + ' seconds';
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = 'index.php'; // Redirect after countdown
                }
            }, 1000);

            // Redirect on button click
            var okButton = document.getElementById('okButton');
            okButton.addEventListener('click', function() {
                window.location.href = 'index.php'; // Redirect to the main page on button click
            });
        });
    </script>
</body>
</html>
