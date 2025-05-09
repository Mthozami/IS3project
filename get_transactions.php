<?php
// get_transactions.php
header("Content-Type: text/html");

$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");
if ($conn->connect_error) {
  echo "<tr><td colspan='7'>Connection failed.</td></tr>";
  exit;
}

$sql = "
  SELECT 
    t.TransactionID,
    t.FineID,
    t.ReceiptNumber,
    t.PaymentDate,
    t.PaidAmount,
    b.FullName,
    b.BookTitle
  FROM Transactions t
  JOIN Fines f ON t.FineID = f.FineID
  JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
  ORDER BY t.PaymentDate DESC
";

$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
  echo "<tr><td colspan='7'>No transactions found.</td></tr>";
  exit;
}

while ($row = $result->fetch_assoc()) {
  echo "<tr>";
  echo "<td>" . htmlspecialchars($row["TransactionID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["FineID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["FullName"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["BookTitle"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["ReceiptNumber"]) . "</td>";
  echo "<td>R" . htmlspecialchars($row["PaidAmount"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["PaymentDate"]) . "</td>";
  echo "</tr>";
}

$conn->close();
?>
