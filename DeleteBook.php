<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = 'Mthozami@2004'; 
$dbname = 'LibraryDB';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if BookID is received correctly via POST
if (isset($_POST['BookID'])) {
    $book_id = intval($_POST['BookID']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM Books WHERE BookID = ?");
    $stmt->bind_param("i", $book_id);

    // Execute and return response
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
} else {
    echo "invalid";
}

$conn->close();
?>
