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

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultation_id = intval($_POST['consultation_id']);

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete related records from chat table
        $sql = "DELETE FROM chat WHERE consultation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $consultation_id);
        $stmt->execute();
        $stmt->close();

        // Delete the consultation record
        $sql = "DELETE FROM consultation WHERE consultation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $consultation_id);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        $message = "Appointment cancelled successfully.";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();

        $message = "Error cancelling appointment: " . $exception->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <script>
        function showPopupAndRedirect(message) {
            alert(message);
            window.location.href = "booking.php";
        }
    </script>
</head>
<body onload="showPopupAndRedirect('<?php echo $message; ?>')">
</body>
</html>
