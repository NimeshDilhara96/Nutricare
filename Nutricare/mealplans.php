<?php
session_start(); // Start the session to access session variables

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location:login.html");
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
    while($row = $result->fetch_assoc()) {
        $user_name = $row['username'];
        $user_email = $row['email'];
        $user_fname = $row['fname'];
        $user_lname = $row['lname'];
        $diabetic = $row['diabetic'];
        $high_blood_pressure = $row['high_blood_pressure'];
        $profile_picture = $row['profile_picture'];
    }
} else {
    echo "User information not found!";
    exit();
}

// Define the queries for health condition and meal time

$query_breakfast = "
SELECT daily_meal_plan.*
FROM daily_meal_plan 
WHERE (
        (daily_meal_plan.plan_type = 'Combo Plan' AND $diabetic = 1 AND $high_blood_pressure = 1) OR
        (daily_meal_plan.plan_type = 'diabetic_free' AND $diabetic = 1 AND $high_blood_pressure = 0) OR
        (daily_meal_plan.plan_type = 'High blood pressure Free' AND $diabetic = 0 AND $high_blood_pressure = 1)
      )
AND daily_meal_plan.meal_time = 'breakfast'
ORDER BY daily_meal_plan.d_plan_id DESC 
LIMIT 1";
$result_breakfast = $conn->query($query_breakfast);

$query_lunch = "
SELECT daily_meal_plan.*
FROM daily_meal_plan 
WHERE (
        (daily_meal_plan.plan_type = 'Combo Plan' AND $diabetic = 1 AND $high_blood_pressure = 1) OR
        (daily_meal_plan.plan_type = 'diabetic_free' AND $diabetic = 1 AND $high_blood_pressure = 0) OR
        (daily_meal_plan.plan_type = 'High blood pressure Free' AND $diabetic = 0 AND $high_blood_pressure = 1)
      )
AND daily_meal_plan.meal_time = 'lunch'
ORDER BY daily_meal_plan.d_plan_id DESC 
LIMIT 1";
$result_lunch = $conn->query($query_lunch);

$query_dinner = "
SELECT daily_meal_plan.*
FROM daily_meal_plan 
WHERE (
        (daily_meal_plan.plan_type = 'Combo Plan' AND $diabetic = 1 AND $high_blood_pressure = 1) OR
        (daily_meal_plan.plan_type = 'diabetic_free' AND $diabetic = 1 AND $high_blood_pressure = 0) OR
        (daily_meal_plan.plan_type = 'High blood pressure Free' AND $diabetic = 0 AND $high_blood_pressure = 1)
      )
AND daily_meal_plan.meal_time = 'dinner'
ORDER BY daily_meal_plan.d_plan_id DESC 
LIMIT 1";
$result_dinner = $conn->query($query_dinner);

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
    <link rel="stylesheet" href="style.css">
     
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <title>Admin Dashboard Panel</title> 
    <style>
 body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    margin: 0;
    padding: 0;
}

.mcontainer {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    max-width: 500px;
    margin-left: 10px;
    margin-right: auto;
    padding: 20px;
}

.col-md-3 {
    flex: 0 0 31%;
    margin-bottom: 20px;
}

.meal-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.meal-card:hover {
    transform: translateY(-5px);
}

.meal-card img {
    width: 250px;
    height: 200px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    animation: imgFloat 7s ease-in-out infinite;
}
@keyframes imgFloat {
    50%{
        transform: translateY(10px);
        border-radius: 45% 55% 45% 55%;
    }
}

.meal-card-content {
    padding: 15px;
}

.meal-time, .meal-name, .meal-details, .nutrition-values {
    margin-bottom: 10px;
}

.meal-time {
    font-weight: bold;
    color: #ff6347;
}

.meal-name {
    font-size: 1.5em;
    margin-bottom: 5px;
}

.meal-details {
    color: #555;
}

.nutrition-values {
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 0 0 10px 10px;
    font-size: 0.9em;
    color: #333;
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
    <a href="link1.php" class="btn btn-primary">Daily</a>
    <a href="wmealplans.php" class="btn btn-secondary">Weekly</a>
    <br>
    <br>
        <?php while($row = mysqli_fetch_assoc($result_breakfast)): ?>
        <div class="col-md-3">
            <div class="meal-card">
                <img src="<?php echo $row['meal_image']; ?>" alt="Meal">
                <div class="meal-card-content">
                    <div class="meal-time"><?php echo $row['meal_time']; ?></div>
                    <div class="meal-name"><?php echo $row['plan_name']; ?></div>
                    <div class="meal-details"><?php echo $row['plan_details']; ?></div>
                    <div class="nutrition-values">
                        <p><strong>Nutrition Values:</strong> <?php echo $row['nutrition_values']; ?></p>
                        <p>Protein: 8g</p>
                        <p>Carbs: 45g</p>
                        <p>Fat: 15g</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php while($row = mysqli_fetch_assoc($result_lunch)): ?>
        <div class="col-md-3">
            <div class="meal-card">
                <img src="<?php echo $row['meal_image']; ?>" alt="Meal">
                <div class="meal-card-content">
                    <div class="meal-time"><?php echo $row['meal_time']; ?></div>
                    <div class="meal-name"><?php echo $row['plan_name']; ?></div>
                    <div class="meal-details"><?php echo $row['plan_details']; ?></div>
                    <div class="nutrition-values">
                        <p>Calories: 350</p>
                        <p>Protein: 8g</p>
                        <p>Carbs: 45g</p>
                        <p>Fat: 15g</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php while($row = mysqli_fetch_assoc($result_dinner)): ?>
        <div class="col-md-3">
            <div class="meal-card">
                <img src="<?php echo $row['meal_image']; ?>" alt="Meal">
                <div class="meal-card-content">
                    <div class="meal-time"><?php echo $row['meal_time']; ?></div>
                    <div class="meal-name"><?php echo $row['plan_name']; ?></div>
                    <div class="meal-details"><?php echo $row['plan_details']; ?></div>
                    <div class="nutrition-values">
                        <p>Calories: 350</p>
                        <p>Protein: 8g</p>
                        <p>Carbs: 45g</p>
                        <p>Fat: 15g</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
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