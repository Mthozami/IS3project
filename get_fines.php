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
  WHERE f.IsPaid = 0
  ORDER BY f.FineID ASC
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<tr><td colspan='8'>No unpaid fines found.</td></tr>";
    exit;
}

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["FineID"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["BorrowingID"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["FullName"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["BookTitle"]) . "</td>";
    echo "<td>R" . htmlspecialchars($row["Amount"]) . "</td>";
    echo "<td class='unpaid'>Unpaid</td>";
    echo "<td>" . htmlspecialchars($row["CreatedAt"]) . "</td>";
    echo "<td>
            <button class='pay-btn' data-fine-id='" . $row["FineID"] . "'>Mark as Paid</button>
            <button class='edit-btn'>Adjust</button>
            <button class='delete-btn'>Cancel</button>
          </td>";
    echo "</tr>";
}

$conn->close();
?>
