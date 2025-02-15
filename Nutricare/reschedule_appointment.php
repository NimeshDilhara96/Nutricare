<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "Nutricare";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultation_id = intval($_POST['consultation_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    $sql = "UPDATE consultation SET date = ?, time = ? WHERE consultation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $date, $time, $consultation_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Appointment rescheduled successfully.');
            window.location.href = 'booking.php';
        </script>";
    } else {
        echo "Error rescheduling appointment: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
