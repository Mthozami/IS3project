<?php
header("Content-Type: application/json");

// DB connection setup
$servername = "localhost";
$username = "root";
$password = "Mzamoh@25";
$dbname = "LibraryDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Database connection failed."]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $bookId = $_POST["bookId"] ?? "";
  $userId = $_POST["userId"] ?? "";
  $quantity = $_POST["quantity"] ?? 1;
  $borrowDate = $_POST["borrowDate"] ?? "";
  $returnDate = $_POST["returnDate"] ?? "";

  if (empty($bookId) || empty($userId) || empty($borrowDate) || empty($returnDate)) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
  }

  // Check book availability
  $checkQuery = "SELECT Quantity FROM books WHERE BookID = ?";
  $stmt = $conn->prepare($checkQuery);
  $stmt->bind_param("i", $bookId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Book not found."]);
    exit;
  }

  $row = $result->fetch_assoc();
  if ((int)$row["Quantity"] < (int)$quantity) {
    echo json_encode(["success" => false, "message" => "Not enough copies available."]);
    exit;
  }

  // Insert borrowing
  $insertQuery = "INSERT INTO borrowings (BookID, UserID, Quantity, BorrowedDate, ReturnDate, Status)
                  VALUES (?, ?, ?, ?, ?, 'borrowed')";
  $stmt = $conn->prepare($insertQuery);
  $stmt->bind_param("iiiss", $bookId, $userId, $quantity, $borrowDate, $returnDate);

  if ($stmt->execute()) {
    // Update stock
    $updateQuery = "UPDATE books SET Quantity = Quantity - ? WHERE BookID = ?";
    $stmt2 = $conn->prepare($updateQuery);
    $stmt2->bind_param("ii", $quantity, $bookId);
    $stmt2->execute();

    // Return new borrowing info
    $borrowingId = $conn->insert_id;
    echo json_encode([
      "success" => true,
      "message" => "Borrowing recorded successfully.",
      "data" => [
        "BorrowingID" => $borrowingId,
        "BookID" => $bookId,
        "UserID" => $userId,
        "Quantity" => $quantity,
        "BorrowedDate" => $borrowDate,
        "ReturnDate" => $returnDate,
        "Status" => "borrowed"
      ]
    ]);
  } else {
    echo json_encode(["success" => false, "message" => "Failed to record borrowing."]);
  }
} else {
  echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
