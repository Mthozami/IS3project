<?php
// DeleteBook.php

$host = 'localhost';
$username = 'root';
$password = 'Mzamoh@25';
$dbname = 'LibraryDB';

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if BookID is received
if (isset($_POST['BookID'])) {
    $bookID = intval($_POST['BookID']);

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM Books WHERE BookID = ?");
    $stmt->bind_param("i", $bookID);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "invalid";
}

$conn->close();
?>
