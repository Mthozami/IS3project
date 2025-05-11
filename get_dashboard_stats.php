<?php
header("Content-Type: application/json");

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Connection failed"]);
  exit;
}

$response = [];

// Total Students
$students = $conn->query("SELECT COUNT(*) AS total FROM Users");
$response["totalStudents"] = $students->fetch_assoc()["total"];

// Total Books
$books = $conn->query("SELECT COUNT(*) AS total FROM Books");
$response["totalBooks"] = $books->fetch_assoc()["total"];

// Books Currently Borrowed
$borrowed = $conn->query("SELECT COUNT(*) AS total FROM Borrowings WHERE Status = 'borrowed'");
$response["borrowedBooks"] = $borrowed->fetch_assoc()["total"];

// Overdue Books (ReturnDate < today)
$overdue = $conn->query("SELECT COUNT(*) AS total FROM Borrowings WHERE Status = 'borrowed' AND ReturnDate < CURDATE()");
$response["overdueBooks"] = $overdue->fetch_assoc()["total"];

// Fines Collected from Transactions
$fines = $conn->query("SELECT SUM(PaidAmount) AS total FROM Transactions");
$response["finesCollected"] = number_format($fines->fetch_assoc()["total"] ?? 0, 2);

$conn->close();
echo json_encode($response);
?>
