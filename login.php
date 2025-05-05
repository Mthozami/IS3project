<?php
session_start();

// Database credentials
$host = "localhost";
$username = "root";
$password = "Mzamoh@25";
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
    $storedPassword = $user['Password']; // Note capital "P"

    // Validate hashed password
    if (password_verify($password, $storedPassword)) {
        $_SESSION['email'] = $user['Email'];
        echo "<script>alert('Login successful!'); window.location.href='AdminDashBoard.html';</script>";
    }
    // Legacy fallback
    elseif ($password === $storedPassword) {
        $_SESSION['email'] = $user['Email'];
        echo "<script>alert('Login successful (legacy)!'); window.location.href='AdminDashBoard.html';</script>";
    } else {
        echo "<script>alert('Incorrect password!'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('User not found!'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
