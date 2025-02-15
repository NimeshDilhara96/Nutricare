<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Nutricare";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get user count
function getUserCount($conn) {
    $sql = "SELECT COUNT(*) AS count FROM User";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get consultation count
function getConsultationCount($conn) {
    $sql = "SELECT COUNT(*) AS count FROM consultation";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Search functionality
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Function to fetch users
function fetchUsers($conn, $searchQuery) {
    $sql = "SELECT * FROM User WHERE email LIKE '%$searchQuery%' OR user_id LIKE '%$searchQuery%'";
    return $conn->query($sql);
}

// Block user
if (isset($_POST['block_user'])) {
    $userId = $_POST['user_id'];
    $sql = "UPDATE User SET blocked = 1 WHERE user_id = $userId";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>User blocked successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error blocking user: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 20px; }
        .profile-img { width: 50px; height: 50px; object-fit: cover; }
        .alert { margin-top: 20px; }
        .table th, .table td { text-align: center; vertical-align: middle; padding: 5px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Admin Dashboard</h1>
        
        <form method="post" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search by email or ID" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="row mb-4">
            <div class="col-md-6">
                <h3>System Counts</h3>
                <ul class="list-group">
                    <li class="list-group-item">User Count: <strong><?php echo getUserCount($conn); ?></strong></li>
                    <li class="list-group-item">Consultation Count: <strong><?php echo getConsultationCount($conn); ?></strong></li>
                </ul>
            </div>
        </div>

        <h3>Users</h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Height</th>
                    <th>Weight</th>
                    <th>Diabetic</th>
                    <th>High BP</th>
                    <th>Low BP</th>
                    <th>Preferences</th>
                    <th>Profile</th>
                    <th>Report</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = fetchUsers($conn, $searchQuery);
                if ($users->num_rows > 0) {
                    while($row = $users->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['user_id']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td>{$row['fname']} {$row['lname']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['phonenumber']}</td>";
                        echo "<td>{$row['age']}</td>";
                        echo "<td>{$row['height']}</td>";
                        echo "<td>{$row['weight']}</td>";
                        echo "<td>" . ($row['diabetic'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>" . ($row['high_blood_pressure'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>" . ($row['low_blood_pressure'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>{$row['diet_preferences']}</td>";
                        echo "<td><img src='{$row['profile_picture']}' alt='Profile Picture' class='profile-img'></td>";
                        echo "<td><a href='download_report.php?user_id={$row['user_id']}' class='btn btn-info btn-sm'>Download</a></td>";
                        echo "<td>
                                <form method='post' style='display:inline;'>
                                    <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                    <button type='submit' name='block_user' class='btn btn-danger btn-sm'>Block</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='15' class='text-center'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
