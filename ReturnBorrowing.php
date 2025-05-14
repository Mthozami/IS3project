<?php
// Tell the browser this is just plain text
header("Content-Type: text/plain");

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  echo "Database connection failed.";
  exit;
}

// Make sure the user used POST to send the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Get the numbers from the form safely
  $borrowingId = isset($_POST["borrowingId"]) ? intval($_POST["borrowingId"]) : 0;
  $bookId = isset($_POST["bookId"]) ? intval($_POST["bookId"]) : 0;
  $quantity = isset($_POST["quantity"]) ? intval($_POST["quantity"]) : 0;

  // Make sure none of them are missing or wrong
  if ($borrowingId <= 0 || $bookId <= 0 || $quantity <= 0) {
    echo "Missing or invalid required fields.";
    exit;
  }

  // Step 1: Check if this borrowing already exists and hasn’t been returned
  $checkStmt = $conn->prepare("SELECT Status FROM Borrowings WHERE BorrowingID = ?");
  $checkStmt->bind_param("i", $borrowingId);
  $checkStmt->execute();
  $result = $checkStmt->get_result();

  // If no record found
  if ($result === false || $result->num_rows === 0) {
    echo "Borrowing record not found.";
    exit;
  }

  $row = $result->fetch_assoc();
  if (strtolower($row["Status"]) === "returned") {
    echo "This book has already been marked as returned.";
    exit;
  }

  // Step 2: Mark the book as returned
  $updateStmt = $conn->prepare("UPDATE Borrowings SET Status = 'returned' WHERE BorrowingID = ?");
  $updateStmt->bind_param("i", $borrowingId);
  if (!$updateStmt->execute()) {
    echo "Failed to update borrowing status.";
    exit;
  }

  // Step 3: Add the book back to the stock
  $bookUpdateStmt = $conn->prepare("UPDATE Books SET Quantity = Quantity + ? WHERE BookID = ?");
  $bookUpdateStmt->bind_param("ii", $quantity, $bookId);
  if (!$bookUpdateStmt->execute()) {
    echo "Failed to update book quantity.";
    exit;
  }

  echo "Book returned successfully.";
} else {
  echo "Invalid request.";
}

// Close the connection
$conn->close();
?>
