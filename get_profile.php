<?php
// We start the session to remember who is logged in
session_start(); 
header("Content-Type: application/json");

// These are  database credentials
$host = "localhost";
$db = "LibraryDB";
$user = "root";
$pass = "Mthozami@2004";

// Try to connect to the database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}
// Start transaction
$conn->begin_transaction(); 

// If no one is logged in, we stop here
if (!isset($_SESSION["UserID"])) {
    echo json_encode([
        "success" => false,
        "error" => "User not logged in"
    ]);
    // Rollback on failure
    $conn->rollback(); 
    exit;
}

// Get the ID of the user who is logged in
$userId = $_SESSION["UserID"];

/* We ask the database for info about this user (like name, email, phone)
 We use a safe method here called "prepare", to stop hackers*/
$stmt = $conn->prepare("SELECT FullName, Email, PhoneNumber FROM Users WHERE UserID = ?");
// Make sure the ID is a number
$stmt->bind_param("i", $userId); 
$stmt->execute();
$result = $stmt->get_result();

// If we find the user, we show their info
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "FullName" => $user["FullName"],
        "Email" => $user["Email"],
        "PhoneNumber" => $user["PhoneNumber"]
    ]);
    // Commit if successful
    $conn->commit(); 
} else {
    echo json_encode([
        "success" => false,
        "error" => "User not found"
    ]);
    // Rollback if not found
    $conn->rollback(); 
}

// We are done talking to the database
$stmt->close();
$conn->close();
?>
