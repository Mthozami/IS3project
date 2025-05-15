<?php
// Set content type to HTML for table rows output
header("Content-Type: text/html");

// Establish connection to MySQL database
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");

// If connection fails, output an error row in HTML
if ($conn->connect_error) {
  echo "<tr><td colspan='7'>Connection failed.</td></tr>";
  exit;
}

// Begin transaction
$conn->begin_transaction();

// SQL query to fetch transactions joined with fines, borrowings, users, and books
$sql = "
  SELECT 
    t.TransactionID,
    t.FineID,
    t.ReceiptNumber,
    t.PaymentDate,
    t.PaidAmount,
    u.FullName,
    bk.Title AS BookTitle
  FROM Transactions t
  JOIN Fines f ON t.FineID = f.FineID
  JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
  JOIN Users u ON b.UserID = u.UserID
  JOIN Books bk ON b.BookID = bk.BookID
  ORDER BY t.PaymentDate ASC
";

// Execute the query
$result = $conn->query($sql);

// If no results, display a message row
if (!$result || $result->num_rows === 0) {
  echo "<tr><td colspan='7'>No transactions found.</td></tr>";
  $conn->commit(); // Commit transaction
  exit;
}

// Loop through each result row
while ($row = $result->fetch_assoc()) {
  echo "<tr>";
  // Escape and display each field to prevent XSS
  echo "<td>" . htmlspecialchars($row["TransactionID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["FineID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["FullName"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["BookTitle"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["ReceiptNumber"]) . "</td>";
  echo "<td>R" . number_format($row["PaidAmount"], 2) . "</td>";
  echo "<td>" . htmlspecialchars($row["PaymentDate"]) . "</td>";
  echo "</tr>";
}

// Commit transaction
$conn->commit();

// Close DB connection
$conn->close();
?>