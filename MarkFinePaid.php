<?php
// mark_fine_paid.php

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed: " . $conn->connect_error;
    exit;
}

// Validate input
if (!isset($_POST['FineID']) || !is_numeric($_POST['FineID'])) {
    http_response_code(400);
    echo "Invalid Fine ID.";
    exit;
}

$fineID = intval($_POST['FineID']);

// Update the fine to mark it as paid and update the timestamp
$sql = "UPDATE Fines SET IsPaid = 1, CreatedAt = NOW() WHERE FineID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Database error: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $fineID);
if ($stmt->execute()) {
    echo "Fine marked as paid successfully.";
} else {
    http_response_code(500);
    echo "Failed to mark fine as paid.";
}

$stmt->close();
$conn->close();
