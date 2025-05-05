<?php
$host = 'localhost';
$username = 'root';
$password = '@2belihlerh'; 
$dbname = 'LibraryDB';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['bookId'], $_POST['title'], $_POST['quantity'], $_POST['isbn'])) {
    $bookId = intval($_POST['bookId']);
    $title = $conn->real_escape_string($_POST['title']);
    $quantity = intval($_POST['quantity']);
    $isbn = $conn->real_escape_string($_POST['isbn']);

    $stmt = $conn->prepare("UPDATE Books SET Title = ?, Quantity = ?, ISBN = ? WHERE BookID = ?");
    $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookId);

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
