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

// Retrieve admin information from database
$username = $_SESSION['username'];
$sql = "SELECT * FROM consultant WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output admin information
    while($row = $result->fetch_assoc()) {
        $consultant_id = $row['consultant_id']; // Get consultant_id for bookings query
        $consultant_name = $row['username'];
    }
} else {
    echo "Admin information not found!";
}

// Fetch consultant bookings from the database
$booking_sql = "SELECT c.consultation_id, c.date, c.time, u.fname AS user_name
                FROM consultation c
                JOIN user u ON c.user_id = u.user_id
                WHERE c.consultant_id = $consultant_id";
$bookings = $conn->query($booking_sql);

if (!$bookings) {
    echo "Error fetching bookings: " . $conn->error;
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="Coun/style.css">
    
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Admin Dashboard Panel - Diet Plans</title> 
    <style>
        /* CSS for Booking List */
        .booking-list {
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }

        .booking-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }

        .booking-details {
            flex: 1;
        }

        .booking h2 {
            margin-top: 0;
            font-size: 16px;
        }

        .booking p {
            font-size: 14px;
            margin: 5px 0;
        }

        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 3px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }

        .countdown {
            font-size: 18px;
            color: #333;
            margin-top: 20px;
        }

        /* Styles for Reschedule and Cancel Modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
                <img src="images/logo.png" alt="">
            </div>
            <span class="logo_name">Nutricare</span>
        </div>
        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="#">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Dashboard</span>
                </a></li>
                <li><a href="adminCoundaily_insert2.php">
                    <i class="uil uil-files-landscapes"></i>
                    <span class="link-name">Add Meal Plan</span>
                </a></li>
                <li><a href="adminCoundiet_plan2.php">
                    <i class="uil uil-files-landscapes"></i>
                    <span class="link-name">Add Diet Plan</span>
                </a></li>
                <li><a href="adminCounviewmealplan2.php">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">View Meal Plan</span>
                </a></li>
                <li><a href="adminCounviewbooking.php">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Appoinment</span>
                </a></li>
            </ul>
            <ul class="logout-mode">
                <li><a href="Coun/logout.php">
                    <i class="uil uil-signout"></i>
                    <span class="link-name">Logout</span>
                </a></li>
                <li class="mode">
                    <a href="#">
                        <i class="uil uil-moon"></i>
                        <span class="link-name">Dark Mode</span>
                    </a>
                    <div class="mode-toggle">
                        <span class="switch"></span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
            <h1>Welcome, <?php echo htmlspecialchars($consultant_name); ?></h1>
            <img src="images/profile.jpg" alt="Profile Image">
        </div>
        <div class="dash-content"> 
            <div class="booking-list">
                <h1>Your Appointments</h1>
                <?php if ($bookings && $bookings->num_rows > 0): ?>
                    <?php while($row = $bookings->fetch_assoc()): ?>
                        <div class="booking-item">
                            <div class="booking-details">
                                <h2>Client: <?php echo htmlspecialchars($row['user_name']); ?></h2>
                                <p>Date: <?php echo htmlspecialchars($row['date']); ?></p>
                                <p>Time: <?php echo htmlspecialchars($row['time']); ?></p>
                            </div>
                            <div class="actions">
                                <button class="button" onclick="showCancelModal(<?php echo $row['consultation_id']; ?>)">Cancel</button>
                                <button class="button" onclick="showRescheduleModal(<?php echo $row['consultation_id']; ?>)">Reschedule</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No bookings found.</p>
                <?php endif; ?>
            </div>

            <div id="countdown-timer" class="countdown">
                <!-- Countdown timer will be displayed here -->
            </div>
        </div>
    </section>

    <!-- Cancel Appointment Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('cancelModal')">&times;</span>
            <h2>Cancel Appointment</h2>
            <form id="cancelForm" action="Councancel_appointment.php" method="POST">
                <input type="hidden" id="cancel_id" name="consultation_id" value="">
                <p>Are you sure you want to cancel this appointment?</p>
                <input type="submit" class="button" value="Confirm Cancel">
                <button type="button" class="button" onclick="closeModal('cancelModal')">Close</button>
            </form>
        </div>
    </div>

    <!-- Reschedule Appointment Modal -->
    <div id="rescheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('rescheduleModal')">&times;</span>
            <h2>Reschedule Appointment</h2>
            <form id="rescheduleForm" action="Counreschedule_appointment.php" method="POST">
                <input type="hidden" id="reschedule_id" name="consultation_id" value="">
                <label for="new_date">New Date:</label>
                <input type="date" id="new_date" name="new_date" required>
                <label for="new_time">New Time:</label>
                <input type="time" id="new_time" name="new_time" required>
                <input type="submit" class="button" value="Confirm Reschedule">
                <button type="button" class="button" onclick="closeModal('rescheduleModal')">Close</button>
            </form>
        </div>
    </div>

    <script>
        function showCancelModal(id) {
            document.getElementById('cancel_id').value = id;
            document.getElementById('cancelModal').style.display = "block";
        }

        function showRescheduleModal(id) {
            document.getElementById('reschedule_id').value = id;
            document.getElementById('rescheduleModal').style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
    </script>
</body>
</html>
