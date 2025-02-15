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

$plan_message = ""; // Initialize the plan message variable this using to get what is user plan

if ($result->num_rows > 0) {
    // Output user information
    while($row = $result->fetch_assoc()) {
        $user_name = $row['username'];
        $user_email = $row['email'];
        $user_fname = $row['fname'];
        $profile_picture = $row['profile_picture']; // Assuming the column name is 'profile_picture'
        // Other conditions and information retrieval as per your existing code
        
        if($row['high_blood_pressure'] == 1 && $row['diabetic'] == 1) {
            $plan_message = "combo plan\n";
            break; // Exit the loop if both conditions are met
        }


        if($row['high_blood_pressure'] == 1) {
            $plan_message = "High blood pressure free plan\n";
            break; // Exit the loop if high blood pressure condition is met
        }
        
        if($row['diabetic'] == 1) {
            $plan_message = "Diabetic free plan\n";
            break; // Exit the loop if diabetic condition is met
        }
        

  
        // You can retrieve other user information here if needed
    }
} else {
    echo "User information not found!";
}


// Define the query health condition and meal time

$query_breakfast = "
SELECT daily_meal_plan.*
FROM user
JOIN daily_meal_plan 
ON ((User.diabetic = 1 AND User.high_blood_pressure = 1 AND daily_meal_plan.plan_type = 'Combo Plan') 
    OR (User.diabetic = 1 AND User.high_blood_pressure = 0 AND daily_meal_plan.plan_type = 'diabetic_free') 
    OR (User.diabetic = 0 AND User.high_blood_pressure = 1 AND daily_meal_plan.plan_type = 'High blood pressure Free'))
WHERE daily_meal_plan.meal_time = 'breakfast'
ORDER BY daily_meal_plan.d_plan_id DESC 
LIMIT 1";// i set by limit beacause i want recent breakfast so i use for ORDER BY daily_meal_plan.d_plan_id DESC LIMIT 1
$result_breakfast = mysqli_query($conn, $query_breakfast);


$query_lunch = "
SELECT daily_meal_plan.*
FROM user
JOIN daily_meal_plan 
ON ((User.diabetic = 1 AND User.high_blood_pressure = 1 AND daily_meal_plan.plan_type = 'Combo Plan') 
    OR (User.diabetic = 1 AND User.high_blood_pressure = 0 AND daily_meal_plan.plan_type = 'diabetic_free') 
    OR (User.diabetic = 0 AND User.high_blood_pressure = 1 AND daily_meal_plan.plan_type = 'High blood pressure Free'))
WHERE daily_meal_plan.meal_time = 'lunch'
ORDER BY daily_meal_plan.d_plan_id DESC 
LIMIT 1";
$result_lunch = mysqli_query($conn, $query_lunch);


$query_dinner = "
SELECT daily_meal_plan.*
FROM user
JOIN daily_meal_plan 
ON ((User.diabetic = 1 AND User.high_blood_pressure = 1 AND daily_meal_plan.plan_type = 'Combo Plan') 
    OR (User.diabetic = 1 AND User.high_blood_pressure = 0 AND daily_meal_plan.plan_type = 'diabetic_free') 
    OR (User.diabetic = 0 AND User.high_blood_pressure = 1 AND daily_meal_plan.plan_type = 'High blood pressure Free'))
WHERE daily_meal_plan.meal_time = 'dinner'
ORDER BY daily_meal_plan.d_plan_id DESC 
LIMIT 1";
$result_dinner = mysqli_query($conn, $query_dinner);





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

    <title>Nutricare</title> 
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .meal-plan {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
        }

        .meal {
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .meal h2 {
            margin-bottom: 10px;
            color: #333;
        }

        .meal p {
            color: #666;
        }

        .highlight {
            background-color: #ffdb4d; /* Yellow background */
            padding: 5px;
            border-radius: 3px;
            animation: imgFloat 7s ease-in-out infinite;
            
        }

        /* Style for the date and time */
        #datetime {
            font-size: 18px;
            color: #333;
            margin-top: 20px;
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

        /* Style for BMI tool */
        .bmi-tool {
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin-top: 20px;
        }

        .bmi-tool h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .bmi-tool label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .bmi-tool input[type="range"] {
            width: 100%;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .bmi-tool button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .bmi-result {
            margin-top: 10px;
            color: #333;
        }

        /* Style for displaying range slider values */
        .slider-value {
            margin-bottom: 5px;
            color: #666;
        }
         /* loading screen */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        .loading-image {
            max-width: 200px; /* Adjust image width as needed */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .hidden {
          display: none;
         }
        
    </style>
</head>
<body>

<!-- Loading screen -->
<div class="loading-overlay">
<!-- Image -->
<img src="images/MommentX.gif" alt="Loading Image" class="loading-image">
</div>

<div id="allcontenhidden"> <!-- hide all content -->
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
                <li><a href="shoping.html">
                    <i class="uil uil-thumbs-up"></i>
                    <span class="link-name">Shopping list</span>
                </a></li>
                <li><a href="#">
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
            <a href="notification.php"><h1>Notification</h1></a>
            <h1>Welcome,<?php echo $user_fname; ?></h1>
            
            <a href="updateprofileview.php"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" style="width:42px;height:42px;" ></a>
        </div>

        <div class="dash-content">
            <div class="meal-plan">
                <!-- Meal plan on the left -->
                <div class="meal">
                    <h2><span class="highlight"><?php  echo $plan_message ;?></span></h2>
                    <p>Enjoy delicious meals suitable for individuals with diabetes.</p>
                </div>
              
                <!-- Date and time on the right -->
                <div id="datetime"></div>
            </div>
            <h1>Today Your Meal Plan   <?php echo $user_fname; ?></h1>
            
            <!-- Daily Meal Plan section -->
            <div class="mcontainer">
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result_breakfast)): ?>
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

<!-- BMI Tool -->
<div class="bmi-tool">
    <h3>BMI Calculator</h3>
    <label for="weight">Weight (kg):</label>
    <input type="range" id="weight" min="0" max="150" value="0" oninput="updateWeight()">
    <div class="slider-value" id="weightValue">Weight: 0 kg</div>
    <label for="age">Age:</label>
    <input type="range" id="age" min="0" max="120" value="0" oninput="updateAge()">
    <div class="slider-value" id="ageValue">Age: 0</div>
    <button onclick="calculateBMI()">Calculate BMI</button>
    <div class="bmi-result" id="bmiResult"></div>
</div>




        </div><!-- hide all content end-->
<script>
    // Function to calculate BMI
    function calculateBMI() {
        var weight = document.getElementById('weight').value;
        var age = document.getElementById('age').value;
        var bmi = weight / ((age / 100) * (age / 100));
        var bmiResult = "Your BMI is: " + bmi.toFixed(2);
        document.getElementById('bmiResult').textContent = bmiResult;
    }

    // Function to update weight value
    function updateWeight() {
        var weightValue = document.getElementById('weight').value;
        document.getElementById('weightValue').textContent = "Weight: " + weightValue + " kg";
    }

    // Function to update age value
    function updateAge() {
        var ageValue = document.getElementById('age').value;
        document.getElementById('ageValue').textContent = "Age: " + ageValue;
    }

    // Function to update date and time
    function updateDateTime() {
        var today = new Date();
        var date = today.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        var time = today.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true });
        var dateTimeString = date + ', ' + time;
        document.getElementById('datetime').textContent = dateTimeString;
    }

    // Update date and time initially and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>



<script>
    // Function to hide the loading screen
    function hideLoadingScreen() {
        var loadingOverlay = document.querySelector('.loading-overlay');
        loadingOverlay.style.display = 'none';
    }

    // Hide the loading screen after 20 seconds
    setTimeout(hideLoadingScreen, 5000);
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var contentDiv = document.getElementById('allcontenhidden');
    contentDiv.classList.add('hidden'); // Initially hide the content
    
    setTimeout(function() {
      contentDiv.classList.remove('hidden'); // Show the content after 1 minute
    }, 2000); // 60000 milliseconds = 1 minute
  });
</script>

            
<script src="script.js"></script>
</body>
</html>