<?php
// AddBook.php

$host = 'localhost';
$username = 'root';
$password = 'Mzamoh@25';
$dbname = 'LibraryDB';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['title'], $_POST['quantity'], $_POST['isbn'])) {
    $title = $_POST['title'];
    $quantity = intval($_POST['quantity']);
    $isbn = $_POST['isbn'];

    $stmt = $conn->prepare("INSERT INTO Books (Title, Quantity, ISBN) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $title, $quantity, $isbn);

    if ($stmt->execute()) {
        // Redirect with success query parameter
        header("Location: AddBook.html?success=true");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid input!";
}

$conn->close();
?>
