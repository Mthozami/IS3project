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
$conn->begin_transaction(); // Start transaction

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
