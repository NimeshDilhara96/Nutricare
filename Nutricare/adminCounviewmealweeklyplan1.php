<?php
include "connect.php";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Adding new record to weekly_meal_plan
        $stmt = $conn->prepare("INSERT INTO weekly_meal_plan (plan_name, plan_details, plan_type, meal_time, meal_preference, nutrition_values, meal_image, day) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $plan_name, $plan_details, $plan_type, $meal_time, $meal_preference, $nutrition_values, $meal_image, $day);

        // Set parameters
        $plan_name = $_POST['plan_name'];
        $plan_details = $_POST['plan_details'];
        $plan_type = $_POST['plan_type'];
        $meal_time = $_POST['meal_time'];
        $meal_preference = $_POST['meal_preference'];
        $nutrition_values = $_POST['nutrition_values'];
        $day = $_POST['day'];

        // File upload handling
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["meal_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["meal_image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }

        if ($_FILES["meal_image"]["size"] > 500000) {
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["meal_image"]["tmp_name"], $target_file)) {
                $meal_image = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        // Updating record in weekly_meal_plan
        $stmt = $conn->prepare("UPDATE weekly_meal_plan SET plan_name=?, plan_details=?, plan_type=?, meal_time=?, meal_preference=?, nutrition_values=?, meal_image=?, day=? WHERE w_plan_id=?");
        $stmt->bind_param("ssssssssi", $plan_name, $plan_details, $plan_type, $meal_time, $meal_preference, $nutrition_values, $meal_image, $day, $w_plan_id);

        // Set parameters
        $w_plan_id = $_POST['w_plan_id'];
        $plan_name = $_POST['plan_name'];
        $plan_details = $_POST['plan_details'];
        $plan_type = $_POST['plan_type'];
        $meal_time = $_POST['meal_time'];
        $meal_preference = $_POST['meal_preference'];
        $nutrition_values = $_POST['nutrition_values'];
        $day = $_POST['day'];

        // File upload handling
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["meal_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["meal_image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }

        if ($_FILES["meal_image"]["size"] > 500000) {
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["meal_image"]["tmp_name"], $target_file)) {
                $meal_image = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        // Deleting record from weekly_meal_plan
        $w_plan_id = $_POST['w_plan_id'];
        $stmt = $conn->prepare("DELETE FROM weekly_meal_plan WHERE w_plan_id=?");
        $stmt->bind_param("i", $w_plan_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch record for update
$update_data = [];
if (isset($_GET['update_id'])) {
    $w_plan_id = $_GET['update_id'];
    $stmt = $conn->prepare("SELECT * FROM weekly_meal_plan WHERE w_plan_id=?");
    $stmt->bind_param("i", $w_plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $update_data = $result->fetch_assoc();
    $stmt->close();
}

// Fetch records for display
$result = $conn->query("SELECT * FROM weekly_meal_plan");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Meal Plan Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .modal {
            display: <?php echo isset($_GET['update_id']) ? 'block' : 'none'; ?>;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Weekly Meal Plan Management</h2>



<h3>Existing Weekly Meal Plans</h3>
<table>
    <tr>
        <th>Plan Name</th>
        <th>Plan Details</th>
        <th>Meal Image</th>
        <th>Plan Type</th>
        <th>Day</th>
        <th>Meal Time</th>
        <th>Meal Preference</th>
        <th>Nutrition Values</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
        <td><?php echo htmlspecialchars($row['plan_details']); ?></td>
        <td><img src="<?php echo htmlspecialchars($row['meal_image']); ?>" width="100" /></td>
        <td><?php echo htmlspecialchars($row['plan_type']); ?></td>
        <td><?php echo htmlspecialchars($row['day']); ?></td>
        <td><?php echo htmlspecialchars($row['meal_time']); ?></td>
        <td><?php echo htmlspecialchars($row['meal_preference']); ?></td>
        <td><?php echo htmlspecialchars($row['nutrition_values']); ?></td>
        <td>
            <a href="?update_id=<?php echo $row['w_plan_id']; ?>">Update</a>
            <form action="" method="post" style="display:inline;">
                <input type="hidden" name="w_plan_id" value="<?php echo $row['w_plan_id']; ?>">
                <input type="submit" name="delete" value="Delete">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Update Form Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('updateModal').style.display='none'">&times;</span>
        <h3>Update Meal Plan</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="w_plan_id" value="<?php echo htmlspecialchars($update_data['w_plan_id'] ?? ''); ?>">
            <label for="update_plan_name">Plan Name:</label>
            <input type="text" id="update_plan_name" name="plan_name" value="<?php echo htmlspecialchars($update_data['plan_name'] ?? ''); ?>" required><br>
            <label for="update_plan_details">Plan Details:</label>
            <textarea id="update_plan_details" name="plan_details" required><?php echo htmlspecialchars($update_data['plan_details'] ?? ''); ?></textarea><br>
            <label for="update_plan_type">Plan Type:</label>
            <select id="update_plan_type" name="plan_type">
                <option value="diabetic_free" <?php echo isset($update_data['plan_type']) && $update_data['plan_type'] == 'diabetic_free' ? 'selected' : ''; ?>>Diabetic Free</option>
                <option value="High blood pressure Free" <?php echo isset($update_data['plan_type']) && $update_data['plan_type'] == 'High blood pressure Free' ? 'selected' : ''; ?>>High Blood Pressure Free</option>
                <option value="Combo Plan" <?php echo isset($update_data['plan_type']) && $update_data['plan_type'] == 'Combo Plan' ? 'selected' : ''; ?>>Combo Plan</option>
            </select><br>
            <label for="update_meal_time">Meal Time:</label>
            <select id="update_meal_time" name="meal_time">
                <option value="breakfast" <?php echo isset($update_data['meal_time']) && $update_data['meal_time'] == 'breakfast' ? 'selected' : ''; ?>>Breakfast</option>
                <option value="lunch" <?php echo isset($update_data['meal_time']) && $update_data['meal_time'] == 'lunch' ? 'selected' : ''; ?>>Lunch</option>
                <option value="dinner" <?php echo isset($update_data['meal_time']) && $update_data['meal_time'] == 'dinner' ? 'selected' : ''; ?>>Dinner</option>
            </select><br>
            <label for="update_meal_preference">Meal Preference:</label>
            <select id="update_meal_preference" name="meal_preference">
                <option value="vegan" <?php echo isset($update_data['meal_preference']) && $update_data['meal_preference'] == 'vegan' ? 'selected' : ''; ?>>Vegan</option>
                <option value="nonvegan" <?php echo isset($update_data['meal_preference']) && $update_data['meal_preference'] == 'nonvegan' ? 'selected' : ''; ?>>Non-Vegan</option>
            </select><br>
            <label for="update_nutrition_values">Nutrition Values (JSON format):</label>
            <textarea id="update_nutrition_values" name="nutrition_values" required><?php echo htmlspecialchars($update_data['nutrition_values'] ?? ''); ?></textarea><br>
            <label for="update_meal_image">Meal Image:</label>
            <input type="file" id="update_meal_image" name="meal_image" accept="image/*"><br><br>
            <label for="update_day">Day:</label>
            <select id="update_day" name="day">
                <option value="Monday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Monday' ? 'selected' : ''; ?>>Monday</option>
                <option value="Tuesday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                <option value="Wednesday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                <option value="Thursday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                <option value="Friday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Friday' ? 'selected' : ''; ?>>Friday</option>
                <option value="Saturday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Saturday' ? 'selected' : ''; ?>>Saturday</option>
                <option value="Sunday" <?php echo isset($update_data['day']) && $update_data['day'] == 'Sunday' ? 'selected' : ''; ?>>Sunday</option>
            </select><br><br>
            <input type="submit" name="update" value="Update Meal Plan">
        </form>
    </div>
</div>

<script>
    // No need for additional JavaScript as modal visibility is controlled by PHP
</script>

</body>
</html>

<?php
$conn->close();
?>
