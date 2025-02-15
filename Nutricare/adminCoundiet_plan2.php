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
        $consultant_name = $row['username'];
        // You can retrieve other admin information here if needed
    }
} else {
    echo "Admin information not found!";
}

// Retrieve diet plans from the database
$diet_plans_sql = "SELECT * FROM diet_plan";
$diet_plans_result = $conn->query($diet_plans_sql);

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
        /* Add your CSS styles here */
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
            <div class="container">
                <h2>Add Diet Plan</h2>
                <form action="adminCoundiet_plan1.php" method="POST">
                    <label for="day_of_week">Day of Week:</label>
                    <select id="day_of_week" name="day_of_week" required>
                        <option value="">Select Day</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                    <br><br>
                    <label for="examples">Examples:</label>
                    <textarea id="examples" name="examples" rows="4" required></textarea>
                    <br><br>
                    <input type="hidden" name="action" value="insert"> <!-- Default action -->
                    <input type="submit" value="Insert Or Update">
                </form>
                <br>
                           <!-- Display Diet Plans -->
                <h2>Existing Diet Plans</h2>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Day of Week</th>
                            <th>Examples</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($diet_plans_result->num_rows > 0): ?>
                            <?php while ($row = $diet_plans_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['day_of_week']); ?></td>
                                    <td><?php echo htmlspecialchars($row['examples']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No diet plans found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>
