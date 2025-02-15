<?php
// Check if a session is not already started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "connect.php"; // Include the database connection file

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Retrieve user email from session
$email = $_SESSION['email'];

// Retrieve user information from the database
$sql = "SELECT * FROM user WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found!";
    exit();
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $diabetic = isset($_POST['diabetic']) ? 1 : 0;
    $high_blood_pressure = isset($_POST['high_blood_pressure']) ? 1 : 0;
    $meal_plan_type = $_POST['meal_plan_type'];
    $profile_picture = $user['profile_picture']; // Default to current profile picture

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Prepare SQL statement
    $sql = "UPDATE user SET 
            fname='$fname', 
            lname='$lname', 
            age=$age, 
            gender='$gender', 
            phonenumber='$phone', 
            diabetic=$diabetic, 
            high_blood_pressure=$high_blood_pressure, 
            meal_plan_type='$meal_plan_type',
            profile_picture='$profile_picture' 
            WHERE email='$email'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Update successful, set success message in session and redirect
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: updateprofileview.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <style>
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #f0f0f0;
            background-image: url('<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'placeholder.png'; ?>');
            background-size: cover;
            background-position: center;
            margin: 0 auto 10px;
        }
        .edit-profile-picture {
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            cursor: pointer;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-group label {
            flex: 1;
            margin-right: 10px;
            text-align: right;
        }
        .form-group input, .form-group select, .form-group button {
            flex: 2;
            padding: 8px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .checkbox-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            align-self: flex-end;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        window.onload = function() {
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "alert('" . $_SESSION['success_message'] . "');";
                unset($_SESSION['success_message']); // Clear the message after showing it
            }
            ?>

            // Function to display selected file name
            document.getElementById('profile_picture').addEventListener('change', function() {
                var fileName = document.getElementById('profile_picture').value.split('\\').pop();
                document.getElementById('file-name').textContent = fileName;
            });
        };

        function showEditProfilePicture() {
            document.getElementById('profile_picture').click();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center;">Update Profile</h1>
        <div class="profile-picture"></div>
        <div class="edit-profile-picture" onclick="showEditProfilePicture()">Edit Profile Picture</div>
        <form method="POST" action="update_profile.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_picture"></label>
                <input type="file" id="profile_picture" name="profile_picture" style="display: none;">
                <span id="file-name" style="margin-left: 5px;"></span>
                </div>
            <div class="form-group">
                <label for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" value="<?php echo $user['fname']; ?>" required>
            </div>

            <div class="form-group">
                <label for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" value="<?php echo $user['lname']; ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo $user['age']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo $user['phonenumber']; ?>" required>
            </div>

            <div class="checkbox-group">
                <label for="diabetic">Diabetic:</label>
                <input type="checkbox" id="diabetic" name="diabetic" <?php if ($user['diabetic']) echo 'checked'; ?>>

                <label for="high_blood_pressure">High Blood Pressure:</label>
                <input type="checkbox" id="high_blood_pressure" name="high_blood_pressure" <?php if ($user['high_blood_pressure']) echo 'checked'; ?>>
            </div>

            <div class="form-group">
                <label for="meal_plan_type">Meal Plan Type:</label>
                <input type="text" id="meal_plan_type" name="meal_plan_type" value="<?php echo $user['meal_plan_type']; ?>" required>
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
