<?php
session_start();

$host = "localhost";
$db = "LibraryDB";
$user = "root";
$pass = "Mthozami@2004";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed.");
}

if (!isset($_SESSION["UserID"])) {
    http_response_code(403);
    exit("You are not logged in.");
}

$userId = $_SESSION["UserID"];
$fullName = $_POST["FullName"] ?? '';
$email = $_POST["Email"] ?? '';
$phone = $_POST["PhoneNumber"] ?? '';
$password = $_POST["Password"] ?? '';

// Basic validation
if (!$fullName || !$email || !$phone) {
    http_response_code(400);
    exit("Please fill in all required fields.");
}

// Check if password was provided
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Password = ? WHERE UserID = ?");
    $stmt->bind_param("ssssi", $fullName, $email, $phone, $hashedPassword, $userId);
} else {
    $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ? WHERE UserID = ?");
    $stmt->bind_param("sssi", $fullName, $email, $phone, $userId);
}

if ($stmt->execute()) {
    echo "Profile updated successfully!";
} else {
    http_response_code(500);
    echo "Failed to update profile: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
