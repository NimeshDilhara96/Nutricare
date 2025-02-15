<?php
// Check if form is submitted
include "connect.php";

    // Prepare and bind parameters
    $stmt = $conn->prepare("INSERT INTO weekly_meal_plan (plan_name, plan_details, plan_type, meal_time, meal_preference, nutrition_values, meal_image,day) VALUES (?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssssss", $plan_name, $plan_details, $plan_type, $meal_time, $meal_preference, $nutrition_values, $meal_image,$day);

    // Set parameters
    $plan_name = $_POST['plan_name'];
    $plan_details = $_POST['plan_details'];
    $plan_type = $_POST['plan_type'];
    $meal_time = $_POST['meal_time'];
    $day=$_POST['day'];
    $meal_preference = $_POST['meal_preference'];
    $nutrition_values = $_POST['nutrition_values'];

    // File upload handling
    $target_dir = "uploads/"; // Specify the absolute path to your target directory
    $target_file = $target_dir . basename($_FILES["meal_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["meal_image"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check file size
    if ($_FILES["meal_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["meal_image"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["meal_image"]["name"])). " has been uploaded.";
            // Set meal_image parameter
            $meal_image = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Execute SQL statement
    if ($stmt->execute() === TRUE) {
        echo "<script>
            alert('New record created successfully');
            window.location.href = 'adminCouninsert_weekly_meal_plan2.php';
          </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

?>
