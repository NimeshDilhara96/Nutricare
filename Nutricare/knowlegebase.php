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
        // You can retrieve other user information here if needed
        $profile_picture = $row['profile_picture']; // Assuming the column name is 'profile_picture'
        // Other conditions and information retrieval as per your existing code
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

    <title>Admin Dashboard Panel</title> 
    <style>
  body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        .container {
            width: 80%;
            margin: 2rem auto;
            padding: 1rem;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 0.5rem;
        }
        section {
            margin: 2rem 0;
        }
        .articles, .videos {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .article, .video {
            flex: 1 1 calc(33% - 2rem);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            background: white;
            border-radius: 5px;
        }
        .article img, .video iframe {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .article h3, .video h3 {
            font-size: 1.2rem;
            margin: 1rem 0;
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
            <h1><?php echo $user_fname . ' ' . $user_lname; ?></h1>
            
            <a href="updateprofileview.php"><img src="<?php echo $profile_picture; ?>" alt="Profile Picture" style="width:42px;height:42px;" ></a>
        </div>

        <div class="dash-content">
        <div class="container">
        <section>
            <h2>Articles</h2>
            <div class="articles">
                <div class="article">
                    <img src="images/healthy diet for high blood pressure.jpg" alt="Healthy Diet">
                    <h3>Healthy Diet for High Blood Pressure</h3>
                    <p>A balanced diet is crucial for managing high blood pressure. Learn which foods to include in your meals.</p>
                </div>
                <div class="article">
                    <img src="images/diabetes meal plan.jpg" alt="Diabetes Meal Plan">
                    <h3>Diabetes Meal Plan</h3>
                    <p>Discover the best meal plans that help manage diabetes effectively and keep your blood sugar levels in check.</p>
                </div>
                <div class="article">
                    <img src="images/Weight Loss Diet.jpeg" alt="Weight Loss Diet">
                    <h3>Weight Loss Diet</h3>
                    <p>Find out the most effective diet plans for weight loss and maintaining a healthy lifestyle.</p>
                </div>
            </div>
        </section>
        <section>
            <h2>Videos</h2>
            <div class="videos">
                <div class="video">
                    <iframe width="100%" height="200" src="https://www.youtube.com/embed/Dl1vMBCBjJE?si=MtV2Ks4-ICEjeDW_" frameborder="0" allowfullscreen></iframe>
                    <h3>Nutrition Tips for High Blood Pressure</h3>
                </div>
                <div class="video">
                    <iframe width="100%" height="200" src="https://www.youtube.com/embed/wOIZEz0hAY4?si=D_pv9JNwJvLCmwaz" frameborder="0" allowfullscreen></iframe>
                    <h3>Managing Diabetes with Diet</h3>
                </div>
                <div class="video">
                    <iframe width="100%" height="200" src="https://www.youtube.com/embed/_nR1juKxIRM?si=sq_UHXvNIxW_y0ai" frameborder="0" allowfullscreen></iframe>
                    <h3>Effective Weight Loss Strategies</h3>
                </div>
            </div>
        </section>
    </div>
</body>
</html>

    <script src="script.js"></script>
</body>
</html>