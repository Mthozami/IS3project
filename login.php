<?php
// Start the session
session_start();

// DB connection variables
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Handle DB connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only accept POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

// Read email and password from POST request and // trim whitespace
$email = trim($_POST['email'] ?? ''); 
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($email) || empty($password)) {
    die("Email or password cannot be empty.");
}

// Step 1: Use prepared statement to fetch user by email safe
$sql = "SELECT * FROM Users WHERE Email = ?";
$stmt = $conn->prepare($sql);

// Handle prepare failure
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind email string
$stmt->bind_param("s", $email); 
// Run query
$stmt->execute(); 
// Get result set
$result = $stmt->get_result(); 

// Step 2: If user found
if ($result->num_rows === 1) {
    // Get user record
    $user = $result->fetch_assoc(); 
    // Stored password hash or raw password
    $storedPassword = $user['Password']; 
    // Get user role
    $userRole = $user['Role']; 

    // Check password (supports both hashed and plain text)
    if (password_verify($password, $storedPassword) || $password === $storedPassword) {
        // Store login session info
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Email'] = $user['Email'];
        $_SESSION['Role'] = $userRole;

        // Redirect based on role
        if ($userRole === 'Admin') {
            echo "<script>alert('Admin login successful!'); window.location.href='AdminDashboard.html';</script>";
        } else {
            echo "<script>alert('User login successful!'); window.location.href='BorrowerDashboard.html';</script>";
        }
    } else {
        echo "<script>alert('Incorrect password!'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('User not found!'); window.location.href='login.html';</script>";
}
// Close prepared statement
$stmt->close(); 
// Close DB connection
$conn->close(); 
?>
