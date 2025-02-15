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

// Retrieve user information from database
$email = $_SESSION['email'];
$sql = "SELECT * FROM user WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output user information
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id']; // Assuming 'user_id' is the primary key of the 'user' table
        $user_name = $row['username'];
        $user_email = $row['email'];
        $user_fname = $row['fname'];
        $profile_picture = $row['profile_picture']; // Assuming the column name is 'profile_picture'
    }
} else {
    echo "User information not found!";
    exit();
}

// Query to retrieve diet plan information
$diet_plan_sql = "SELECT * FROM diet_plan";
$diet_plan_result = $conn->query($diet_plan_sql);

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
    <link rel="stylesheet" href="dietplan.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Admin Dashboard Panel</title>
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
            <h1>Welcome, <?php echo $user_fname; ?></h1>
            <a href="updateprofileview.php"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" style="width:42px;height:42px;"></a>
        </div>
        <div class="dash-content">
            <section id="diet-plan-table">
                <h2>Diet Plan Foods For 7 Days</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Days</th>
                            <th>Examples</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($diet_plan_result->num_rows > 0) {
                            while ($row = $diet_plan_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['day_of_week'] . "</td>";
                                echo "<td>" . $row['examples'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No diet plan found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <section id="guidelines">
                <h2>Dietary Guidelines</h2>
                <ul>
                    <li>Limit sodium intake to help control blood pressure.</li>
                    <li>Include plenty of fruits and vegetables in your diet.</li>
                    <li>Choose whole grains over refined grains.</li>
                    <li>Opt for lean protein sources like fish, poultry, and legumes.</li>
                    <li>Avoid sugary foods and beverages.</li>
                    <li>Monitor your carbohydrate intake to manage blood sugar levels.</li>
                </ul>
            </section>
            <section id="print-buttons">
    <h2>Do you need diet plan</h2>
    <a href="dowloaddiet.php" class="btn" style="width:100%"><i class="fa fa-download"></i> Download</a>
</section>
        </div>
    </section>
    <script src="script.js"></script>
</body>
</html>
