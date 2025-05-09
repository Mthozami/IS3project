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

// Get overdue borrowings
$overdueQuery = "
  SELECT b.BorrowingID, b.ReturnDate
  FROM Borrowings b
  LEFT JOIN Fines f ON b.BorrowingID = f.BorrowingID AND f.IsPaid = 0
  WHERE b.Status = 'borrowed' AND b.ReturnDate < CURDATE()
";

$overdueResult = $conn->query($overdueQuery);
if ($overdueResult && $overdueResult->num_rows > 0) {
  while ($row = $overdueResult->fetch_assoc()) {
    $borrowingID = $row["BorrowingID"];
    $returnDate = new DateTime($row["ReturnDate"]);
    $today = new DateTime();
    $daysOverdue = $returnDate->diff($today)->days;
    $amount = 25 * $daysOverdue;

    $check = $conn->query("SELECT FineID FROM Fines WHERE BorrowingID = $borrowingID AND IsPaid = 0");
    if ($check->num_rows > 0) {
      $conn->query("UPDATE Fines SET Amount = $amount WHERE BorrowingID = $borrowingID AND IsPaid = 0");
    } else {
      $conn->query("INSERT INTO Fines (BorrowingID, Amount) VALUES ($borrowingID, $amount)");
    }
  }
}

$conn->close();
echo "Fines updated.";
?>