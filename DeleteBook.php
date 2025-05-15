<?php
// This file deletes a book from the library

// Connect to the library database
$host = 'localhost';
$username = 'root';
$password = 'Mthozami@2004';
$dbname = 'LibraryDB';

$conn = new mysqli($host, $username, $password, $dbname);

// Show error if connection doesn't work
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if we were given a book ID
if (isset($_GET['BookID'])) {
    // Turn input into number to stay safe
    $bookID = intval($_GET['BookID']); 

    // Begin transaction
    $conn->begin_transaction(); // Start transaction

    // Prepare a safe delete query
    $stmt = $conn->prepare("DELETE FROM Books WHERE BookID = ?");
    // i means integer
    $stmt->bind_param("i", $bookID); 

    // Try to delete the book
    if ($stmt->execute()) {
        // Commit if successful
        $conn->commit(); 
        header("Location: AddBook.html");
        exit;
    } else {
        // Rollback if failed
        $conn->rollback(); 
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request!";
}

$conn->close();
?>
