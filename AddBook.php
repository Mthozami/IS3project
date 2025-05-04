<?php
// add_book.php

$host = 'localhost';
$username = 'root';
$password = 'Mthozami@2004'; // replace with your DB password
$dbname = 'LibraryDB';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die('Connection Failed: ' . $conn->connect_error);
}

$title = $_POST['title'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$isbn = $_POST['isbn'] ?? '';

if (!empty($title) && !empty($quantity) && !empty($isbn)) {
  $stmt = $conn->prepare("INSERT INTO Books (Title, Quantity, ISBN) VALUES (?, ?, ?)");
  $stmt->bind_param("sis", $title, $quantity, $isbn);

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
