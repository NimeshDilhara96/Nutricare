<?php
session_start(); // Start the session to access session variables

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: admin/adminlogin.html");
    exit();
}

// Database connection parameters
$servername = "localhost";
$db_username = "root"; // Change this to your database username
$db_password = ""; // Change this to your database password
$db_name = "Nutricare";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$popupMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];

    // Retrieve all user IDs
    $sql = "SELECT user_id FROM user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("INSERT INTO Notifications (user_id, message) VALUES (?, ?)");

        while ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            $stmt->bind_param("is", $user_id, $message);
            $stmt->execute();
        }

        $popupMessage = "Notification added for all users successfully!";
        $stmt->close();
    } else {
        $popupMessage = "No users found!";
    }
}

// Retrieve admin information from database
$username = $_SESSION['username'];
$sql = "SELECT * FROM admin WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output admin information
    while($row = $result->fetch_assoc()) {
        $admin_name = $row['username'];
        // You can retrieve other admin information here if needed
    }
} else {
    echo "Admin information not found!";
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
    <link rel="stylesheet" href="admin/style.css">
    
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Admin Dashboard Panel - Notifications</title> 
    <style>
        /* Add your CSS styles here */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .popup.show {
            display: block;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }
        .popup-overlay.show {
            display: block;
        }
        .popup-close {
            cursor: pointer;
            font-weight: bold;
            float: right;
            font-size: 20px;
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
                <li><a href="admindashboard.php">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Dashboard</span>
                </a></li>
                
              
                <li><a href="newadmin.php">
                    <i class="uil uil-user-plus"></i>
                    <span class="link-name">New Admin</span>
                </a></li>
                <li><a href="adminfeedbackviewer.php">
                    <i class="uil uil-share"></i>
                    <span class="link-name">User feedback</span>
                </a></li>
            </ul>
            <li><a href="adminnotification.php">
                    <i class="uil uil-share"></i>
                    <span class="link-name">Notification</span>
                </a></li>
            </ul>
            <li><a href="adminadd_doctor2.php">
                <i class="uil uil-user-plus"></i>
                <span class="link-name">Add Doctor</span>
            </a></li>
            
            <ul class="logout-mode">
                <li><a href="admin/logout.php">
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
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?></h1>
            <img src="images/profile.jpg" alt="Profile Image">
        </div>
        <div class="dash-content">
            <h2>Add Notification</h2>
            <form method="post" action="">
                <label for="message">Notification Message:</label>
                <input type="text" id="message" name="message" required>
                <button type="submit" onclick="showPopup()">Add Notification</button>
            </form>

            <!-- Popup HTML -->
            <div id="popup" class="popup">
                <span class="popup-close" onclick="hidePopup()">&times;</span>
                <p id="popupMessage"><?php echo htmlspecialchars($popupMessage); ?></p>
            </div>
            <div id="popupOverlay" class="popup-overlay" onclick="hidePopup()"></div>
        </div>
    </section>
    <script>
        function showPopup() {
            var popup = document.getElementById('popup');
            var overlay = document.getElementById('popupOverlay');
            var popupMessage = document.getElementById('popupMessage');
            popupMessage.textContent = '<?php echo addslashes($popupMessage); ?>';
            popup.classList.add('show');
            overlay.classList.add('show');
            return false; // Prevent form submission to display the popup
        }

        function hidePopup() {
            var popup = document.getElementById('popup');
            var overlay = document.getElementById('popupOverlay');
            popup.classList.remove('show');
            overlay.classList.remove('show');
        }
    </script>
    <script src="script.js"></script>
</body>
</html>
