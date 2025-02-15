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
        // You can retrieve other user information here if needed
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
                WHERE c.user_id = $user_id
                ORDER BY c.date, c.time
                LIMIT 1"; // Fetch the nearest upcoming booking
$bookings = $conn->query($booking_sql);

$nearest_booking = null;
if ($bookings && $bookings->num_rows > 0) {
    $nearest_booking = $bookings->fetch_assoc();
} else {
    $nearest_booking = null;
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
    <title>Consultant Dashboard</title>
    <style>
    /* CSS for Doctors Section */
    .doctors {
        max-width: 600px;
        margin: 0 auto;
        padding: 10px;
    }

    .doctor {
        display: flex;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
    }

    .doctor img {
        width: 100px;
        height: auto;
        margin-right: 10px;
        border-radius: 5px;
    }

    .doctor-details {
        flex: 1;
    }

    .doctor h2 {
        margin-top: 0;
        font-size: 16px;
    }

    .doctor p {
        font-size: 14px;
        margin: 5px 0;
    }

    .availability {
        font-size: 14px;
    }

    .available {
        color: green;
    }

    .not-available {
        color: red;
    }

    .doctor button {
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

    .doctor button:hover {
        background-color: #45a049;
    }

    /* CSS for Appointment Section */
    .appointment {
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #4CAF50;
        padding: 10px;
        margin-top: 20px;
    }

    .appointment-details {
        flex: 1;
    }

    .appointment h2 {
        margin-top: 0;
        font-size: 16px;
        color: white;
    }

    .appointment p {
        font-size: 14px;
        margin: 5px 0;
        color: white;
    }

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

    /* Countdown timer */
    .countdown {
        font-size: 18px;
        color: #333;
        margin-top: 20px;
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
                    <span class="link-name">Dahsboard</span>
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
                <li><a href="#">
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
            <a href="updateprofileview.php"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" style="width:42px;height:42px;" ></a>
        </div>
        <div class="dash-content">
            <div class="doctors">
                <h1>Our Doctors</h1>
                <?php
                // Fetch three doctors from the database
                $conn = new mysqli($servername, $db_username, $db_password, $db_name);
                $sql = "SELECT consultant_id, name, specification FROM consultant LIMIT 3";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='doctor'>
                                <img src='images/profile.jpg' alt='Doctor'>
                                <div class='doctor-details'>
                                    <h2>Dr. {$row['name']}</h2>
                                    <p>{$row['specification']}</p>
                                    <p class='availability available'>Available</p>
                                    <button onclick='scheduleAppointment({$row['consultant_id']})'>Schedule Appointment</button>
                                </div>
                              </div>";
                    }
                } else {
                    echo "No doctors found";
                }
                $conn->close();
                ?>
            </div>
            <div id="appointment-form" class="appointment" style="display:none;">
                <div class="appointment-details">
                    <h2>Your Appointment is Ready</h2>
                    <form action="confirm_appointment.php" method="POST">
                        <input type="hidden" id="consultant_id" name="consultant_id" value="">
                        <p>Appointment Date: <input type="date" name="date" required></p>
                        <p>Appointment Time: <input type="time" name="time" required></p>
                        <input type="submit" value="Confirm Appointment">
                    </form>
                </div>
            </div>
            <div class="booking-list">
                <h1>Your Bookings</h1>
                <?php if ($nearest_booking): ?>
                    <div class="booking-item">
                        <div class="booking-details">
                            <h2>Consultant: <?php echo htmlspecialchars($nearest_booking['consultant_name']); ?></h2>
                            <p>Date: <?php echo htmlspecialchars($nearest_booking['date']); ?></p>
                            <p>Time: <?php echo htmlspecialchars($nearest_booking['time']); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No bookings found.</p>
                <?php endif; ?>
            </div>
            <div id="countdown-timer" class="countdown">
                <!-- Countdown timer will be displayed here -->
            </div>
        </div>
    </section>
    <script>
        function scheduleAppointment(consultant_id) {
            document.getElementById('consultant_id').value = consultant_id;
            document.getElementById('appointment-form').style.display = 'block';
        }

        function startCountdown(bookingDate, bookingTime) {
            const countdownElement = document.getElementById('countdown-timer');
            const bookingDateTime = new Date(`${bookingDate}T${bookingTime}`);
            const now = new Date();

            const duration = bookingDateTime - now;

            if (duration <= 0) {
                countdownElement.textContent = "Your booking has passed.";
                return;
            }

            let timer = duration;

            const interval = setInterval(function() {
                const minutes = Math.floor((timer % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timer % (1000 * 60)) / 1000);

                countdownElement.textContent = `Time until your next booking: ${minutes}m ${seconds}s`;

                if (timer <= 0) {
                    clearInterval(interval);
                    countdownElement.textContent = "Your booking is now!";
                } else {
                    timer -= 1000;
                }
            }, 1000);
        }

        <?php if ($nearest_booking): ?>
            const bookingDate = "<?php echo $nearest_booking['date']; ?>";
            const bookingTime = "<?php echo $nearest_booking['time']; ?>";
            startCountdown(bookingDate, bookingTime);
        <?php endif; ?>
    </script>
    <script src="script.js"></script>
</body>
</html>
