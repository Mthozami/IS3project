<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookID = $_POST['bookID'] ?? '';
    $title = $_POST['title'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $isbn = $_POST['isbn'] ?? '';

    if ($bookID && $title && $quantity && $isbn) {
        $stmt = $conn->prepare("UPDATE books SET title = ?, quantity = ?, isbn = ? WHERE id = ?");
        $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookID);

        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    } else {
        echo "error";
    }
    $conn->close();
}
?>
