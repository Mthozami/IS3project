<?php
// I am checking if I got a UserID from the web link
if (!isset($_GET['UserID'])) {
    die("User ID not provided.");
}

$userId = $_GET['UserID'];

// I connect to my MySQL database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
// If the connection is broken, I display an error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Begin transaction
$conn->begin_transaction();

// I want to find the user in my database, so I use a ? to be safe
$sql = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($sql);
// Using parameter binding to make it Safe from SQL injection!
$stmt->bind_param("i", $userId); 
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Rollback if not found
    $conn->rollback(); 
    die("User not found.");
}

// Commit if successful
$conn->commit(); 
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Edit User Information</h2>
    <form action="update_user.php" method="POST">
        <input type="hidden" name="UserID" value="<?php echo htmlspecialchars($user['UserID']); ?>">
        
        <div class="form-group">
            <label for="fullName">Full Name:</label>
            <input type="text" id="fullName" name="fullName" 
                   value="<?php echo htmlspecialchars($user['FullName']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="Borrower" <?php echo ($user['Role'] == 'Borrower') ? 'selected' : ''; ?>>Borrower</option>
                <option value="Admin" <?php echo ($user['Role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <button type="submit">Update User</button>
    </form>
</body>
</html>
