<?php
header("Content-Type: text/html");

$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");
if ($conn->connect_error) {
  echo "<tr><td colspan='8'>Database connection failed.</td></tr>";
  exit;
}

$sql = "
  SELECT 
    f.FineID, f.Amount, f.IsPaid, f.CreatedAt,
    b.BorrowingID, b.FullName, b.BookTitle
  FROM Fines f
  JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
  ORDER BY f.FineID ASC
";

$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
  echo "<tr><td colspan='8'>No fines found.</td></tr>";
  exit;
}

while ($row = $result->fetch_assoc()) {
  $status = $row["IsPaid"] ? "<span class='paid'>Paid</span>" : "<span class='unpaid'>Unpaid</span>";
  $actions = $row["IsPaid"] ? "-" : "
    <button class='pay-btn' data-fine-id='{$row["FineID"]}'>Mark as Paid</button>
    <button class='edit-btn' data-fine-id='{$row["FineID"]}'>Adjust</button>

  ";
  echo "<tr>
          <td>{$row["FineID"]}</td>
          <td>{$row["BorrowingID"]}</td>
          <td>{$row["FullName"]}</td>
          <td>{$row["BookTitle"]}</td>
          <td>R" . number_format($row["Amount"], 2) . "</td>
          <td>$status</td>
          <td>{$row["CreatedAt"]}</td>
          <td>$actions</td>
        </tr>";
}

$conn->close();
?>
