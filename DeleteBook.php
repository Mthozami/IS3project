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

// Check if BookID is received via GET
if (isset($_GET['BookID'])) {
    $bookID = intval($_GET['BookID']); // Sanitize the input to avoid SQL injection

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM Books WHERE BookID = ?");
    $stmt->bind_param("i", $bookID);

    if ($stmt->execute()) {
        // Redirect to the Book Management page after deletion
        header("Location: AddBook.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request!";
}

$conn->close();
?>
