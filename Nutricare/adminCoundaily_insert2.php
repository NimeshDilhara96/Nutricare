
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
        $consultant_name = $row['name'];
        
        // You can retrieve other admin information here if needed
    }
} else {
    echo "Coun information not found!";
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
    
    <!-- CSS -->
    <link rel="stylesheet" href="admin/style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Admin Dashboard Panel - Diet Plans</title>
    <style>
        .btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
    cursor: pointer;
}

/* Primary button styles */
.btn-primary {
    background-color: #4caf50; /* Green background */
    color: #fff; /* White text */
    border: 1px solid #4caf50; /* Border matches background color */
}

.btn-primary:hover {
    background-color: #45a049; /* Darker green background on hover */
    border-color: #45a049; /* Border color matches background color */
}

/* Secondary button styles */
.btn-secondary {
    background-color: #f1f1f1; /* Light gray background */
    color: #333; /* Dark text */
    border: 1px solid #ddd; /* Light gray border */
}

.btn-secondary:hover {
    background-color: #ddd; /* Slightly darker gray background on hover */
    border-color: #ccc; /* Darker gray border on hover */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn {
        font-size: 14px;
        padding: 8px 16px;
    }
}
/* Add more styles as needed */
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
            <h1>Welcome, <?php echo $consultant_name; ?></h1>
            <img src="images/profile.jpg" alt="Profile Image">
        </div>
        <div class="dash-content">
        <br>
    <a href="adminCoundaily_insert2.php" class="btn btn-primary">Daily</a>
    <a href="adminCouninsert_weekly_meal_plan2.php" class="btn btn-secondary">Weekly</a>
  <h2>Insert Daily Meal Plan</h2>
  <form action="adminCoundaily_insert.php" method="post" enctype="multipart/form-data">
    <label for="plan_name">Plan Name:</label><br>
    <input type="text" id="plan_name" name="plan_name" required><br><br>
    
    <label for="plan_details">Plan Details:</label><br>
    <textarea id="plan_details" name="plan_details" rows="4" required></textarea><br><br>
    
    <label for="meal_image">Meal Image:</label><br>
    <input type="file" id="meal_image" name="meal_image" accept="image/*" required><br><br>
    
    <label for="plan_type">Plan Type:</label><br>
    <input type="radio" id="diabetic_free" name="plan_type" value="diabetic_free" required>
    <label for="diabetic_free">diabetic_free</label>
    <input type="radio" id="High blood pressure Free" name="plan_type" value="High blood pressure Free" required>
    <label for="High blood pressure Free">High blood pressure Free</label>
    <input type="radio" id="Combo Plan" name="plan_type" value="Combo Plan" required>
    <label for="Combo Plan">Combo Plan</label><br><br>
    
    
    <label for="meal_time">Meal Time:</label><br>
    <select id="meal_time" name="meal_time" required>
      <option value="breakfast">Breakfast</option>
      <option value="lunch">Lunch</option>
      <option value="dinner">Dinner</option>
    </select><br><br>
    
    <label for="meal_preference">Meal Preference:</label><br>
    <input type="radio" id="vegan" name="meal_preference" value="vegan" required>
    <label for="vegan">Vegan</label>
    <input type="radio" id="nonvegan" name="meal_preference" value="nonvegan" required>
    <label for="nonvegan">Non-vegan</label><br><br>
    
    <label for="nutrition_values">Nutrition Values (Calories | Carbs | Protein | Fat):</label><br>
    <input type="text" id="nutrition_values" name="nutrition_values" placeholder="300 | 25g | 15g | 12g" required><br><br>
    
    <input type="submit" value="Submit">
  </form>
  </div>
  <script src="script.js"></script>
</body>
</html>
