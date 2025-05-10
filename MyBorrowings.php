<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit;
}

$userEmail = $_SESSION['email'];

// Database connection
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// SQL Query: Join Borrowings with Books to fetch Book Title
$sql = "SELECT b.BorrowingID, bk.Title AS BookTitle, b.Quantity, b.BorrowDate, b.ReturnDate, b.Status
        FROM Borrowings b
        JOIN Books bk ON b.BookID = bk.BookID
        WHERE b.UserEmail = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

$rowsHtml = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rowsHtml .= "<tr>
            <td>{$row['BorrowingID']}</td>
            <td>{$row['BookTitle']}</td>
            <td>{$row['Quantity']}</td>
            <td>{$row['BorrowDate']}</td>
            <td>{$row['ReturnDate']}</td>
            <td>{$row['Status']}</td>
        </tr>";
    }
} else {
    $rowsHtml = "<tr><td colspan='6'>No borrowings found.</td></tr>";
}

$stmt->close();
$conn->close();

// Now, include the HTML layout
include('MyBorrowings.html');
?>