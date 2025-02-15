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
$sql_user = "SELECT * FROM User WHERE email='$email'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    // Output user information
    while($row = $result_user->fetch_assoc()) {
        $user_name = $row['username'];
        $user_email = $row['email'];
        $user_fname = $row['fname'];
        $user_lname = $row['lname'];
        $profile_picture = $row['profile_picture']; // Assuming the column name is 'profile_picture'
        $high_blood_pressure = $row['high_blood_pressure'];
        $diabetic = $row['diabetic'];
    }
} else {
    echo "User information not found!";
}

// Determine the meal plan filter based on user's health conditions
$plan_filter = '';

if ($high_blood_pressure && $diabetic) {
    $plan_filter = " AND plan_type='Combo Plan'";
} elseif ($high_blood_pressure) {
    $plan_filter = " AND plan_type='High Blood Pressure Free'";
} elseif ($diabetic) {
    $plan_filter = " AND plan_type='Diabetes Free'";
}

// Query to retrieve weekly meal plans based on user's health conditions
$sql_meal_plans = "SELECT * FROM weekly_meal_plan WHERE 1=1" . $plan_filter . " ORDER BY day, meal_time";
$result_meal_plans = $conn->query($sql_meal_plans);

// Prepare data for each day
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$meal_plans_by_day = [];

while ($row = $result_meal_plans->fetch_assoc()) {
    $day = $row['day'];
    $meal_time = $row['meal_time'];
    if (!isset($meal_plans_by_day[$day])) {
        $meal_plans_by_day[$day] = ['breakfast' => [], 'lunch' => [], 'dinner' => []];
    }
    $meal_plans_by_day[$day][$meal_time][] = $row;
}

?>






























<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="style.css">
     
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <title>Admin Dashboard Panel</title> 
    <style>
 body {
            font-family: Arial, sans-serif;
        }
        .container {
        width: 80%;
        margin: auto;
    }
    .day-box {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin: 20px auto; /* Center the box */
        padding: 10px;
        background-color: #f9f9f9;
        width: 60%; /* Set width to 60% of the container */
        max-width: 600px; /* Max width for larger screens */
    }
    .day-title {
        font-size: 24px;
        margin-bottom: 10px;
    }
    .meal-time {
        font-size: 20px;
        margin-top: 10px;
        margin-bottom: 5px;
        color: #333;
    }
    .meal-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #fff;
    }
    .meal-item img {
        width: 100px;
        height: auto;
        border-radius: 4px;
        float: left;
        margin-right: 10px;
    }
/* Mobile view adjustments */
@media (max-width: 768px) {
    .mcontainer {
        flex-direction: column;
        margin-left: auto;
        margin-right: auto;
        padding: 10px;
    }

    .col-md-3 {
        flex: 0 0 100%;
        margin-bottom: 20px;
    }

    .meal-name {
        font-size: 1.2em;
    }

    .nutrition-values {
        font-size: 0.8em;
    }
    .meal-details{
        font-size: 0.9em;  
    }
}

.feedback-bar {
    margin-top: 20px;
    padding: 10px 0;
    background-color: #f2f2f2;
    border-top: 1px solid #ddd;
    display: flex;
    border-radius: 35px; 
    justify-content: center;
}

#feedback-input {
    padding: 8px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 35px;
    margin-right: 10px;
}

#feedback-input::placeholder {
    color: #999;
}

#feedback-input:focus {
    outline: none;
    border-color: #4caf50;
}

button {
    padding: 8px 20px;
    font-size: 14px;
    border: none;
    background-color: #4caf50;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #45a049;
}
.advertisement {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 1025px; /* Set width to 300px */
        height: 50px; /* Set height to 250px */
        background-color: #f2f2f2; /* Optional: Add a background color */
        border-radius: 10px; /* Optional: Add border-radius for rounded corners */
    }

    .advertisement img {
        max-width: 100%;
        max-height: 100%; /* Ensure the image doesn't exceed the container */
        width: auto;
        height: auto;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        margin-right: 10px;
    }

    .advertisement p {
        color: #333;
        font-size: 16px;
        margin: 0; /* Remove default margin */
    }
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
            <h1><?php echo $user_fname . ' ' . $user_lname; ?></h1>
            
            <a href="updateprofileview.php"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" style="width:42px;height:42px;" ></a>
        </div>

        <div class="dash-content">
        <div class="mcontainer">
        <div class="button-container">
        
    </div>
    <div class="row">
    <div class="container">
        <br>
    <a href="mealplans.php" class="btn btn-secondary">Daily</a>
    <a href="wmealplans.php" class="btn btn-primary">Weekly</a>
        <?php foreach ($days as $day): ?>
            <div class="day-box">
                <div class="day-title"><?php echo $day; ?></div>
                <?php foreach (['breakfast', 'lunch', 'dinner'] as $meal_time): ?>
                    <?php if (!empty($meal_plans_by_day[$day][$meal_time])): ?>
                        <div class="meal-time"><?php echo ucfirst($meal_time); ?></div>
                        <?php foreach ($meal_plans_by_day[$day][$meal_time] as $meal): ?>
                            <div class="meal-item">
                                <img src="<?php echo $meal['meal_image']; ?>" alt="Meal Image">
                                <h3><?php echo $meal['plan_name']; ?></h3>
                                <p><strong></strong> <?php echo $meal['plan_details']; ?></p>
                                <p><strong></strong> <?php echo $meal['plan_type']; ?></p>
                                <p><strong></strong> <?php echo $meal['meal_preference']; ?></p>
                                <p><strong>Nutrition Values:</strong> <?php echo json_encode($meal['nutrition_values']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

        


    <div class="feedback-bar">
    <input type="text" id="feedback-input" placeholder="Enter your feedback...">
    <button onclick="sendFeedback()">Send</button>
</div>
<div class="advertisement">
    <img src="images/burger.png" alt="Advertisement">
    <p>Powered by Nutricare X MommentX</p>
</div>

<script>
    function sendFeedback() {
        var feedbackText = document.getElementById('feedback-input').value;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "feedback.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText);
            }
        };

        var params = "feedback=" + encodeURIComponent(feedbackText);
        xhr.send(params);
    }
</script>
    <script src="script.js"></script>
</body>
</html>