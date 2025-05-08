<?php
// DB connection setup
$servername = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Database connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $bookId     = $_POST["bookId"]     ?? "";
  $bookTitle  = $_POST["bookTitle"]  ?? "";
  $userId     = $_POST["userId"]     ?? "";
  $fullName   = $_POST["fullName"]   ?? "";
  $quantity   = $_POST["quantity"]   ?? 1;
  $borrowDate = $_POST["borrowDate"] ?? "";
  $returnDate = $_POST["returnDate"] ?? "";

  if (empty($bookId) || empty($bookTitle) || empty($userId) || empty($fullName) || empty($borrowDate) || empty($returnDate)) {
    die("Missing required fields.");
  }

  // Check book availability
  $checkQuery = "SELECT Quantity FROM books WHERE BookID = ?";
  $stmt = $conn->prepare($checkQuery);
  $stmt->bind_param("i", $bookId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    die("Book not found.");
  }

  $row = $result->fetch_assoc();
  if ((int)$row["Quantity"] < (int)$quantity) {
    die("Not enough copies available.");
  }

  // Insert borrowing record
  $insertQuery = "INSERT INTO borrowings (BookTitle, BookID, FullName, UserID, Quantity, BorrowedDate, ReturnDate, Status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'borrowed')";
  $stmt = $conn->prepare($insertQuery);
  $stmt->bind_param("sisisss", $bookTitle, $bookId, $fullName, $userId, $quantity, $borrowDate, $returnDate);

  if ($stmt->execute()) {
    // Reduce stock in Books table
    $updateQuery = "UPDATE books SET Quantity = Quantity - ? WHERE BookID = ?";
    $stmt2 = $conn->prepare($updateQuery);
    $stmt2->bind_param("ii", $quantity, $bookId);
    $stmt2->execute();

    echo "Borrowing recorded successfully.";
  } else {
    echo "Failed to record borrowing.";
  }
} else {
  echo "Invalid request method.";
}

$conn->close();
?>
