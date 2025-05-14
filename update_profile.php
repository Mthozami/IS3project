<?php
// Start the session so we know who the user is
session_start();

// These are the details needed to connect to the database
$host = "localhost";
$db = "LibraryDB";
$user = "root";
$pass = "Mthozami@2004";

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);

// If the connection fails, stop and show an error
if ($conn->connect_error) {
    http_response_code(500); // This means "server error"
    exit("Database connection failed."); // Show this message
}

// If the user is not logged in, stop and show an error
if (!isset($_SESSION["UserID"])) {
    http_response_code(403); // This means "not allowed"
    exit("You are not logged in.");
}

// Get the user's ID from the session
$userId = $_SESSION["UserID"];

// Get the values from the form, or use empty if missing
$fullName = $_POST["FullName"] ?? '';
$email = $_POST["Email"] ?? '';
$phone = $_POST["PhoneNumber"] ?? '';
$currentPassword = $_POST["CurrentPassword"] ?? '';
$newPassword = $_POST["Password"] ?? '';
$confirmPassword = $_POST["ConfirmPassword"] ?? '';

// If name, email, or phone is missing, stop and show error
if (!$fullName || !$email || !$phone) {
    http_response_code(400); // This means "bad request"
    exit("Please fill in all required fields.");
}

// If the user typed a new password, we need to check more
if (!empty($newPassword)) {

    // Check if the new password and the confirm password match
    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        exit("New password and confirmation do not match.");
    }

    // Check if the user typed their current password
    if (empty($currentPassword)) {
        http_response_code(400);
        exit("Current password is required to change password.");
    }

    // Look up the user's old password in the database
    $check = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
    $check->bind_param("i", $userId); // "i" means integer
    $check->execute();
    $result = $check->get_result();
    $user = $result->fetch_assoc();
    $check->close();

    // If we didn't find a user or the password is wrong, stop
    if (!$user || !password_verify($currentPassword, $user["Password"])) {
        http_response_code(401); // This means "unauthorized"
        exit("Current password is incorrect.");
    }

    // Make the new password safe by hashing it
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update everything including the new password
    $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Password = ? WHERE UserID = ?");
    $stmt->bind_param("ssssi", $fullName, $email, $phone, $hashedPassword, $userId); // "s" means string
} else {
    // If no new password, update only name, email, and phone
    $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ? WHERE UserID = ?");
    $stmt->bind_param("sssi", $fullName, $email, $phone, $userId);
}

// Try to update the user's info
if ($stmt->execute()) {
    echo "Profile updated successfully!"; // Tell the user it worked
} else {
    http_response_code(500);
    echo "Failed to update profile: " . $conn->error; // Show the error if it failed
}

// Close the statement and connection to clean up
$stmt->close();
$conn->close();
?>
