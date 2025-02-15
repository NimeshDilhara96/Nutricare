<?php
session_start(); // Start the session to access session variables

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "Nutricare";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user information from database
$email = $_SESSION['email'];
$sql = "SELECT * FROM user WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $user_name = $row['username'];
        $user_email = $row['email'];
        $user_fname = $row['fname'];
        $user_id = $row['user_id']; // Get user_id for bookings query
        $profile_picture = $row['profile_picture']; // Assuming the column name is 'profile_picture'
        // Other conditions and information retrieval as per your existing code
    }
} else {
    echo "User information not found!";
}

// Fetch user bookings from the database
$booking_sql = "SELECT c.consultation_id, c.date, c.time, cons.name AS consultant_name
                FROM consultation c
                JOIN consultant cons ON c.consultant_id = cons.consultant_id
                WHERE c.user_id = $user_id";
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>Admin Dashboard Panel</title>
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
                <li><a href="index.php">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Dashboard</span>
                </a></li>
                <li><a href="mealplans.php">
                    <i class="uil uil-files-landscapes"></i>
                    <span class="link-name">Meal Plans</span>
                </a></li>
                <li><a href="dietplan.php">
                    <i class="uil uil-files-landscapes"></i>
                    <span class="link-name">Diet Plans</span>
                </a></li>
                <li><a href="updateprofileview.php">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">My profile</span>
                </a></li>
                <li><a href="knowlegebase.php">
                    <i class="uil uil-thumbs-up"></i>
                    <span class="link-name">Shopping list</span>
                </a></li>
                <li><a href="knowlegebase.php">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Knowledge Base</span>
                </a></li>
                <li><a href="counsultant.php">
                    <i class="uil uil-share"></i>
                    <span class="link-name">Channeling</span>
                </a></li>
                <li><a href="booking.php">
                    <i class="uil uil-share"></i>
                    <span class="link-name">Booking</span>
                </a></li>
            </ul>
            <ul class="logout-mode">
                <li><a href="logout.php">
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
            <h1>Welcome, <?php echo htmlspecialchars($user_fname); ?></h1>
            <a href="updateprofileview.php"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" style="width:42px;height:42px;"></a>
        </div>
        <div class="dash-content">
            <div class="booking-list">
                <h1>Your Bookings</h1>
                <?php if ($bookings && $bookings->num_rows > 0): ?>
                    <?php while($row = $bookings->fetch_assoc()): ?>
                        <div class="booking-item">
                            <div class="booking-details">
                                <h2>Consultant: <?php echo htmlspecialchars($row['consultant_name']); ?></h2>
                                <p>Date: <?php echo htmlspecialchars($row['date']); ?></p>
                                <p>Time: <?php echo htmlspecialchars($row['time']); ?></p>
                            </div>
                            <div class="actions">
                                <button class="button" onclick="showCancelModal(<?php echo $row['consultation_id']; ?>)">Cancel</button>
                                <button class="button" onclick="showRescheduleModal(<?php echo $row['consultation_id']; ?>)">Reschedule</button>
                                <a href="chat_form.php?consultation_id=<?php echo $row['consultation_id']; ?>" class="button">Chat</a>
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
            <form id="cancelForm" action="cancel_appointment.php" method="POST">
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
            <form id="rescheduleForm" action="reschedule_appointment.php" method="POST">
                <input type="hidden" id="reschedule_id" name="consultation_id" value="">
                <p>New Date: <input type="date" name="date" required></p>
                <p>New Time: <input type="time" name="time" required></p>
                <input type="submit" class="button" value="Confirm Reschedule">
                <button type="button" class="button" onclick="closeModal('rescheduleModal')">Close</button>
            </form>
        </div>
    </div>

    <script>
        function showCancelModal(consultation_id) {
            document.getElementById('cancel_id').value = consultation_id;
            document.getElementById('cancelModal').style.display = 'block';
        }

        function showRescheduleModal(consultation_id) {
            document.getElementById('reschedule_id').value = consultation_id;
            document.getElementById('rescheduleModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Countdown Timer Function
        function startCountdown(duration) {
            let timer = duration, minutes, seconds;
            const countdownElement = document.getElementById('countdown-timer');
            
            const interval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                countdownElement.textContent = 'Time until next booking update: ' + minutes + ":" + seconds;

                if (--timer < 0) {
                    timer = duration;
                    // Optionally: Fetch new bookings or perform any other action
                }
            }, 1000);
        }

        // Start the countdown with a 1-minute duration (60 seconds)
        startCountdown(60);
    </script>
</body>
</html>
