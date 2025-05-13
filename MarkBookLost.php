<?php
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo "Database connection failed.";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["borrowingID"])) {
  $borrowingID = intval($_POST["borrowingID"]);

  // Check if already fined
  $check = $conn->prepare("SELECT FineID FROM Fines WHERE BorrowingID = ?");
  $check->bind_param("i", $borrowingID);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    echo "This borrowing already has a fine.";
    $check->close();
    exit;
  }
  $check->close();

  // Insert R600 fine
  $insert = $conn->prepare("INSERT INTO Fines (BorrowingID, Amount) VALUES (?, 600)");
  $insert->bind_param("i", $borrowingID);
  if ($insert->execute()) {
    echo "Fine of R600 added for lost/damaged book.";
  } else {
    echo "Failed to insert fine.";
  }
  $insert->close();
} else {
  http_response_code(400);
  echo "Invalid request.";
}

$conn->close();
