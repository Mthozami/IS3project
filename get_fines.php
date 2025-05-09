<?php
header("Content-Type: text/html");

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  echo "<tr><td colspan='8'>Database connection failed.</td></tr>";
  exit;
}

$sql = "
  SELECT 
    f.FineID,
    f.Amount,
    f.IsPaid,
    f.CreatedAt,
    b.BorrowingID,
    b.UserID,
    b.BookID,
    b.FullName,
    b.BookTitle
  FROM Fines f
  JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
  ORDER BY f.FineID DESC
";

$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
  echo "<tr><td colspan='8'>No fines found.</td></tr>";
  exit;
}

while ($row = $result->fetch_assoc()) {
  $paidStatus = $row["IsPaid"] ? "<span class='paid'>Paid</span>" : "<span class='unpaid'>Unpaid</span>";
  echo "<tr>";
  echo "<td>" . htmlspecialchars($row["FineID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["BorrowingID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["UserID"]) . "</td>";
  echo "<td>" . htmlspecialchars($row["BookID"]) . "</td>";
  echo "<td>R" . htmlspecialchars($row["Amount"]) . "</td>";
  echo "<td>" . $paidStatus . "</td>";
  echo "<td>" . htmlspecialchars($row["CreatedAt"]) . "</td>";
  echo "<td>
          <button class='pay-btn'>Mark as Paid</button>
          <button class='edit-btn'>Adjust</button>
          <button class='delete-btn'>Cancel</button>
        </td>";
  echo "</tr>";
}

$conn->close();
?>