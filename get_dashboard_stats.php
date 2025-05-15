<?php
// This tells the browser we are sending JSON back
header("Content-Type: application/json");

// This is where we tell the computer how to talk to the library database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Try to talk to the database
$conn = new mysqli($host, $username, $password, $dbname);

// If we can't talk to it, we show an error
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Connection failed"]);
  exit;
}

// This will keep all the numbers we count
$response = [];

// Count how many students there are
$students = $conn->query("SELECT COUNT(*) AS total FROM Users");
$response["totalStudents"] = $students->fetch_assoc()["total"];

// Count how many books there are
$books = $conn->query("SELECT COUNT(*) AS total FROM Books");
$response["totalBooks"] = $books->fetch_assoc()["total"];

// Count how many books are still borrowed
$borrowed = $conn->query("SELECT COUNT(*) AS total FROM Borrowings WHERE Status = 'borrowed'");
$response["borrowedBooks"] = $borrowed->fetch_assoc()["total"];

// Count how many borrowed books are overdue
$overdue = $conn->query("SELECT COUNT(*) AS total FROM Borrowings WHERE Status = 'borrowed' AND ReturnDate < CURDATE()");
$response["overdueBooks"] = $overdue->fetch_assoc()["total"];

// Add up how much money was collected from fines
$fines = $conn->query("SELECT SUM(PaidAmount) AS total FROM Transactions");
$response["finesCollected"] = number_format($fines->fetch_assoc()["total"] ?? 0, 2);

// We close the connection to the database
$conn->close();

// Show the results
echo json_encode($response);
?>
