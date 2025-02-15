<?php
// Check if session is not active before starting a new session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: admin/adminlogin.html");
    exit();
}

// Include the database connection file
include 'connect.php';

// Retrieve feedback data from database
$sql = "SELECT id, feedback_date, email, feedback_text FROM feedback";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Feedback Data</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Feedback Date</th>
                    <th>Email</th>
                    <th>Feedback Text</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output feedback data
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['feedback_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['feedback_text']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No feedback found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
