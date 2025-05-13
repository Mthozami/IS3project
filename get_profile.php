<?php
session_start();
header("Content-Type: application/json");

// Database configuration
$host = "localhost";
$db = "LibraryDB";
$user = "root";
$pass = "Mthozami@2004";

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION["UserID"])) {
    echo json_encode([
        "success" => false,
        "error" => "User not logged in"
    ]);
    exit;
}

$userId = $_SESSION["UserID"];

// Prepare and execute SQL statement
$stmt = $conn->prepare("SELECT FullName, Email, PhoneNumber FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Return user data
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "FullName" => $user["FullName"],
        "Email" => $user["Email"],
        "PhoneNumber" => $user["PhoneNumber"]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "User not found"
    ]);
}

$stmt->close();
$conn->close();
?>
