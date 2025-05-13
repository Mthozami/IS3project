<?php
session_start();

// Database credentials
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// DB connection
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

// Validate POST inputs
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die("Email or password cannot be empty.");
}

// Fetch user by email
$sql = "SELECT * FROM Users WHERE Email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $storedPassword = $user['Password'];
    $userRole = $user['Role'];

    // Secure password check
    if (password_verify($password, $storedPassword) || $password === $storedPassword) {
        // ✅ Store session data properly
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

$stmt->close();
$conn->close();
?>
